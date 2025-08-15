<?php

namespace App\Http\Controllers;

use App\TransportOrder; 
use App\Transporter;
use App\Location;
use App\Product;
use App\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TransportOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $transportOrders = TransportOrder::with(['transporter', 'assignedVehicle'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('transport-orders.index', compact('transportOrders'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
{
    $transporters = Transporter::all();
    $vehicles = Vehicle::all();
    
    // Pass all the static options from TransportOrder to the view
    $data = [
        'transporters' => $transporters,
        'vehicles' => $vehicles,
        'pickupLocations' => TransportOrder::pickupLocations(),
        'deliveryLocations' => TransportOrder::deliveryLocations(),
        'productOptions' => TransportOrder::productOptions(),
        'unitOptions' => TransportOrder::unitOptions(),
        'priorityOptions' => TransportOrder::priorityOptions(),
        'paymentMethods' => TransportOrder::paymentMethods(),
        'statusOptions' => TransportOrder::statusOptions()
    ];
    
    return view('transport-orders.create', $data);
}

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    

    public function store(Request $request)
    {
        $validated = $request->validate([
            'transporter_id' => 'required|exists:transporters,id',
            'pickup_location' => 'required|string|max:255',
            'delivery_location' => 'required|string|max:255|different:pickup_location',
            'pickup_date' => 'required|date',
            'delivery_date' => 'required|date|after:pickup_date',
            'product' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:0.1',
            'unit' => 'required|in:tons,bags,kg,units',
            'priority' => 'required|in:normal,urgent,very_urgent',
            'assigned_vehicle_id' => 'nullable|exists:vehicles,id',
            'transport_rate' => 'required|numeric|min:0',
            'advance_payment' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|in:cash,bank_transfer,mobile_money,cheque',
            'status' => 'required|in:draft,confirmed,dispatched,in_transit,delivered,closed',
            'notes' => 'nullable|string|max:500'
        ]);
    
        // Parse dates explicitly with Carbon to avoid errors
        $validated['pickup_date'] = Carbon::parse($request->pickup_date)->format('Y-m-d');
        $validated['delivery_date'] = Carbon::parse($request->delivery_date)->format('Y-m-d');
    
        // Generate order number
        $latestOrder = TransportOrder::latest()->first();
        $validated['order_number'] = 'TR-' . str_pad($latestOrder ? $latestOrder->id + 1 : 1, 6, "0", STR_PAD_LEFT);
    
        $transportOrder = TransportOrder::create($validated);
    
        session()->flash("alert-success", "Transport order created successfully!");
        return redirect()->route('transport-orders.show', $transportOrder);
    }
    

    /**
     * Display the specified resource.
     *
     * @param  \App\TransportOrder  $transportOrder
     * @return \Illuminate\Http\Response
     */
    public function show(TransportOrder $transportOrder)
    {
        return view('transport-orders.show', compact('transportOrder'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\TransportOrder  $transportOrder
     * @return \Illuminate\Http\Response
     */
    public function edit(TransportOrder $transportOrder)
    {
        $transporters = Transporter::where('status', 'active')->get();
        $locations = Location::all();
        $products = Product::all();
        $vehicles = Vehicle::where('status', 'active')->get();
        
        return view('transport-orders.edit', compact('transportOrder', 'transporters', 'locations', 'products', 'vehicles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TransportOrder  $transportOrder
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TransportOrder $transportOrder)
    {
        $request->validate([
            'transporter_id' => 'required|exists:transporters,id',
            'pickup_location_id' => 'required|exists:locations,id',
            'delivery_location_id' => 'required|exists:locations,id|different:pickup_location_id',
            'pickup_date' => 'required|date',
            'delivery_date' => 'required|date|after:pickup_date',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0.1',
            'unit' => 'required|in:tons,bags,kg,units',
            'priority' => 'required|in:normal,urgent,very_urgent',
            'assigned_vehicle_id' => 'nullable|exists:vehicles,id',
            'transport_rate' => 'required|numeric|min:0',
            'advance_payment' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|in:cash,bank_transfer,mobile_money,cheque',
            'status' => 'required|in:draft,confirmed,dispatched,in_transit,delivered,closed',
            'notes' => 'nullable|string|max:500'
        ]);

        $transportOrder->update($request->all());

        session()->flash("alert-success", "Transport order updated successfully!");
        return redirect()->route('transport-orders.show', $transportOrder);
    }

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