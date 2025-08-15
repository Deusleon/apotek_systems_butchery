<?php

namespace App\Http\Controllers;

use App\StockCountSchedule;
use App\Store;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockCountScheduleController extends Controller
{
    public function index()
    {
        $schedules = StockCountSchedule::with(['store', 'creator'])
            ->orderBy('schedule_date', 'desc')
            ->get();

        return view('stock_management.stock_count_schedules.index', compact('schedules'));
    }

    public function create()
    {
        $stores = Store::pluck('name', 'id');
        return view('stock_management.stock_count_schedules.create', compact('stores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'schedule_date' => 'required|date',
            'store_id' => 'required|exists:inv_stores,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            StockCountSchedule::create([
                'schedule_date' => $request->input('schedule_date'),
                'store_id' => $request->input('store_id'),
                'notes' => $request->input('notes'),
                'created_by' => Auth::id(),
                'status' => 'pending',
            ]);

            DB::commit();
            return redirect()->route('stock-count-schedules.index')->with('success', 'Stock count schedule created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Failed to create stock count schedule: ' . $e->getMessage());
        }
    }

    public function edit(StockCountSchedule $stockCountSchedule)
    {
        $stores = Store::pluck('name', 'id');
        return view('stock_management.stock_count_schedules.edit', compact('stockCountSchedule', 'stores'));
    }

    public function update(Request $request, StockCountSchedule $stockCountSchedule)
    {
        $request->validate([
            'schedule_date' => 'required|date',
            'store_id' => 'required|exists:inv_stores,id',
            'notes' => 'nullable|string|max:1000',
            'status' => 'required|in:pending,completed,cancelled',
        ]);

        DB::beginTransaction();
        try {
            $stockCountSchedule->update([
                'schedule_date' => $request->input('schedule_date'),
                'store_id' => $request->input('store_id'),
                'notes' => $request->input('notes'),
                'status' => $request->input('status'),
            ]);

            DB::commit();
            return redirect()->route('stock-count-schedules.index')->with('success', 'Stock count schedule updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Failed to update stock count schedule: ' . $e->getMessage());
        }
    }

    public function destroy(StockCountSchedule $stockCountSchedule)
    {
        DB::beginTransaction();
        try {
            $stockCountSchedule->delete();
            DB::commit();
            return redirect()->route('stock-count-schedules.index')->with('success', 'Stock count schedule deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to delete stock count schedule: ' . $e->getMessage());
        }
    }

    public function approve(StockCountSchedule $stockCountSchedule)
    {
        if (!Auth::user()->can('approve stock count schedules')) {
            return redirect()->back()->with('error', 'You do not have permission to approve stock count schedules.');
        }

        DB::beginTransaction();
        try {
            $stockCountSchedule->update([
                'status' => 'completed',
            ]);

            DB::commit();
            return redirect()->route('stock-count-schedules.index')->with('success', 'Stock count schedule approved successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to approve stock count schedule: ' . $e->getMessage());
        }
    }
} 