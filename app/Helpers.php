<?php

use Illuminate\Support\Facades\DB;
use App\Setting;

function getRoles(){
    return DB::table('roles')
        ->select('id','name')
        ->orderBy('name')
        ->get();
}

function getSettings(){
    return Setting::get();
}

