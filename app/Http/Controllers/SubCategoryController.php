<?php

namespace App\Http\Controllers;

use App\Category;
use App\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use View;

class SubCategoryController extends Controller
{
    //
    public function index()

    {

     $subcategories = SubCategory::orderBy('id', 'DESC')->get();
        $count = $subcategories->count();
     $categories = Category::orderBy('id','DESC')->get();
    	return View::make('masters.sub_categories.index')
            ->with(compact('subcategories'))
            ->with(compact('categories', 'count'));
    }

    public function store(Request $request)
    {
        $existing = SubCategory::where('name',$request->subcategory_name)->count();

        if($existing > 0)
        {
            session()->flash("alert-danger", "Product Subcategory Exists!");
            return back();
        }

        // dd($request);
        $subcategories = new SubCategory;
        $subcategories->category_id = $request->category_id;
        $subcategories->name= $request->subcategory_name;
        $subcategories->save();

        session()->flash("alert-success", "Product Subcategory Added Successfully!");
        return back();
    }


    public function update(Request $request)
    {

        $existing = SubCategory::where('name',$request->subcategory_name)->count();

        if($existing > 0)
        {
            session()->flash("alert-danger", "Product Subcategory Exists!");
            return back();
        }
        $subcategories = SubCategory::find($request->id);
        $subcategories->category_id= $request->category_id;
        $subcategories->name = $request->subcategory_name;
        $subcategories->save();

      session()->flash("alert-success", "Product Subcategory Updated Successfully!");
        return back();
    }

    public function destroy(Request $request)
    {
        $check_existance = DB::table('inv_products')->where('sub_category_id',$request->id)->count();

        if($check_existance > 0)
        {
            session()->flash("alert-danger", "Product Subcategory is in use!");
            return back();
        }
        try {
            Subcategory::destroy($request->id);
            session()->flash("alert-danger", "Product Subcategory Deleted successfully!");
            return back();
        } catch (\Exception $exception) {
            session()->flash("alert-danger", "Product Subcategory in use!");
            return back();
        }

    }

}
