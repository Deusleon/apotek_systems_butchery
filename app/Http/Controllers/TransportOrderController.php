<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\TransportOrder;
use App\Transporter;
use App\Location;
use App\Product;
use App\Supplier;
use Illuminate\Support\Str;
use App\Store;
use App\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Payment;
use Illuminate\Support\Facades\Log;

class TransportOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     public function index()
    {
        $transportOrders = TransportOrder::with(['transporter', 'assignedVehicle', 'payments'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        $transporters = Transporter::where('status', 'active')->get();
        $vehicles = Vehicle::where('status', 'active')->get();
        $products = Product::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();
        $stores = Store::orderBy('name')->get();

        return view('transport-orders.index', compact(
            'transportOrders',
            'transporters',
            'vehicles',
            'products',
            'suppliers',
            'stores'
        ));
    }
    // public function index()
    // {
    //     $transportOrders = TransportOrder::with(['transporter', 'assignedVehicle'])
    //         ->orderBy('created_at', 'desc')
    //         ->get();

    //     return view('transport-orders.index', compact('transportOrders'));
    // }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */





     public function create()
     {
         $transporters = Transporter::all();
         $vehicles = Vehicle::all();
         $products = Product::orderBy('name')->get();
         $suppliers = Supplier::orderBy('name')->get(); // pickup
         $stores = Store::orderBy('name')->get(); // delivery
     
         // Prepare simple arrays for JS usage in Blade, no arrow functions in Blade
         $suppliersArray = $suppliers->map(function ($s) {
             return ['name' => $s->name];
         })->toArray();
     
         $storesArray = $stores->map(function ($s) {
             return ['name' => $s->name];
         })->toArray();
     
         $data = [
             'transporters' => $transporters,
             'vehicles' => $vehicles,
             'products' => $products,
             'suppliers' => $suppliers,
             'stores' => $stores,
             'suppliersArray' => $suppliersArray,   // Pass this for Blade JS
             'storesArray' => $storesArray,         // Pass this for Blade JS
             'unitOptions' => TransportOrder::unitOptions(),
             'priorityOptions' => TransportOrder::priorityOptions(),
             'paymentMethods' => TransportOrder::paymentMethods(),
             'statusOptions' => TransportOrder::statusOptions()
         ];
     
         return view('transport-orders.create', $data);
     }
     


    //     public function create()
    // {
    //     $transporters = Transporter::all();
    //     $vehicles = Vehicle::all();

    //     // Pass all the static options from TransportOrder to the view
    //     $data = [
    //         'transporters' => $transporters,
    //         'vehicles' => $vehicles,
    //         'pickupLocations' => TransportOrder::pickupLocations(),
    //         'deliveryLocations' => TransportOrder::deliveryLocations(),
    //         'productOptions' => TransportOrder::productOptions(),
    //         'unitOptions' => TransportOrder::unitOptions(),
    //         'priorityOptions' => TransportOrder::priorityOptions(),
    //         'paymentMethods' => TransportOrder::paymentMethods(),
    //         'statusOptions' => TransportOrder::statusOptions()
    //     ];

    //     return view('transport-orders.create', $data);
    // }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */


public function store(Request $request)
{
    $request->validate([
        'order_type' => 'required|in:factory,inter_branch',
        'pickup_date' => 'required|date',
        'delivery_date' => 'required|date|after:pickup_date',
        'priority' => 'required|in:normal,urgent,very_urgent',
        'transport_rate' => 'required|numeric|min:0',
        'advance_payment' => 'nullable|numeric|min:0|lte:transport_rate',
        'payment_method' => 'nullable|in:cash,bank_transfer,mobile_money,cheque',
        'status' => 'required|in:draft,confirmed,dispatched,in_transit,delivered,closed',
        'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,xlsx,xls,zip',
        'notes' => 'nullable|string|max:500',

        'transporters' => 'required|array|min:1',
        'products' => 'required|array',
        'quantities' => 'required|array',
        'units' => 'required|array',
        'deliveries' => 'required|array',
    ]);

    // Save attachments (if any)
    $attachments = [];
    if ($request->hasFile('attachments')) {
        foreach ($request->file('attachments') as $file) {
            $attachments[] = $file->store('transport_attachments', 'public');
        }
    }

    // Generate shared order number
    $latestOrder = TransportOrder::latest()->first();
    $orderNumber = 'TR-' . str_pad($latestOrder ? $latestOrder->id + 1 : 1, 6, "0", STR_PAD_LEFT);

    // Loop through items
    foreach ($request->products as $i => $product) {
        // Decide pickup location based on order_type and index
        $pickupLocation = null;
        $orderNumberWithIndex = $orderNumber . '-' . ($i + 1);

        if ($request->order_type === 'factory') {
            $pickupLocation = $request->pickup_suppliers[$i] ?? null;
        } elseif ($request->order_type === 'inter_branch') {
            $pickupLocation = $request->pickup_stores[$i] ?? null;
        }

        // Fail-safe: if pickup location is still null, skip or throw error
        if (!$pickupLocation) {
            continue; // Or throw validation error
        }

        // Prepare data for logging and creation
        $orderData = [
            'order_type'          => $request->order_type,
            'order_number'        => $orderNumberWithIndex,
            'transporter_id'      => $request->transporters[$i] ?? null,
            'assigned_vehicle_id' => $request->vehicles[$i] ?? null,
            'product'             => $product,
            'quantity'            => $request->quantities[$i],
            'unit'                => $request->units[$i],
            'pickup_location'     => $pickupLocation,
            'delivery_location'   => $request->deliveries[$i],
            'pickup_date'         => Carbon::parse($request->pickup_date),
            'delivery_date'       => Carbon::parse($request->delivery_date),
            'priority'            => $request->priority,
            'transport_rate'      => $request->transport_rate,
            'advance_payment'     => $request->advance_payment,
            'balance_due'         => $request->transport_rate - ($request->advance_payment ?? 0),
            'payment_method'      => $request->payment_method,
            'status'              => $request->status,
            'notes'               => $request->notes,
            'attachments'         => json_encode($attachments),
            'created_by'          => auth()->id(),
        ];

        // Log the data before creating the order
        Log::info('Attempting to create Transport Order with the following data:', $orderData);

        // Create the transport order
        $transportOrder = TransportOrder::create($orderData);

        if ($transportOrder && $transportOrder->advance_payment > 0) {
            Payment::create([
                'transport_order_id' => $transportOrder->id,
                'amount' => $transportOrder->advance_payment,
                'payment_type' => 'advance',
                'payment_method' => $transportOrder->payment_method,
                'status' => 'completed',
                'user_id' => auth()->id(),
                'receipt_number' =>$transportOrder->order_number,
                'payment_date' => Carbon::now()
            ]);
        }

        if($transportOrder) {
            $transportOrder->updatePaymentStatus();
        }
    }

    session()->flash("alert-success", "Transport order created successfully!");
    return redirect()->route('transport-orders.index');
}


public function paymentsIndex()
{
    $transportOrders = TransportOrder::with('transporter')->orderBy('created_at', 'desc')->get();
    return view('transport-orders.payments', compact('transportOrders'));
}


    //  public function store(Request $request)
    //  {
    //      // Validate fixed fields (not arrays)
    //      $request->validate([
    //          'order_type' => 'required|in:factory,inter_branch',
    //          'pickup_date' => 'required|date',
    //          'delivery_date' => 'required|date|after:pickup_date',
    //          'priority' => 'required|in:normal,urgent,very_urgent',
    //          'transport_rate' => 'required|numeric|min:0',
    //          'advance_payment' => 'nullable|numeric|min:0',
    //          'payment_method' => 'nullable|in:cash,bank_transfer,mobile_money,cheque',
    //          'status' => 'required|in:draft,confirmed,dispatched,in_transit,delivered,closed',
    //          'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,xlsx,xls,zip',
    //          'notes' => 'nullable|string|max:500',
    //      ]);
     
    //      // Validate arrays
    //      $request->validate([
    //          'transporters' => 'required|array|min:1',
    //          'vehicles' => 'array',
    //          'products' => 'required|array',
    //          'quantities' => 'required|array',
    //          'units' => 'required|array',
    //          'deliveries' => 'required|array',
    //      ]);
     
    //      // Determine pickup locations
    //      $pickup_locations = $request->order_type === 'factory'
    //          ? $request->pickup_suppliers
    //          : $request->pickup_stores;
     
    //      // Generate common order number
    //      $latestOrder = TransportOrder::latest()->first();
    //      $orderNumber = 'TR-' . str_pad($latestOrder ? $latestOrder->id + 1 : 1, 6, "0", STR_PAD_LEFT);
     
    //      // Handle attachments
    //      $attachments = [];
    //      if ($request->hasFile('attachments')) {
    //          foreach ($request->file('attachments') as $file) {
    //              $attachments[] = $file->store('transport_attachments', 'public');
    //          }
    //      }
     
    //      // Loop through order items and store each
    //      foreach ($request->products as $index => $product_id) {
    //          TransportOrder::create([
    //              'order_type'        => $request->order_type,
    //              'order_number'      => $orderNumber,
    //              'transporter_id'    => $request->transporters[$index] ?? null,
    //              'assigned_vehicle_id' => $request->vehicles[$index] ?? null,
    //              'product'           => $product_id,
    //              'quantity'          => $request->quantities[$index],
    //              'unit'              => $request->units[$index],
    //              'pickup_location'   => $pickup_locations[$index] ?? null,
    //              'delivery_location' => $request->deliveries[$index] ?? null,
    //              'pickup_date'       => Carbon::parse($request->pickup_date),
    //              'delivery_date'     => Carbon::parse($request->delivery_date),
    //              'priority'          => $request->priority,
    //              'transport_rate'    => $request->transport_rate,
    //              'advance_payment'   => $request->advance_payment,
    //              'payment_method'    => $request->payment_method,
    //              'status'            => $request->status,
    //              'notes'             => $request->notes,
    //              'attachments'       => json_encode($attachments),
    //          ]);
    //      }
     
    //      session()->flash("alert-success", "Transport order created successfully!");
    //      return redirect()->route('transport-orders.index');
    //  }


    /**
     * Display the specified resource.
     *
     * @param  \App\TransportOrder  $transportOrder
     * @return \Illuminate\Http\Response
     */
    public function show(TransportOrder $transportOrder)
    {
        $transportOrder->load(['transporter', 'payments']);
        return response()->json([
            'success' => true,
            'data' => $transportOrder
        ]);
    }
    
    public function edit(TransportOrder $transportOrder)
    {
        $transportOrder->load(['transporter']);
        return response()->json([
            'success' => true,
            'data' => $transportOrder
        ]);
    }
    
    public function update(Request $request, TransportOrder $transportOrder)
{
    $request->validate([
        'transporter_id' => 'required|exists:transporters,id',
        'pickup_location' => 'required',
        'delivery_location' => 'required',
        'pickup_date' => 'required|date',
        'delivery_date' => 'nullable|date|after:pickup_date',
        'transport_rate' => 'required|numeric|min:0',
        'advance_payment' => 'nullable|numeric|min:0|lte:transport_rate',
        'status' => 'required|in:draft,confirmed,dispatched,in_transit,delivered,closed',
        'priority' => 'required|in:normal,urgent,very_urgent',
        'notes' => 'nullable|string|max:500'
    ]);

    $data = $request->only([
        'transporter_id',
        'pickup_location',
        'delivery_location',
        'transport_rate',
        'advance_payment',
        'status',
        'priority',
        'notes'
    ]);

    // Format dates properly
    $data['pickup_date'] = Carbon::parse($request->pickup_date);
    if ($request->delivery_date) {
        $data['delivery_date'] = Carbon::parse($request->delivery_date);
    }

    // Check if advance payment is being updated
    if ($request->has('advance_payment') && $request->advance_payment != $transportOrder->advance_payment) {
        // If new advance payment equals transport rate, create a payment record
        if ($request->advance_payment == $request->transport_rate) {
            Payment::create([
                'transport_order_id' => $transportOrder->id,
                'amount' => $request->advance_payment,
                'payment_type' => 'full',
                'payment_method' => $transportOrder->payment_method,
                'status' => 'completed',
                'user_id' => auth()->id(),
                'receipt_number' => $transportOrder->order_number,
                'payment_date' => Carbon::now()
            ]);
        }
        // If there was an advance payment before but now it's less than before
        elseif ($transportOrder->advance_payment > 0 && $request->advance_payment < $transportOrder->advance_payment) {
            // You might want to handle this case (e.g., create a refund record)
        }
    }

    $transportOrder->update($data);

    // Update payment status after the order is updated
    $transportOrder->updatePaymentStatus();

    return redirect()->back()->with('success', 'Transport order updated successfully!');
}

// public function edit(TransportOrder $transportOrder)
// {
//     $transporters = Transporter::where('status', 'active')->get();
    
//     return view('transport-orders.edit', [
//         'transportOrder' => $transportOrder,
//         'transporters' => $transporters,
//         'pickupLocations' => TransportOrder::pickupLocations(),
//         'deliveryLocations' => TransportOrder::deliveryLocations(),
//         'statusOptions' => TransportOrder::statusOptions(),
//         'priorityOptions' => TransportOrder::priorityOptions()
//     ]);
// }

// public function update(Request $request, TransportOrder $transportOrder)
// {
//     $request->validate([
//         'transporter_id' => 'required|exists:transporters,id',
//         'pickup_location' => 'required',
//         'delivery_location' => 'required',
//         'pickup_date' => 'required|date',
//         'delivery_date' => 'nullable|date|after:pickup_date',
//         'transport_rate' => 'required|numeric|min:0',
//         'advance_payment' => 'nullable|numeric|min:0|lte:transport_rate',
//         'status' => 'required|in:draft,confirmed,dispatched,in_transit,delivered,closed',
//         'priority' => 'required|in:normal,urgent,very_urgent',
//         'notes' => 'nullable|string|max:500'
//     ]);

//     $data = $request->only([
//         'transporter_id',
//         'pickup_location',
//         'delivery_location',
//         'transport_rate',
//         'advance_payment',
//         'status',
//         'priority',
//         'notes'
//     ]);

//     // Format dates properly
//     $data['pickup_date'] = Carbon::parse($request->pickup_date);
//     if ($request->delivery_date) {
//         $data['delivery_date'] = Carbon::parse($request->delivery_date);
//     }

//     $transportOrder->update($data);

//     return redirect()->route('transport-orders.show', $transportOrder)
//         ->with('success', 'Transport order updated successfully!');
// }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TransportOrder  $transportOrder
     * @return \Illuminate\Http\Response
     */
    // public function update(Request $request, TransportOrder $transportOrder)
    // {
    //     $request->validate([
    //         'transporter_id' => 'required|exists:transporters,id',
    //         'pickup_location_id' => 'required|exists:locations,id',
    //         'delivery_location_id' => 'required|exists:locations,id|different:pickup_location_id',
    //         'pickup_date' => 'required|date',
    //         'delivery_date' => 'required|date|after:pickup_date',
    //         'product_id' => 'required|exists:products,id',
    //         'quantity' => 'required|numeric|min:0.1',
    //         'unit' => 'required|in:tons,bags,kg,units',
    //         'priority' => 'required|in:normal,urgent,very_urgent',
    //         'assigned_vehicle_id' => 'nullable|exists:vehicles,id',
    //         'transport_rate' => 'required|numeric|min:0',
    //         'advance_payment' => 'nullable|numeric|min:0',
    //         'payment_method' => 'nullable|in:cash,bank_transfer,mobile_money,cheque',
    //         'status' => 'required|in:draft,confirmed,dispatched,in_transit,delivered,closed',
    //         'notes' => 'nullable|string|max:500'
    //     ]);

    //     $transportOrder->update($request->all());

    //     session()->flash("alert-success", "Transport order updated successfully!");
    //     return redirect()->route('transport-orders.show', $transportOrder);
    // }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\TransportOrder  $transportOrder
     * @return \Illuminate\Http\Response
     */
    public function destroy(TransportOrder $transportOrder)
    {
        $transportOrder->delete();
        session()->flash("alert-success", "Transport order deleted successfully!");
        return redirect()->route('transport-orders.index');
    }

    
}
