<?php

namespace App\Http\Controllers;

use App\Location;
use App\StockIssue;
use Illuminate\Support\Facades\DB;

class RePrintIssueController extends Controller
{


    public function index()
    {

        $all_issues = StockIssue::select(DB::raw('sum(quantity) as quantity'), 'issue_no', 'issued_to', 'created_at')
            ->groupby('issue_no', 'issued_to', 'created_at')
            ->get();
        $locations = Location::all();

        return view('stock_management.re_print_issue.index')->with([
            'all_issues' => $all_issues,
            'locations' => $locations
        ]);

    }


}
