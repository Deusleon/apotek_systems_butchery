<?php

namespace App\Http\Controllers;

use App\TransportOrder;
use App\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index(TransportOrder $transportOrder = null)
    {
        if ($transportOrder) {
            $payments = $transportOrder->payments()->with(['user', 'transportOrder'])->latest()->get();
            return view('payments.index', compact('transportOrder', 'payments'));
        } else {
            $payments = Payment::with(['transportOrder', 'user'])->latest()->get();
            return view('payments.index', compact('payments'));
        }
    }

    public function create(TransportOrder $transportOrder)
    {
        $summary = $transportOrder->paymentSummary();
    
        return view('payments.create', [
            'transportOrder' => $transportOrder,
            'summary' => $summary
        ]);
    }


    public function store(Request $request, TransportOrder $transportOrder = null)
    {
        $request->validate([
            'amount' => [
                'required',
                'numeric',
                'min:0.01',
                function ($attribute, $value, $fail) use ($transportOrder) {
                    if ($transportOrder) {
                        if ($value > $transportOrder->transport_rate) {
                            $fail('The amount cannot exceed the total transport rate of ' . number_format($transportOrder->transport_rate, 2) . '.');
                        }
                        if ($value > $transportOrder->balance()) {
                            $fail('Amount cannot exceed the remaining balance of ' . number_format($transportOrder->balance(), 2) . '.');
                        }
                    }
                }
            ],
            'payment_date' => 'required|date|before_or_equal:today',
            'payment_method' => 'required|in:cash,bank_transfer,mobile_money,cheque',
            'receipt_number' => 'required|string|unique:payments,receipt_number',
            'transaction_reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'payment_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);
    
        try {
            DB::beginTransaction();
    
            $payment = new Payment($request->except('payment_proof'));
            $payment->user_id = auth()->id();
            $payment->payment_type = 'balance';
            $payment->status = 'completed';
    
            if ($transportOrder) {
                $payment->transport_order_id = $transportOrder->id;
                $transportOrder->decrement('balance_due', $request->amount);
            }
    
            if ($request->hasFile('payment_proof')) {
                $payment->payment_proof = $request->file('payment_proof')->store('payment_proofs', 'public');
            }
    
            $payment->save();
    
            if ($transportOrder) {
                $transportOrder->updatePaymentStatus();
            }
    
            DB::commit();
    
            return response()->json([
                'success' => true,
                'message' => 'Payment recorded successfully!'
            ]);
    
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to record payment: ' . $e->getMessage()
            ], 500);
        }
    }

    // public function store(Request $request, TransportOrder $transportOrder)
    // {
    //     $request->validate([
    //         'amount' => [
    //             'required',
    //             'numeric',
    //             'min:0.01',
    //             function ($attribute, $value, $fail) use ($transportOrder, $request) {
    //                 $summary = $transportOrder->paymentSummary();
    //                 $maxAmount = $request->payment_type === 'advance' 
    //                     ? $summary['advance_balance'] 
    //                     : $summary['remaining_balance'];
                
    //                 if ($value > $maxAmount) {
    //                     $fail("Amount cannot exceed ".number_format($maxAmount, 2)." for this payment type");
    //                 }
    //             }
    //         ],
    //         'payment_type' => 'required|in:advance,balance',
    //         'payment_method' => 'required|in:cash,bank_transfer,mobile_money,cheque',
    //         'payment_date' => 'required|date|before_or_equal:today',
    //         'transaction_reference' => 'nullable|string|max:255',
    //         'receipt_number' => 'required|string|unique:payments,receipt_number',
    //         'payment_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
    //         'notes' => 'nullable|string'
    //     ]);

    //     try {
    //         DB::beginTransaction();

    //         $payment = new Payment($request->except('payment_proof'));
    //         $payment->transport_order_id = $transportOrder->id;
    //         $payment->user_id = auth()->id();
    //         $payment->payment_date = $request->payment_date ?? Carbon::now();

    //         if ($request->hasFile('payment_proof')) {
    //             $payment->payment_proof = $request->file('payment_proof')->store('payment_proofs', 'public');
    //         }

    //         $payment->save();

    //         $transportOrder->updatePaymentStatus();

    //         DB::commit();

    //         return redirect()
    //             ->route('transport-orders.payments.index', $transportOrder)
    //             ->with('success', 'Payment recorded successfully');

    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error('Failed to record payment: ' . $e->getMessage());
    //         return back()->with('error', 'Failed to record payment: ' . $e->getMessage());
    //     }
    // }



    // public function edit(TransportOrder $transportOrder, Payment $payment)
    // {
    //     $summary = $transportOrder->paymentSummary();

    //     return view('transport-orders.payments.edit', [
    //         'transportOrder' => $transportOrder,
    //         'payment' => $payment,
    //         'summary' => $summary
    //     ]);
    // }

    public function show(TransportOrder $transportOrder, Payment $payment)
{
    try {
        // Verify the payment belongs to the transport order
        if ($payment->transport_order_id !== $transportOrder->id) {
            return response()->json([
                'error' => 'Payment not found for this order.',
                'message' => 'The requested payment does not belong to this transport order.'
            ], 404);
        }

        // Load relationships if needed
        $transportOrder->load('transporter');
        
        $summary = $transportOrder->paymentSummary();
        $maxAmount = $summary['balance_due'] + $payment->amount;

        // Prepare payment data with proof URL
        $paymentData = [
            'id' => $payment->id,
            'amount' => $payment->amount,
            'payment_date' => $payment->payment_date,
            'payment_method' => $payment->payment_method,
            'receipt_number' => $payment->receipt_number,
            'transaction_reference' => $payment->transaction_reference,
            'notes' => $payment->notes,
            'payment_proof_url' => $payment->payment_proof ? Storage::url($payment->payment_proof) : null,
            'created_at' => $payment->created_at,
            'updated_at' => $payment->updated_at
        ];

        return response()->json([
            'payment' => $paymentData,
            'summary' => $summary,
            'max_amount' => $maxAmount,
            'update_url' => route('transport-orders.payments.update', [$transportOrder, $payment]),
            'message' => 'Payment details loaded successfully'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Server Error',
            'message' => 'Failed to load payment details: ' . $e->getMessage()
        ], 500);
    }
}

