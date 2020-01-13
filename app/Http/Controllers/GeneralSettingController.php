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

        return View::make('masters.general_setting.index')
        ->with(compact('generalSettings'))
        ->with(compact('store'));
    }

    public function updateInfo(Request $request)
    {
        $logo=$request->file('logo');
        $general_info = GeneralSetting::find($request->id);
         if(!empty($logo)){ 
       $originalLogoName = $logo->getClientOriginalName();
        $logoExtension = $logo->getClientOriginalExtension();
        $logoStore = base_path().'/public/fileStore/logo/';
        $logoName = $logo ->getFilename().'.'.$logoExtension;
        $logo->move($logoStore,$logoName);
        $general_info = GeneralSetting::find($request->id);
        $general_info->logo =$logoName;
      }
        $general_info->business_name = $request->business_name;
        $general_info->registration_number = $request->registration_number;
        $general_info->tin_number = $request->tin_number;
        $general_info->vrn_number = $request->vrn_number;
        $general_info->phone = $request->phone;
        $general_info->email = $request->email;
        $general_info->website = $request->website;
        $general_info->address = $request->address;
        $general_info->slogan = $request->slogan;
        $general_info->save();

        session()->flash("alert-success", "Changes saved successfully!");
        return back();
    }

     public function updateSetting(Request $request)
    {
    
       $general_setting = GeneralSetting::find($request->id);
      if(!empty($request->make_batch_number_mandatory)){ 
        $general_setting->make_batch_number_mandatory="Yes"; }
      else{ 
      $general_setting->make_batch_number_mandatory="No";
     }

     if(!empty($request->support_multstore)){ 
        $general_setting->support_multstore="Yes"; }
      else{ 
      $general_setting->support_multstore="No";
     }
      if(!empty($request->make_customer_name_mandatory)){
        $general_setting->make_customer_name_mandatory="Yes"; }
      else{ 
      $general_setting->make_customer_name_mandatory="No";
     }
      if(!empty($request->make_invoice_number_mandatory)){
        $general_setting->make_invoice_number_mandatory="Yes"; }
      else{ 
      $general_setting->make_invoice_number_mandatory="No";
     }
     if(!empty($request->enable_cashflow_feature)){ 
        $general_setting->enable_cashflow_feature="Yes"; }
      else{ 
      $general_setting->enable_cashflow_feature="No";
     }
      if(!empty($request->enable_back_date_sale)){
        $general_setting->enable_back_date_sale="Yes"; }
      else{ 
      $general_setting->enable_back_date_sale="No";
     } 
        $general_setting->good_receiving_option = $request->good_receiving_option;
        $general_setting->default_store_id = $request->default_store_id;
        
        $general_setting->save();

        session()->flash("alert-success", "Changes saved successfully!");
        return back();
    }

     public function updateReceipt(Request $request)
    {
        $receipt = GeneralSetting::find($request->id);
        if(!empty($request->receipt_printing)){ 
        $receipt->receipt_printing="Yes"; 
        }
        else{ 
        $receipt->receipt_printing="No";
        }
        if(!empty($request->location_printing)){ 
        $receipt->location_printing="Yes"; }
        else{ 
        $receipt->location_printing="No";
        }
        $receipt->number_of_copies = $request->number_of_copies;
        $receipt->receipt_size = $request->receipt_size;
        $receipt->vat_or_tax = $request->vat_or_tax;
        $receipt->save();


        session()->flash("alert-success","Changes saved successfully!");
        return back();
    }

   

}
