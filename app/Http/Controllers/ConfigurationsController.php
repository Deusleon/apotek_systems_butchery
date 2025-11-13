<?php

namespace App\Http\Controllers;

use App\PriceCategory;
use App\Setting;
use App\Store;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class ConfigurationsController extends Controller
 {

    public function index()
 {
        /*return default store*/
        $default_store = optional( Auth::user()->store->first() )->name;
        $stores = Store::where( 'name', $default_store )->first();
        $sale_types = PriceCategory::all();

        if ( $stores != null ) {
            $default_store_id = $stores->name;
        } else {
            $default_store_id = 'Please Set Store';
        }
        session()->put( 'store', $default_store_id );

        $configurations = Setting::orderBy( 'display_number', 'ASC' )->get();
        $store = Store::all();

        return View::make( 'configurations.index' )
        ->with( compact( 'configurations' ) )
        ->with( compact( 'sale_types' ) )
        ->with( compact( 'store' ) );
    }

    public function store( Request $request )
 {
        dd( $request->all() );
    }

    public function update( Request $request ) {
        $logo = $request->file( 'logo' );
        $setting = Setting::find( $request->setting_id );
        if ( $logo ) {
            File::delete( public_path() . '/fileStore/logo/' . $setting->value );
            $originalLogoName = $logo->getClientOriginalName();
            $logoExtension = $logo->getClientOriginalExtension();
            $logoStore = base_path() . '/public/fileStore/logo/';
            $logoName = $logo->getFilename() . '.' . $logoExtension;
            $logo->move( $logoStore, $logoName );
            $setting->value = $logoName;
        } else {
            $setting->value = $request->formdata;
        }

        if ( $request->setting_id == 121 ) {
            $user = auth()->user();

            if ( !$user || $user->store->name !== 'ALL' ) {
                return redirect()->back()->with( 'alert-warning', 'You do not have permission to perform this action!' );
            }

            $defaultStoreName = Setting::where( 'id', 122 )->value( 'value' );
            $defaultStore = Store::where( 'name', $defaultStoreName )->first();

            if ( !$defaultStore ) {
                return redirect()->back()->with( 'danger', 'Default Branch not found' );
            }

            $setting->updated_by = Auth::user()->id;
            $setting->save();

            session( [
                'current_store_id' => $defaultStore->id,
                'store' => $defaultStore->name
            ] );

            return redirect()->back()->with( 'alert-success', "Default Branch is {$defaultStore->name}" );
        }

        if ( $request->setting_id == 122 ) {
            $user = auth()->user();

            if ( !$user || $user->store->name !== 'ALL' ) {
                return redirect()->back()->with( 'warning', 'You do not have permission to perform this action!' );
            }

            $storeName = $request->formdata;
            $store = Store::where( 'name', $storeName )->first();

            if ( !$store ) {
                return redirect()->back()->with( 'error', 'Branch not found' );
            }

            $setting->updated_by = Auth::user()->id;
            $setting->save();

            session( [
                'current_store_id' => $store->id,
                'store' => $storeName
            ] );

            return redirect()->back()->with( 'success', "Branch changed to {$storeName}" );
        }

        $setting->updated_by = Auth::user()->id;
        $setting->save();

        return redirect()->back()->with( 'alert-success', 'Changes saved successfully!' );
    }

    public function destroy( $id )
 {
        //
    }
}
