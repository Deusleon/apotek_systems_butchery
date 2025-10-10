<?php

namespace App\Http\Controllers;

use App\Invoice;
use App\Supplier;
use Auth;
use DB;
use Exception;
use Illuminate\Http\Request;
use View;

class InvoiceController extends Controller
{
    //
    public function index()
    {
        $invoices = Invoice::orderBy('invoice_date', 'DESC')->get();

        $suppliers = Supplier::orderBy('name', 'ASC')->get();

        return View::make('purchases.invoice_management.index',
            compact('invoices', 'suppliers'));
    }

    public function payments()
    {
        $invoices = Invoice::where('remain_balance', '>', 0)
            ->orderBy('invoice_date', 'DESC')
            ->get();

        $suppliers = Supplier::orderBy('name', 'ASC')->get();

        return View::make('purchases.invoice_management.payments',
            compact('invoices', 'suppliers'));
    }

    public function store(Request $request)
    {
        date_default_timezone_set('Africa/Nairobi');
        $date = date('Y-m-d H:i:s');
        try {

            $invoice = new  Invoice;
            $invoice->invoice_no = $request->invoice_number;
            $invoice->supplier_id = $request->supplier;
            $invoice->invoice_date = $request->invoice_date;
            $invoice->invoice_amount = str_replace(',', '', $request->invoice_amount);
            $invoice->paid_amount = str_replace(',', '', $request->paid_amount);
            $invoice->received_amount = str_replace(',', '', $request->received_amount);
            $invoice->grace_period = $request->grace_period;
            $invoice->received_status = $request->received_status;
            $invoice->payment_due_date = $request->payment_due_date;
            $invoice->remarks = $request->remarks;
            $invoice->updated_by = Auth::user()->id;
            $invoice->updated_at = $date;
            $invoice->save();

            session()->flash("alert-success", "Invoice Added Successfully!");
            return back();
        } catch (Exception $exception) {

            session()->flash("alert-danger", "Invoice Exists Already!");
            return back();

        }
    }

    public function update(Request $request)
    {
        date_default_timezone_set('Africa/Nairobi');
        $date = date('Y-m-d H:i:s');

        $invoice = Invoice::find($request->id);
        $invoice->invoice_no = $request->invoice_number;
        $invoice->supplier_id = $request->supplier;
        $invoice->invoice_date = $request->invoice_date;
        $invoice->invoice_amount = str_replace(',', '', $request->invoice_amount);
        $invoice->paid_amount = str_replace(',', '', $request->paid_amount);
        $invoice->received_amount = str_replace(',', '', $request->received_amount);
        $invoice->grace_period = $request->grace_period;
        $invoice->received_status = $request->received_status;
        $invoice->payment_due_date = $request->payment_due_date;
        $invoice->remarks = $request->remarks;
        $invoice->updated_by = Auth::user()->id;
        $invoice->updated_at = $date;
        $invoice->save();

        session()->flash("alert-success", "Invoice Updated Successfully!");
        return back();
    }

    public function getInvoice(Request $request)
    {
        $from = $request->date[0];
        $to   = $request->date[1];

        $invoice_history = Invoice::whereBetween('invoice_date', [$from, $to])
            ->orderBy('invoice_date', 'DESC')
            ->get();

        foreach ($invoice_history as $value) {
            $value->supplier;
            $value->date     = date('Y-m-d', strtotime($value->invoice_date));
            $value->due_date = date('Y-m-d', strtotime($value->payment_due_date));

            // Calculate paid status
            if ($value->paid_amount >= $value->invoice_amount) {
                $value->paid_status = 'Fully Paid';
            } elseif ($value->paid_amount > 0) {
                $value->paid_status = 'Partially Paid';
            } else {
                $value->paid_status = 'Unpaid';
            }

            // Received status is already stored in received_status field
            $value->received_status = $value->received_status;
        }

        return response()->json($invoice_history);
    }

    public function getInvoiceByDueDate(Request $request)
    {
        $from  = $request->date[0];
        $to    = $request->date[1];
        $from1 = $request->date1[0];
        $to1   = $request->date1[1];

        $invoice_history = Invoice::whereBetween('payment_due_date', [$from, $to])
            ->whereBetween('invoice_date', [$from1, $to1])
            ->orderBy('invoice_date', 'DESC')
            ->get();

        foreach ($invoice_history as $value) {
            $value->supplier;
            $value->date     = date('Y-m-d', strtotime($value->invoice_date));
            $value->due_date = date('Y-m-d', strtotime($value->payment_due_date));

            // Calculate paid status
            if ($value->paid_amount >= $value->invoice_amount) {
                $value->paid_status = 'Fully Paid';
            } elseif ($value->paid_amount > 0) {
                $value->paid_status = 'Partially Paid';
            } else {
                $value->paid_status = 'Unpaid';
            }

            // Received status is already stored in received_status field
            $value->received_status = $value->received_status;
        }

        return response()->json($invoice_history);
    }

    public function storePayment(Request $request)
    {
        date_default_timezone_set('Africa/Nairobi');
        $date = date('Y-m-d H:i:s');

        try {
            // Validate the request
            $request->validate([
                'supplier_id' => 'required|exists:inv_suppliers,id',
                'invoice_id' => 'required|exists:inv_invoices,id',
                'amount_paid' => 'required|numeric|min:0.01',
                'payment_method' => 'required|in:cash,bank_transfer,mobile_money,cheque',
                'payment_date' => 'required|date|before_or_equal:today',
                'remarks' => 'nullable|string|max:255'
            ]);

            // Check if invoice belongs to the selected supplier
            $invoice = Invoice::where('id', $request->invoice_id)
                            ->where('supplier_id', $request->supplier_id)
                            ->first();

            if (!$invoice) {
                return response()->json(['error' => 'Invalid invoice for selected supplier'], 422);
            }

            // Check if payment amount exceeds remaining balance
            $remainingBalance = $invoice->invoice_amount - $invoice->paid_amount;
            if ($request->amount_paid > $remainingBalance) {
                return response()->json(['error' => 'Payment amount cannot exceed remaining balance'], 422);
            }

            // Update invoice paid amount
            $invoice->paid_amount += str_replace(',', '', $request->amount_paid);
            $invoice->save();

            // Here you could create a payment record in a separate payments table if needed
            // For now, we'll just update the invoice

            session()->flash("alert-success", "Payment recorded successfully!");
            return response()->json(['success' => true, 'message' => 'Payment recorded successfully!']);

        } catch (Exception $exception) {
            return response()->json(['error' => 'Payment failed: ' . $exception->getMessage()], 500);
        }
    }

    public function getPaymentHistory()
    {
        // For now, return empty array since we're not storing payments separately
        // In a full implementation, you'd query a payments table
        return response()->json([]);
    }

    public function getSupplierInvoices(Request $request)
    {
        $supplierId = $request->query('supplier_id');

        if (!$supplierId) {
            return response()->json([]);
        }

        $invoices = Invoice::where('supplier_id', $supplierId)
                         ->whereRaw('paid_amount < invoice_amount')
                         ->orderBy('invoice_date', 'DESC')
                         ->get(['id', 'invoice_no', 'invoice_amount', 'paid_amount']);

        return response()->json($invoices);
    }

}
