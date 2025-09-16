<?php

namespace App\Http\Controllers;

use App\AccExpenseCategory;
use App\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{

    public function index()
    {

        $expense_category = AccExpenseCategory::all();

        return view('expense.index')->with([
            "expense_categories" => $expense_category
        ]);
    }

    public function store(Request $request)
    {
        $expense = new Expense;
        $expense->amount = str_replace(',', '', $request->expense_amount);
        $expense->expense_category_id = $request->expense_category;
        $expense->expense_description = $request->expense_description;
        $expense->payment_method_id = $request->payment_method;
        $expense->created_at = date('Y-m-d', strtotime($request->expense_date));
        $expense->updated_by = Auth::user()->id;
        $expense->store_id = Auth::user()->store_id;
        $expense->save();

        session()->flash("alert-success", "Expense added successfully!");
        return back();
    }

    public function update(Request $request)
    {
        $expense = Expense::find($request->expense_id);
        $expense->amount = str_replace(',', '', $request->expense_amount_edit);
        $expense->expense_category_id = $request->expense_category_edit;
        $expense->expense_description = $request->expense_description_edit;
        $expense->payment_method_id = $request->payment_method_edit;
        $expense->created_at = date('Y-m-d', strtotime($request->expense_date_edit));
        $expense->updated_by = Auth::user()->id;
        $expense->store_id = Auth::user()->store_id;
        $expense->save();

        session()->flash("alert-success", "Expense updated successfully!");
        return back();
    }

    public function destroy(Request $request)
    {
        Expense::destroy($request->expense_id);
        session()->flash("alert-danger", "Expense deleted successfully!");
        return back();
    }

    public function filterExpenseDate(Request $request)
    {
        if ($request->ajax()) {

            $results = $this->searchRecentDate(date('Y-m-d', strtotime($request->from_date)),
                date('Y-m-d', strtotime($request->to_date)));
            $results_mod = array();
            $to_table = array();

            foreach ($results[0] as $result) {
                if ($result->payment_method_id == 1)
                    $method = "CASH";
                else
                    $method = "BILL";
                array_push($results_mod, array(
                    "id" => $result->id,
                    "created_at" => date('Y-m-d', strtotime($result->created_at)),
                    "expense_Category" => $result->accExpenseCategory['name'],
                    "expense_category_id" => $result->expense_category_id,
                    "description" => $result->expense_description,
                    "amount" => $result->amount,
                    "payment_method" => $method,
                    "payment_method_id" => $result->payment_method_id,
                    "user" => $result->user['name']
                ));
            }

            array_push($to_table, array(
                $results_mod, $results[1],
            ));


            return $to_table;

        }
    }

    public function searchRecentDate($first_date, $last_date)
    {
        $total = 0;

        //by default return todays month expenses
        $expense = Expense::whereBetween(DB::raw('date(created_at)'), [$first_date, $last_date])->get();
        foreach ($expense as $item) {
            $total = $total + $item->amount;
        }

        return array($expense, $total, $first_date, $last_date);

    }

}
