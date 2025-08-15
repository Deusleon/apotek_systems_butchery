<?php

namespace App\Http\Controllers;

use App\PriceCategory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PriceCategoryController extends Controller
{
    public function index()
    {

        $price_category = PriceCategory::orderBy('id', 'ASC')->get();
        return view('masters.price_category.index')->with('price_category', $price_category);
    }

    public function store(Request $request)
    {
        $existing = PriceCategory::where('name',$request->name)->count();

        if($existing > 0)
        {
            session()->flash("alert-danger", "Price Category Name Exists!");
            return back();
        }
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

        $existing = PriceCategory::where('name',$request->name)->count();

        if($existing > 0)
        {
            session()->flash("alert-danger", "Price Category Name Exists!");
            return back();
        }

        $price_category = PriceCategory::find($request->price_category_id);
        $price_category->type = $request->code;
        $price_category->name = $request->name;
        try {
            $price_category->save();
            session()->flash("alert-success", "Price category updated successfully!");
            return back();
        } catch (Exception $exception) {
            session()->flash("alert-danger", "Price category exists!");
            return back();
        }

    }

    public function destroy(Request $request)
    {
        $check_existance = DB::table('sales')->where('price_category_id',$request->price_category_id)->count();

        if($check_existance > 0)
        {
            session()->flash("alert-danger", "Price Category is in use!");
            return back();
        }

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
