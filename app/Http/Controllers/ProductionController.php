<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use App\Production;
use App\ProductionDistribution;
use App\Store;

class ProductionController extends Controller

{

    public function index()

    {
        $stores = Store::where('id', '>', 1)->orderBy('name')->get();
        return view('production.index', compact('stores'));

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

    public function show($id)
    {
        $production = Production::find($id);
        if ($production) {
            return response()->json(['success' => true, 'data' => $production]);
        }
        return response()->json(['success' => false, 'message' => 'Production record not found'], 404);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'production_date' => 'required|date',
            'cows_received' => 'required|integer|min:1',
            'total_weight' => 'required|numeric|min:0',
            'meat_output' => 'required|numeric|min:0',
        ]);

        $production = Production::find($id);
        if ($production) {
            $production->update($request->all());
            return response()->json(['success' => true, 'message' => 'Production record updated successfully']);
        }
        return response()->json(['success' => false, 'message' => 'Production record not found'], 404);
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
                'id' => $production->id,
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

    public function getStores()
    {
        $stores = Store::where('id', '>', 1)->orderBy('name')->get();
        return response()->json(['success' => true, 'data' => $stores]);
    }

    public function getDistributions($id)
    {
        $production = Production::with('distributions.store')->find($id);
        if ($production) {
            return response()->json([
                'success' => true, 
                'data' => $production->distributions,
                'production' => $production
            ]);
        }
        return response()->json(['success' => false, 'message' => 'Production record not found'], 404);
    }

    public function storeDistributions(Request $request, $id)
    {
        $request->validate([
            'distributions' => 'required|array',
            'distributions.*.store_id' => 'required|exists:inv_stores,id',
            'distributions.*.meat_type' => 'required|string',
            'distributions.*.weight_distributed' => 'required|numeric|min:0',
        ]);

        $production = Production::find($id);
        if (!$production) {
            return response()->json(['success' => false, 'message' => 'Production record not found'], 404);
        }

        DB::beginTransaction();
        try {
            // Delete existing distributions for this production
            ProductionDistribution::where('production_id', $id)->delete();

            // Insert new distributions
            foreach ($request->distributions as $dist) {
                ProductionDistribution::create([
                    'production_id' => $id,
                    'store_id' => $dist['store_id'],
                    'meat_type' => $dist['meat_type'],
                    'weight_distributed' => $dist['weight_distributed'],
                ]);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Distributions saved successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving distributions: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error saving distributions'], 500);
        }
    }

    public function distributionsReport()
    {
        $stores = Store::where('id', '>', 1)->orderBy('name')->get();
        return view('production.distributions', compact('stores'));
    }

    public function distributionsData(Request $request)
    {
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $store_id = $request->input('store_id');
        $meat_type = $request->input('meat_type');
        $search_value = $request->input('search.value');

        $query = ProductionDistribution::with(['production', 'store'])
            ->whereHas('production', function($q) use ($start_date, $end_date) {
                $q->whereBetween('production_date', [$start_date, $end_date]);
            });

        if ($store_id) {
            $query->where('store_id', $store_id);
        }

        if ($meat_type) {
            $query->where('meat_type', $meat_type);
        }

        if ($search_value) {
            $query->where(function($q) use ($search_value) {
                $q->where('meat_type', 'like', '%' . $search_value . '%')
                  ->orWhereHas('store', function($sq) use ($search_value) {
                      $sq->where('name', 'like', '%' . $search_value . '%');
                  });
            });
        }

        $distributions = $query->orderBy('created_at', 'desc')->get();

        $data = [];
        foreach ($distributions as $dist) {
            $data[] = [
                'id' => $dist->id,
                'production_date' => $dist->production ? date('Y-m-d', strtotime($dist->production->production_date)) : '',
                'store_name' => $dist->store ? $dist->store->name : 'Unknown',
                'meat_type' => $dist->meat_type,
                'weight_distributed' => number_format($dist->weight_distributed, 2),
                'production_id' => $dist->production_id,
            ];
        }

        // Calculate summary
        $summaryQuery = ProductionDistribution::whereHas('production', function($q) use ($start_date, $end_date) {
            $q->whereBetween('production_date', [$start_date, $end_date]);
        });

        if ($store_id) {
            $summaryQuery->where('store_id', $store_id);
        }
        if ($meat_type) {
            $summaryQuery->where('meat_type', $meat_type);
        }

        $summary = [
            'total_distributions' => $summaryQuery->count(),
            'total_weight' => $summaryQuery->sum('weight_distributed'),
            'branches_served' => $summaryQuery->distinct('store_id')->count('store_id'),
            'total_productions' => $summaryQuery->distinct('production_id')->count('production_id'),
        ];

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => count($data),
            'recordsFiltered' => count($data),
            'data' => $data,
            'summary' => $summary
        ]);
    }

    public function deleteDistribution($id)
    {
        $distribution = ProductionDistribution::find($id);
        if ($distribution) {
            $distribution->delete();
            return response()->json(['success' => true, 'message' => 'Distribution deleted successfully']);
        }
        return response()->json(['success' => false, 'message' => 'Distribution not found'], 404);
    }
}