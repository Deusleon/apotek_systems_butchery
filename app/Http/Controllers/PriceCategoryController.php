<?php

namespace App\Http\Controllers;

use App\PriceCategory;
use Exception;
use Illuminate\Http\Request;

class PriceCategoryController extends Controller
{
    public function index()
    {

        $price_category = PriceCategory::orderBy('id', 'ASC')->get();
        return view('masters.price_category.index')->with('price_category', $price_category);
    }

    public function store(Request $request)
    {
        try {
            $price_category = new PriceCategory;
            $price_category->name = $request->name;
            $price_category->type = $request->code;
            $price_category->save();
        } catch (Exception $exception) {
            session()->flash("alert-danger", "Price Category Name Exists!");
            return back();
        }

        session()->flash("alert-success", "Price category added successfully!");
        return back();
    }

    public function update(Request $request)
    {

        $price_category = PriceCategory::find($request->price_category_id);
        $price_category->type = $request->code;
        $price_category->name = $request->name;
        $price_category->save();

        session()->flash("alert-success", "Price category updated successfully!");
        return back();

    }

    public function destroy(Request $request)
    {
        try {
            PriceCategory::destroy($request->price_category_id);
            session()->flash("alert-danger", "Price category deleted successfully!");
            return back();
        } catch (Exception $e) {
            session()->flash("alert-danger", "Price category in use!");
            return back();
        }

    }
}
