<?php

namespace App\Http\Controllers;

use App\Order;
use App\OrderDetail;
use DB;
use Illuminate\Http\Request;
use View;
use Carbon\Carbon;

class PurchaseOrderListController extends Controller
{
    //
    public function index()
    {
        return View::make('purchases.purchase_order_list.index');
    }

    public function destroy(Request $request)
    {

        $order = Order::find($request->id);
        $order->status = 'Cancelled';
        $order->save();
        session()->flash("alert-danger", "Order Rejected Successfully!");
        return back();
    }

    public function getOrderHistory(Request $request)
    {


        $from = Carbon::parse($request->date[0]);
        $to =  Carbon::parse($request->date[1]);
        $order_history = Order::whereBetween('ordered_at', [$from, $to])->orderByDesc('ordered_at')->get();
        foreach ($order_history as $value) {
            $value->supplier;
            $value->details;
        }
        $data = json_decode($order_history, true);

        return $data;
    }

    public function printOrder($order_no)
    {
        $order_details = OrderDetail::where('order_id', $order_no)->get();
        $sub_total = 0;
        $vat = 0;
        $total = 0;
        foreach ($order_details as $order_detail) {
            $order_detail->sub_total = $order_detail->amount - $order_detail->vat;
            $sub_total = $sub_total + $order_detail->sub_total;
            $vat = $vat + $order_detail->vat;
            $total = $total + $order_detail->vat + $order_detail->sub_total;

            $order_detail->sub_totals = $sub_total;
            $order_detail->vats = $vat;
            $order_detail->total = $total;
        }

        $print = new InventoryReportController();
        $view = 'purchases.purchase_order_list.purchase_order_pdf1';
        $output = 'purchase_order.pdf';
        $print->splitPdf($order_details, $view, $output);
    }


}
