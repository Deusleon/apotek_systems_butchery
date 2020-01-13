<?php

namespace App\Http\Controllers;

use App\Category;
use App\SubCategory;
use Illuminate\Http\Request;
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
     $subcategories = SubCategory::find($request->id);
     $subcategories->category_id= $request->category_id;
     $subcategories->name = $request->subcategory_name;
     $subcategories->save();

      session()->flash("alert-success", "Product Subcategory Updated Successfully!");
        return back();
    }

    public function destroy(Request $request)
    {
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