public function edit(TransportOrder $transportOrder, Payment $payment)
{
    $summary = [
        'order_number' => $transportOrder->order_number,
        'transporter_name' => $transportOrder->transporter->name,
    ];

    return response()->json([
        'payment' => [
            'payment_proof_url' => $payment->payment_proof ? Storage::url($payment->payment_proof) : null,
        ],
        'summary' => $summary,
        'max_amount' => $transportOrder->balance() + $payment->amount,
    ]);
}

public function update(Request $request, TransportOrder $transportOrder, Payment $payment)
{
    $originalAmount = $payment->amount;
    $maxAmount = $transportOrder->balance() + $originalAmount;

    $validator = Validator::make($request->all(), [
        'amount' => ['required', 'numeric', 'min:0.01', 'max:'.$maxAmount],
        'payment_date' => 'required|date|before_or_equal:today',
        'payment_method' => 'required|in:cash,bank_transfer,mobile_money,cheque',
        'receipt_number' => 'required|string|unique:payments,receipt_number,'.$payment->id,
        'transaction_reference' => 'nullable|string|max:255',
        'notes' => 'nullable|string',
        'payment_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'errors' => $validator->errors(),
            'message' => 'Validation failed'
        ], 422);
    }

    DB::beginTransaction();
    try {
        // Calculate difference
        $amountDifference = $request->amount - $originalAmount;

        // Update payment
        $payment->update([
            'amount' => $request->amount,
            'payment_date' => $request->payment_date,
            'payment_method' => $request->payment_method,
            'receipt_number' => $request->receipt_number,
            'transaction_reference' => $request->transaction_reference,
            'notes' => $request->notes,
        ]);

        // Handle file upload
        if ($request->hasFile('payment_proof')) {
            // Delete old file if exists
            if ($payment->payment_proof) {
                Storage::disk('public')->delete($payment->payment_proof);
            }
            // Store new file
            $payment->payment_proof = $request->file('payment_proof')->store('payment_proofs', 'public');
            $payment->save();
        }

        // Update transport order
        $transportOrder->decrement('balance_due', $amountDifference);
        $transportOrder->updatePaymentStatus();

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Payment updated successfully!',
            'redirect' => route('transport-orders.payments.index', $transportOrder)
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Failed to update payment: ' . $e->getMessage(),
            'error' => $e->getTraceAsString() // For debugging
        ], 500);
    }
}

    public function destroy(TransportOrder $transportOrder = null, Payment $payment)
    {
        try {
            DB::beginTransaction();

            // Delete payment proof file if exists
            if ($payment->payment_proof) {
                Storage::disk('public')->delete($payment->payment_proof);
            }

            $payment->delete();

            // Update transport order payment status if payment is associated with an order
            if ($payment->transportOrder) {
                $payment->transportOrder->updatePaymentStatus();
            }

            DB::commit();

            $redirectRoute = $transportOrder 
                ? route('transport-orders.payments.index', $transportOrder)
                : route('payments.all');

            return redirect()
                ->to($redirectRoute)
                ->with('success', 'Payment deleted successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete payment: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete payment: ' . $e->getMessage());
        }
    }

    public function allPayments()
    {
        $payments = Payment::with('transportOrder')->latest()->get();
        $transportOrder = null; // Ensure the variable is defined for the view
        return view('payments.index', compact('payments', 'transportOrder'));
    }

    // public function lookup()
    // {
    //     return view('payments.lookup');
    // }

    // public function createWithOrder(Request $request)
    // {
    //     $orderNumber = $request->input('order_number');
    //     $transportOrder = TransportOrder::where('order_number', $orderNumber)->first();

    //     if (!$transportOrder) {
    //         return redirect()->route('payments.lookup')->with('error', 'Order not found.');
    //     }

    //     return view('payments.create', compact('transportOrder'));
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


    public function createWithOrder(Request $request)
{
    $orderNumber = $request->query('order_number');
    
    if (!$orderNumber) {
        return redirect()->route('payments.lookup')->with('error', 'Order number is required');
    }

    $transportOrder = TransportOrder::where('order_number', $orderNumber)->first();

    if (!$transportOrder) {
        return redirect()->route('payments.lookup')->with('error', 'Order not found');
    }

    $summary = $transportOrder->paymentSummary();

    return view('payments.create', [
        'transportOrder' => $transportOrder,
        'summary' => $summary
    ]);
}

