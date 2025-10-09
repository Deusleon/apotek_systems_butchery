<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Supplier; // Make sure you have a Supplier model
use App\Product;  // If you use products dropdown

class PurchaseReturnController extends Controller
{
    public function index()
    {
        // Fetch all suppliers (or adjust as needed)
        $suppliers = Supplier::all();

        // Fetch all products (if you need them)
        $products = Product::all();

        // If you have a default expire_date variable in your view
        $expire_date = 'NO'; // or any default value

        return view('purchases.purchase_returns.index', compact('suppliers', 'products', 'expire_date'));
    }

    public function approvals()
    {
        
        return view('purchases.purchase_returns.approvals');
    }
}
