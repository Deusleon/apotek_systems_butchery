<?php

namespace App\Http\Controllers;
use App\GeneralSetting;
use App\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use View;

class GeneralSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $generalSettings= GeneralSetting::orderBy('id','ASC')->get();
        $store=Store::all();

        return view('masters.general_setting.index')
        ->with(compact('generalSettings'))
        ->with(compact('store'));
    }

     public function updateReceipt(Request $request)
   {
       // Validate the request
       $request->validate([
           'cash_sale_terms' => 'nullable|string|max:5000',
           'credit_sale_terms' => 'nullable|string|max:5000',
           'proforma_invoice_terms' => 'nullable|string|max:5000',
           'purchase_order_terms' => 'nullable|string|max:5000',
           'delivery_note_terms' => 'nullable|string|max:5000',
       ]);

       try {
           // Find or create the general setting record
           $receipt = GeneralSetting::firstOrCreate(['id' => 1]);
           
           $receipt->cash_sale_terms = $request->cash_sale_terms;
           $receipt->credit_sale_terms = $request->credit_sale_terms;
           $receipt->proforma_invoice_terms = $request->proforma_invoice_terms;
           $receipt->purchase_order_terms = $request->purchase_order_terms;
           $receipt->delivery_note_terms = $request->delivery_note_terms;
           $receipt->save();

           session()->flash("alert-success","Terms and conditions updated successfully!");
           return back();
       } catch (\Exception $e) {
           session()->flash("alert-danger","Error updating terms and conditions: " . $e->getMessage());
           return back();
       }
   }

   

}
