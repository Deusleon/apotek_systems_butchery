<?php

namespace App\Http\Controllers;

use App\Category;
use Exception;
use Illuminate\Http\Request;

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
        $category = Category::find($request->id);
        $category->name = $request->name;
        $category->save();
        session()->flash("alert-success", "Product category updated successfully!");
        return back();
    }

}
