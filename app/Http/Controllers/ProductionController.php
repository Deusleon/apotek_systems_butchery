<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Production;

class ProductionController extends Controller

{

    public function index()

    {

        return view('production.index');

    }

    public function store(Request $request)

    {

        $request->validate([

            'production_date' => 'required|date',

            'cows_received' => 'required|integer|min:1',

            'total_weight' => 'required|numeric|min:0',

            'meat_output' => 'required|numeric|min:0',

        ]);

        Production::create($request->all());

        return response()->json(['success' => true, 'message' => 'Production record saved successfully']);

    }

    public function data(Request $request)

    {
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $search_value = $request->input('search.value');
        
        if ($search_value) {
            $productions = Production::whereBetween('production_date', [$start_date, $end_date])
                ->where(function($query) use ($search_value) {
                    $query->where('production_date', 'like', '%' . $search_value . '%')
                          ->orWhere('cows_received', 'like', '%' . $search_value . '%')
                          ->orWhere('total_weight', 'like', '%' . $search_value . '%')
                          ->orWhere('meat_output', 'like', '%' . $search_value . '%');
                })
                ->orderBy('production_date', 'desc')
                ->get();
        } else {
        $productions = Production::whereBetween('production_date', [$start_date, $end_date])
            ->orderBy('production_date', 'desc')
            ->get();
        }
        $data = [];

        foreach ($productions as $production) {

            $yield = 0;

            if ($production->total_weight > 0) {

                $yield = ($production->meat_output / $production->total_weight) * 100;

            }

            $data[] = [

                'production_date' => date('Y-m-d', strtotime($production->production_date)),

                'cows_received' => $production->cows_received,

                'total_weight' => number_format($production->total_weight, 2),

                'meat_output' => number_format($production->meat_output, 2),

                'yield' => number_format($yield, 2) . '%',

                'actions' => '<button class="btn btn-sm btn-info view-btn" data-id="' . $production->id . '"><i class="feather icon-eye"></i></button> 
                             <button class="btn btn-sm btn-danger delete-btn" data-id="' . $production->id . '"><i class="feather icon-trash"></i></button>'

            ];

        }

        return response()->json([

            'draw' => intval($request->draw),

            'recordsTotal' => count($data),

            'recordsFiltered' => count($data),

            'data' => $data

        ]);

    }

    public function destroy($id)

    {

        $production = Production::find($id);

        if ($production) {

            $production->delete();

            return response()->json(['success' => true, 'message' => 'Production record deleted successfully']);

        }

        return response()->json(['success' => false, 'message' => 'Production record not found'], 404);

    }

}