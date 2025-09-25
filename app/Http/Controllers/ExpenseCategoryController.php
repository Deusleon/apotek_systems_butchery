<?php

namespace App\Http\Controllers;

use App\AccExpenseCategory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseCategoryController extends Controller
{

    public function index()
    {
        $expense_categories = AccExpenseCategory::orderBy('id', 'ASC')->get();
        $count = $expense_categories->count();
        foreach ( $expense_categories as $category ) {
            $category_count = DB::table( 'acc_expenses' )->where( 'expense_category_id', $category->id )->count();

            if ( $category_count > 0 ) {
                $category[ 'is_used' ] = 'yes';
            }

            if ( $category_count == 0 ) {
                $category[ 'is_used' ] = 'no';
            }

        }
        return view('masters.expense_category.index', compact("expense_categories", 'count'));

    }

    public function store(Request $request)
    {
        try {
            $expense_category = new AccExpenseCategory;
            $expense_category->name = $request->name;
            $expense_category->save();

            session()->flash("alert-success", "Expense Category Added Successfully!");
            return back();
        } catch (Exception $exception) {
            session()->flash("alert-danger", "Expense Category Exists!");
            return back();
        }
    }


    public function update(Request $request)
    {
        $expense_category = AccExpenseCategory::find($request->id);
        $expense_category->name = $request->name;
        try {
            $expense_category->save();
            session()->flash("alert-success", "Expense category updated successfully!");
            return back();
        } catch (Exception $exception) {
            session()->flash("alert-danger", "Expense category exists!");
            return back();
        }

    }


    public function destroy(Request $request)
    {

        try {
            AccExpenseCategory::find($request->id)->delete();
            session()->flash("alert-danger", "Expense category deleted Successfully!");
            return back();
        } catch (Exception $e) {
            session()->flash("alert-danger", "Expense category in use");
            return back();
        }

    }
}
