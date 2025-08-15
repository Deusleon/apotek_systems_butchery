<?php

namespace App\Http\Controllers;

use App\Category;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function index()

    {
        $categories = Category::orderBy('id', 'DESC')->get();
        $count = $categories->count();
        return view('masters.categories.index', compact('categories', 'count'));
    }

    public function store(Request $request)
    {
        $existing = Category::where('name',$request->name)->count();

        if($existing > 0)
        {
            session()->flash("alert-danger", "Product Category Exists!");
            return back();
        }

        try {
            $category = new Category;
            $category->name = $request->name;
            $category->save();
            session()->flash("alert-success", "Product Category Added Successfully!");
            return back();
        } catch (Exception $exception) {
            session()->flash("alert-danger", "Product Category Exists!");
            return back();
        }

    }

    public function destroy(Request $request)
    {
        $check_existance = DB::table('inv_products')->where('category_id',$request->id)->count();

        if($check_existance > 0)
        {
            session()->flash("alert-danger", "Product Category is in use!");
            return back();
        }
        try {
            Category::destroy($request->id);
            session()->flash("alert-danger", "Product Category Deleted successfully!");
            return back();
        } catch (Exception $exception) {
            session()->flash("alert-danger", "Product Category in use!");
            return back();
        }

    }

    public function update(Request $request, $id)
    {
        $existing = Category::where('name',$request->name)->count();

        if($existing > 0)
        {
            session()->flash("alert-danger", "Product Category Exists!");
            return back();
        }

        $category = Category::find($request->id);
        $category->name = $request->name;
        try {
            $category->save();
            session()->flash("alert-success", "Product category updated successfully!");
            return back();
        } catch (Exception $exception) {
            session()->flash("alert-danger", "Product category exists!");
            return back();
        }
    }

}