// public function lookupSummary(Request $request)
// {
//     $request->validate([
//         'order_number' => 'required|string'
//     ]);

//     $transportOrder = TransportOrder::where('order_number', $request->order_number)
//         ->with('client')
//         ->first();

//     if (!$transportOrder) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Transport order not found with the provided order number.'
//         ], 404);
//     }

//     $summary = $transportOrder->paymentSummary();

//     $html = view('payments.partials.order_summary', [
//         'transportOrder' => $transportOrder,
//         'summary' => $summary
//     ])->render();

//     return response()->json([
//         'success' => true,
//         'html' => $html,
//         'order_number' => $transportOrder->order_number
//     ]);
// }

public function createForm(Request $request)
{
    $request->validate([
        'order_number' => 'required|string'
    ]);

    $transportOrder = TransportOrder::where('order_number', $request->order_number)->first();

    if (!$transportOrder) {
        return response()->json([
            'success' => false,
            'message' => 'Transport order not found.'
        ], 404);
    }

    $summary = $transportOrder->paymentSummary();

    $html = view('payments.partials.payment_form', [
        'transportOrder' => $transportOrder,
        'summary' => $summary
    ])->render();

    return response()->json([
        'success' => true,
        'html' => $html
    ]);
}

// PaymentController.php

public function lookup(Request $request)
{
    $request->validate(['order_number' => 'required|string']);
    
    $transportOrder = TransportOrder::where('order_number', $request->order_number)
        ->with(['payments', 'transporter'])
        ->first();

    if (!$transportOrder) {
        return response()->json([
            'success' => false,
            'message' => 'Order not found. Please check the order number.'
        ], 404);
    }

    $summary = [
        'transport_rate' => $transportOrder->transport_rate,
        'amount_paid' => $transportOrder->payments->sum('amount'),
        'balance_due' => $transportOrder->balance(),
        'can_add_payment' => $transportOrder->balance() > 0
    ];

    if ($summary['balance_due'] <= 0) {
        return response()->json([
            'success' => false,
            'message' => 'This order is already fully paid.'
        ], 400);
    }

    return response()->json([
        'success' => true,
        'transportOrder' => $transportOrder, // Changed from 'order' to 'transportOrder'
        'summary' => $summary,
        'html' => view('payments.partials.payment_form', [
            'transportOrder' => $transportOrder,
            'summary' => $summary
        ])->render()
    ]);
}


}