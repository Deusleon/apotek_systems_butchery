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
        $filePath = 'manuals/user_manual.pdf';

        if (!Storage::disk('public')->exists($filePath)) {
            return redirect()->back()->with('error', 'User manual not found.');
        }

        return Storage::disk('public')->download($filePath, 'APOTEK_POS_User_Manual.pdf');
    }
}