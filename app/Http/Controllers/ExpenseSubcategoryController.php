<?php

namespace App\Http\Controllers;
use App\AccExpenseSubCategory;
use App\AccExpenseCategory;
use Illuminate\Http\Request;
use View;
class ExpenseSubcategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       $expense_categories = AccExpenseCategory::orderBy('id', 'ASC')->get();
       $expense_subcategories = AccExpenseSubCategory::orderBy('id','ASC')->get();
        return View::make('masters.expense_subcategory.index')
        ->with(compact('expense_categories'))
        ->with(compact('expense_subcategories'));
    }


    public function store(Request $request)
    {
        $expense_subcategory = new AccExpenseSubCategory;
        $expense_subcategory->name = $request->name;
        $expense_subcategory->expense_category_id = $request->expense_category_id;
        $expense_subcategory->save();

        session()->flash("alert-success", "Expense subcategory added successfully!");
        return back();
    }


    public function update(Request $request)
    {
        $expense_subcategory = AccExpenseSubCategory::find($request->id);
        $expense_subcategory->name = $request->name;
        $expense_subcategory->expense_category_id = $request->expense_category_id;
        $expense_subcategory->save();

        session()->flash("alert-success", "Expense subcategory updated successfully!");
        return back();
    }


  
    public function destroy(Request $request)
    {
       AccExpenseSubCategory::destroy($request->id);
       session()->flash("alert-danger", "Expense subcategory deleted successfully!");
        return back();
    }
}
