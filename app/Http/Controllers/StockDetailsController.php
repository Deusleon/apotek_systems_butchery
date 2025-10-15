<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StockDetailsController extends Controller
{
    public function stockDetails()
    {

        return view('stock_management.stock_details.index');
    }
}
