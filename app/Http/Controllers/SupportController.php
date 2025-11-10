<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SupportController extends Controller
{
    public function index()
    {
        return view('support.index');
    }

    public function downloadManual()
    {
        $filePath = 'manuals/APOTEk_User_Guide.pdf';

        if (!Storage::disk('public')->exists($filePath)) {
            return redirect()->back()->with('error', 'User guide not found.');
        }

        return Storage::disk('public')->download($filePath, 'APOTEk_User_Guide.pdf');
    }
}