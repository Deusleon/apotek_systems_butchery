<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\User;
use File;
use DB;

class ProfileController extends Controller
{
    public function index()
    {   
        return view('layouts.profile');
    }

    public function updateProfileImage(Request $request)
    {
        $logo = $request->file('profile_image');
        $user_id = User::find(Auth::user()->id);
        if ($logo) {
            File::delete(public_path() . '/fileStore/logo/' . $user_id->profile_image);
            $originalLogoName = $logo->getClientOriginalName();
            $logoExtension = $logo->getClientOriginalExtension();
            $logoStore = base_path() . '/public/fileStore/logo/';
            $logoName = $logo->getFilename() . '.' . $logoExtension;
            $logo->move($logoStore, $logoName);
            $user_id->profile_image = $logoName;
        } else {
            $user_id->profile_image = $request->formdata;
        }
        //$user_id->updated_by = Auth::user()->id;
        $user_id->save();
        session()->flash("alert-success", "Pofile image updated successfully!");
        return back();
    }
}
