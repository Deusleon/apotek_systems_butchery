<?php

namespace App\Http\Controllers;

use App\TransportOrder;
use App\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    
    public function index(TransportOrder $transportOrder)
    {
        Log::info('Entering index method in PaymentController');
        Log::info('Transport Order ID: ' . ($transportOrder->id ?? 'Not Found'));

        if (!$transportOrder) {
            Log::error('Transport Order not found, redirecting to index');
            return redirect()->route('transport-orders.index')->with('error', 'Transport Order not found.');
        }

        $payments = $transportOrder->payments()->with(['user'])->latest()->paginate(10);
        Log::info('Payments retrieved: ' . $payments->count());

        return view('payments.index', compact('transportOrder', 'payments'));
    }
    public function create(TransportOrder $transportOrder)
    {
        $summary = $transportOrder->paymentSummary();
    
        return view('payments.create', [
            'transportOrder' => $transportOrder,
            'summary' => $summary
        ]);
    }

    public function store(Request $request, TransportOrder $transportOrder)
    {
        $request->validate([
            'amount' => [
                'required',
                'numeric',
                'min:0.01',
                function ($attribute, $value, $fail) use ($transportOrder, $request) {
                    $summary = $transportOrder->paymentSummary();
                    $maxAmount = $request->payment_type === 'advance' 
                        ? $summary['advance_balance'] 
                        : $summary['remaining_balance'];
                
                    if ($value > $maxAmount) {
                        $fail("Amount cannot exceed ".number_format($maxAmount, 2)." for this payment type");
                    }
                }
            ],
            'payment_type' => 'required|in:advance,balance',
            'payment_method' => 'required|in:cash,bank_transfer,mobile_money,cheque',
            'payment_date' => 'required|date|before_or_equal:today',
            'transaction_reference' => 'nullable|string|max:255',
            'receipt_number' => 'required|string|unique:payments,receipt_number',
            'payment_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            $payment = new Payment($request->except('payment_proof'));
            $payment->transport_order_id = $transportOrder->id;
            $payment->user_id = auth()->id();
            $payment->transaction_reference = $request->transaction_reference;
            $payment->receipt_number = 'TRO-' . $transportOrder->order_number;
            $payment->payment_date = $request->payment_date ?? Carbon::now();

            // Determine payment status
            $totalPaidBefore = $transportOrder->payments()->sum('amount');
            $totalPaid = $totalPaidBefore + $request->amount;
            if ($totalPaid >= $transportOrder->transport_rate) {
                $payment->status = 'completed';
            } else {
                $payment->status = 'partial';
            }

            if ($request->hasFile('payment_proof')) {
                $payment->payment_proof = $request->file('payment_proof')->store('payment_proofs', 'public');
            }

            $payment->save();

            $transportOrder->updatePaymentStatus();

            DB::commit();

            return redirect()
                ->route('transport-orders.payments.index', $transportOrder)
                ->with('success', 'Payment recorded successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to record payment: ' . $e->getMessage());
        }
    }


public function edit(TransportOrder $transportOrder, Payment $payment)
{
    Log::info('Transport Order ID: ' . $transportOrder->id);
    Log::info('Payment ID: ' . $payment->id);
    Log::info('Payment belongs to Transport Order: ' . ($payment->transport_order_id === $transportOrder->id ? 'Yes' : 'No'));

    $summary = $transportOrder->paymentSummary();

    return view('transport-orders.payments.edit', [
        'transportOrder' => $transportOrder,
        'payment' => $payment,
        'summary' => $summary
    ]);
}

    public function update(Request $request, TransportOrder $transportOrder, Payment $payment)
    {
        $request->validate([
            'amount' => [
                'required',
                'numeric',
                'min:0.01',
                function ($attribute, $value, $fail) use ($transportOrder, $request, $payment) {
                    $summary = $transportOrder->paymentSummary();
                    $maxAmount = $request->payment_type === 'advance' 
                        ? $summary['advance_balance'] + $payment->amount
                        : $summary['remaining_balance'] + $payment->amount;
                
                    if ($value > $maxAmount) {
                        $fail("Amount cannot exceed ".number_format($maxAmount, 2)." for this payment type");
                    }
                }
            ],
            'payment_type' => 'required|in:advance,balance',
            'payment_method' => 'required|in:cash,bank_transfer,mobile_money,cheque',
            'payment_date' => 'required|date|before_or_equal:today',
            'transaction_reference' => 'nullable|string|max:255',
            'receipt_number' => 'required|string|unique:payments,receipt_number,'.$payment->id,
            'payment_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'notes' => 'nullable|string'
        ]);

        $payment->fill($request->except('payment_proof'));
        $payment->transaction_reference = $request->transaction_reference;
        $payment->receipt_number = 'TRO-' . $transportOrder->order_number;

        if ($request->hasFile('payment_proof')) {
            // Delete old file if exists
            if ($payment->payment_proof) {
                Storage::disk('public')->delete($payment->payment_proof);
            }
            $payment->payment_proof = $request->file('payment_proof')->store('payment_proofs', 'public');
        }

        $payment->save();

        // Update transport order payment status
        $transportOrder->updatePaymentStatus();

        return redirect()
            ->route('transport-orders.payments.index', $transportOrder)
            ->with('success', 'Payment updated successfully');
    }

    public function destroy(TransportOrder $transportOrder, Payment $payment)
    {
        // Delete payment proof file if exists
        if ($payment->payment_proof) {
            Storage::disk('public')->delete($payment->payment_proof);
        }

        $payment->delete();

        // Update transport order payment status
        $transportOrder->updatePaymentStatus();

        return redirect()
            ->route('transport-orders.payments.index', $transportOrder)
            ->with('success', 'Payment deleted successfully');
    }

    public function allPayments()
    {
        $payments = Payment::with(['transportOrder', 'user'])
            ->latest()
            ->paginate(25);
        
        return view('payments.index', compact('payments'));
    }

    public function lookup()
    {
        return view('payments.lookup');
    }

    public function createWithOrder(Request $request)
    {
        $orderNumber = $request->input('order_number');
        $transportOrder = TransportOrder::where('order_number', $orderNumber)->first();

        if (!$transportOrder) {
            return redirect()->route('payments.lookup')->with('error', 'Order not found.');
        }

        return view('payments.create', compact('transportOrder'));
    }

    public function getAllPayments(Request $request)
    {
        $columns = ['payment_date', 'order_number', 'amount', 'payment_type', 'payment_method', 'status', 'transaction_reference', 'receipt_number', 'notes', 'action'];
        $totalData = Payment::count();
        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if(empty($request->input('search.value')))
        {
            $payments = Payment::with('transportOrder')->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        }
        else {
            $search = $request->input('search.value'); 

            $payments = Payment::with('transportOrder')
                ->where('amount','LIKE',"%{$search}%")
                ->orWhere('payment_type', 'LIKE', "%{$search}%")
                ->orWhere('payment_method', 'LIKE', "%{$search}%")
                ->orWhere('status', 'LIKE', "%{$search}%")
                ->orWhere('transaction_reference', 'LIKE', "%{$search}%")
                ->orWhere('receipt_number', 'LIKE', "%{$search}%")
                ->orWhere('notes', 'LIKE', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();

            $totalFiltered = Payment::where('amount','LIKE',"%{$search}%")
                ->orWhere('payment_type', 'LIKE', "%{$search}%")
                ->orWhere('payment_method', 'LIKE', "%{$search}%")
                ->orWhere('status', 'LIKE', "%{$search}%")
                ->orWhere('transaction_reference', 'LIKE', "%{$search}%")
                ->orWhere('receipt_number', 'LIKE', "%{$search}%")
                ->orWhere('notes', 'LIKE', "%{$search}%")
                ->count();
        }

        $data = array();
        if(!empty($payments))
        {
            foreach ($payments as $payment)
            {
                $nestedData['payment_date'] = $payment->payment_date->format('Y-m-d');
                $nestedData['order_number'] = $payment->transportOrder->order_number ?? 'N/A';
                $nestedData['amount'] = $payment->amount;
                $nestedData['payment_type'] = $payment->transportOrder->paymentSummary()['is_fully_paid'] ? 'Balance' : 'Advance';
                $nestedData['payment_method'] = $payment->payment_method;
                $nestedData['status'] = $payment->status === 'completed' ? '<span class="badge badge-success">Complete</span>' : '<span class="badge badge-warning">Partial</span>';
                $nestedData['transaction_reference'] = $payment->transaction_reference;
                $nestedData['receipt_number'] = $payment->receipt_number;
                $nestedData['notes'] = $payment->notes;
                $nestedData['action'] = "<button class='btn btn-sm btn-primary btn-edit'>Edit</button> <button class='btn btn-sm btn-info btn-show'>Show</button> <button class='btn btn-sm btn-danger btn-delete'>Delete</button>";
                $data[] = $nestedData;
            }
        }

        $json_data = array(
            "draw" => intval($request->input('draw')),  
            "recordsTotal" => intval($totalData),  
            "recordsFiltered" => intval($totalFiltered), 
            "data" => $data   
        );
        
        echo json_encode($json_data); 
    }


    // Add to your PaymentController

/**
 * Get payment details for show modal (AJAX)
 */
// public function showDetails(Payment $payment)
// {
//     return response()->json([
//         'transport_order' => [
//             'order_number' => $payment->transportOrder->order_number ?? 'N/A'
//         ],
//         'amount' => $payment->amount,
//         'amount_formatted' => number_format($payment->amount, 2),
//         'payment_type' => $payment->payment_type,
//         'payment_type_formatted' => ucfirst($payment->payment_type),
//         'payment_method' => $payment->payment_method,
//         'payment_method_formatted' => ucfirst(str_replace('_', ' ', $payment->payment_method)),
//         'payment_date' => $payment->payment_date,
//         'payment_date_formatted' => $payment->payment_date->format('Y-m-d'),
//         'receipt_number' => $payment->receipt_number,
//         'status' => $payment->status,
//         'status_badge' => $payment->status === 'completed' 
//             ? '<span class="badge badge-success">Complete</span>' 
//             : '<span class="badge badge-warning">Partial</span>',
//         'notes' => $payment->notes,
//         'payment_proof_url' => $payment->payment_proof 
//             ? Storage::url($payment->payment_proof) 
//             : null
//     ]);
// }

/**
 * Get payment data for edit (AJAX)
 */
// public function editData(Payment $payment)
// {
//     $payment->load('transportOrder'); // Eager load transport order

//     return response()->json([
//         'id' => $payment->id,
//         'transport_order_id' => $payment->transport_order_id,
//         'order_number' => optional($payment->transportOrder)->order_number,
//         'amount' => $payment->amount,
//         'payment_type' => $payment->payment_type,
//         'payment_method' => $payment->payment_method,
//         'receipt_number' => $payment->receipt_number,
//         'payment_date' => $payment->payment_date->format('Y-m-d'),
//         'transaction_reference' => $payment->transaction_reference,
//         'notes' => $payment->notes,
//         'status' => $payment->status,
//         'payment_proof_url' => $payment->payment_proof 
//             ? asset('storage/' . $payment->payment_proof)
//             : null,
//     ]);
// }


// public function editData($id)
// {
//     $payment = Payment::with('transportOrder')->find($id);

//     if (!$payment) {
//         return response()->json(['error' => 'Payment not found'], 404);
//     }

//     return response()->json([
//         'id' => $payment->id,
//         'amount' => $payment->amount,
//         'payment_type' => $payment->payment_type,
//         'payment_method' => $payment->payment_method,
//         'receipt_number' => $payment->receipt_number,
//         'payment_date' => $payment->payment_date->format('Y-m-d'),
//         'transaction_reference' => $payment->transaction_reference,
//         'notes' => $payment->notes,
//         'status' => $payment->status,
//         'transport_order_id' => optional($payment->transportOrder)->id,
//     ]);
// }


public function editData(Payment $payment)
{
    return response()->json([
        'id' => $payment->id,
        'amount' => $payment->amount,
        'payment_type' => $payment->payment_type,
        'payment_method' => $payment->payment_method,
        'receipt_number' => $payment->receipt_number,
        'payment_date' => $payment->payment_date->format('Y-m-d'),
        'transaction_reference' => $payment->transaction_reference,
        'notes' => $payment->notes,
        'status' => $payment->status,
        'transport_order' => [
            'order_number' => optional($payment->transportOrder)->order_number
        ]
    ]);
}

public function showDetails(Payment $payment)
{
    return response()->json([
        'id' => $payment->id,
        'amount' => $payment->amount,
        'payment_type' => $payment->payment_type,
        'payment_method' => $payment->payment_method,
        'receipt_number' => $payment->receipt_number,
        'payment_date' => $payment->payment_date->format('Y-m-d'),
        'transaction_reference' => $payment->transaction_reference,
        'notes' => $payment->notes,
        'status' => $payment->status,
        'transport_order' => [
            'order_number' => optional($payment->transportOrder)->order_number
        ],
        'payment_proof_url' => $payment->payment_proof ? Storage::url($payment->payment_proof) : null
    ]);
}


}
