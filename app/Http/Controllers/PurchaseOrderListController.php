<?php

namespace App\Http\Controllers;

use App\GeneralSetting;
use App\Order;
use App\OrderDetail;
use App\Setting;
use DB;
use Illuminate\Http\Request;
use View;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade as PDF;

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

        $store_id = current_store_id();
        $useStoreFilter = !is_all_store();

        $query = Order::whereBetween('ordered_at', [$from, $to]);

        if ($useStoreFilter) {
            $query->where('store_id', $store_id);
        }

        $order_history = $query->orderByDesc('ordered_at')->get();

        foreach ($order_history as $value) {
            $value->supplier;
            $value->details;
        }
        $data = json_decode($order_history, true);

        return $data;
    }

    public function printOrder($order_no)
    {
    $pharmacy['name'] = Setting::where('id', 100)->value('value');
    $pharmacy['address'] = Setting::where('id', 106)->value('value');
    $pharmacy['phone'] = Setting::where('id', 107)->value('value');
    $pharmacy['email'] = Setting::where('id', 108)->value('value');
    $pharmacy['website'] = Setting::where('id', 109)->value('value');
    $pharmacy['logo'] = Setting::where('id', 105)->value('value');
    $pharmacy['tin_number'] = Setting::where('id', 102)->value('value');

    // Get general settings for terms & conditions
    $generalSettings = GeneralSetting::first();

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
        $data = $order_details;
                $pdf = PDF::loadView( 'purchases.purchase_order_list.purchase_order_pdf1',
                compact( 'data', 'pharmacy', 'generalSettings') )
                ->setPaper( 'a4', '' );
                return $pdf->stream( 'purchase_order.pdf' );
    }


}
