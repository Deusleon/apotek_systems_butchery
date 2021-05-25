<?php

namespace App\Http\Controllers;

use App\PriceCategory;
use App\Setting;
use App\Store;
use Auth;
use File;
use Illuminate\Http\Request;
use View;

class ConfigurationsController extends Controller
{

    public function index()
    {
        /*return default store*/
        $default_store = Setting::where('id', 122)->value('value');
        $stores = Store::where('name', $default_store)->first();
        $sale_types = PriceCategory::all();

        if ($stores != null) {
            $default_store_id = $stores->name;
        } else {
            $default_store_id = "Please Set Store";
        }
        session()->put('store', $default_store_id);

        $configurations = Setting::orderBy('id', 'ASC')->get();
        $store = Store::all();

        return View::make('configurations.index')
            ->with(compact('configurations'))
            ->with(compact('sale_types'))
            ->with(compact('store'));
    }

    public function store(Request $request)
    {
        dd($request->all());
    }


    public function update(Request $request) {
        $logo=$request->file('logo');
        $setting =Setting::find($request->setting_id);
        if ($logo) {
            File::delete(public_path() . '/fileStore/logo/' . $setting->value);
            $originalLogoName = $logo->getClientOriginalName();
            $logoExtension = $logo->getClientOriginalExtension();
            $logoStore = base_path() . '/public/fileStore/logo/';
            $logoName = $logo->getFilename() . '.' . $logoExtension;
            $logo->move($logoStore, $logoName);
            $setting->value = $logoName;
        } else {
            $setting->value = $request->formdata;
        }
        $setting->updated_by=Auth::user()->id;
        $setting->save();
        session()->flash("alert-success", "Changes saved successfully!");
        return back();
    }


    public function destroy($id)
    {
        //
    }
}
