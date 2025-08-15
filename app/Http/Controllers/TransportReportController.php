<?php

namespace App\Http\Controllers;

use App\TransportOrder;
use App\Vehicle;
use App\Transporter;
use App\Payment;
use App\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PDF;
use DB;

ini_set('max_execution_time', 500);
set_time_limit(500);
ini_set('memory_limit', '512M');

class TransportReportController extends Controller
{
    public function index()
    {
        $orders = TransportOrder::all();
        $vehicles = Vehicle::all();
        $transporters = Transporter::all();
        
        return view('transport_reports.index', compact('orders', 'vehicles', 'transporters'));
    }

    public function generateReport(Request $request)
    {
        $request->validate([
            'report_option' => 'required|in:1,2,3,4'
        ]);

        try {
            switch ($request->report_option) {
                case '1': // Transport Order Report
                    return $this->generateTransportOrderReport($request);
                case '2': // Transporter Report
                    return $this->generateTransporterReport($request);
                case '3': // Vehicle Report
                    return $this->generateVehicleReport($request);
                case '4': // Payment Report
                    return $this->generatePaymentReport($request);
                default:
                    return redirect()->back()->with('error', 'Invalid report option selected');
            }
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error generating report: ' . $e->getMessage());
        }
    }

    protected function generateTransportOrderReport(Request $request)
    {
        // Get pharmacy settings
        $pharmacy = [
            'name' => Setting::where('id', 100)->value('value'),
            'logo' => storage_path('app/public/' . Setting::where('id', 105)->value('value')),
            'address' => Setting::where('id', 106)->value('value'),
            'email' => Setting::where('id', 108)->value('value'),
            'website' => Setting::where('id', 109)->value('value'),
            'phone' => Setting::where('id', 107)->value('value'),
            'tin_number' => Setting::where('id', 102)->value('value')
        ];
    
        // Check if logo exists
        if (!file_exists($pharmacy['logo'])) {
            $pharmacy['logo'] = null;
        }
    
        // Build query
        $query = TransportOrder::with(['transporter', 'vehicle', 'payments'])
            ->orderBy('pickup_date', 'desc');
    
        // Apply filters
        if ($request->transport_order_id) {
            $query->where('id', $request->transport_order_id);
        }
    
        if ($request->order_date_range) {
            $dates = explode(' - ', $request->order_date_range);
            $query->whereBetween('pickup_date', [Carbon::parse($dates[0]), Carbon::parse($dates[1])]);
        }
    
        $orders = $query->get();
    
        // Generate PDF
        $pdf = PDF::loadView('transport_reports.transport_orders', [
            'orders' => $orders,
            'pharmacy' => $pharmacy,
            'filter_order' => $request->transport_order_id ? TransportOrder::find($request->transport_order_id)->order_number : null,
            'filter_date_range' => $request->order_date_range
        ]);
    
        return $pdf->stream('transport-orders-'.now()->format('Y-m-d').'.pdf');
    }

    protected function generateTransporterReport(Request $request)
{
    // Get pharmacy settings
    $pharmacy = [
        'name' => Setting::where('id', 100)->value('value'),
        'logo' => storage_path('app/public/' . Setting::where('id', 105)->value('value')),
        'address' => Setting::where('id', 106)->value('value'),
        'email' => Setting::where('id', 108)->value('value'),
        'website' => Setting::where('id', 109)->value('value'),
        'phone' => Setting::where('id', 107)->value('value'),
        'tin_number' => Setting::where('id', 102)->value('value')
    ];

    // Check if logo exists
    if (!file_exists($pharmacy['logo'])) {
        $pharmacy['logo'] = null;
    }

    // Base query
    $query = Transporter::query()
        ->with(['transportOrders' => function($q) use ($request) {
            if ($request->transporter_date_range) {
                $dates = explode(' - ', $request->transporter_date_range);
                $q->whereBetween('pickup_date', [Carbon::parse($dates[0]), Carbon::parse($dates[1])]);
            }
        }])
        ->orderBy('name');

    // Apply filters
    if ($request->transporter_id) {
        $query->where('id', $request->transporter_id);
    }

    $transporters = $query->get()->map(function($transporter) {
        $transporter->total_orders = $transporter->transportOrders->count();
        $transporter->completed_orders = $transporter->transportOrders->where('status', 'delivered')->count();
        $transporter->total_revenue = $transporter->transportOrders->sum('transport_rate');
        return $transporter;
    });

    // Generate PDF
    $pdf = PDF::loadView('transport_reports.transporters_report', [
        'transporters' => $transporters,
        'pharmacy' => $pharmacy,
        'filter_transporter' => $request->transporter_id ? Transporter::find($request->transporter_id)->name : null,
        'filter_date_range' => $request->transporter_date_range
    ]);

    return $pdf->stream('transporters_report-'.now()->format('Y-m-d').'.pdf');
}

protected function generateVehicleReport(Request $request)
{
    // Get pharmacy settings
    $pharmacy = [
        'name' => Setting::where('id', 100)->value('value'),
        'logo' => storage_path('app/public/' . Setting::where('id', 105)->value('value')),
        'address' => Setting::where('id', 106)->value('value'),
        'email' => Setting::where('id', 108)->value('value'),
        'website' => Setting::where('id', 109)->value('value'),
        'phone' => Setting::where('id', 107)->value('value'),
        'tin_number' => Setting::where('id', 102)->value('value')
    ];

    // Check if logo exists
    if (!file_exists($pharmacy['logo'])) {
        $pharmacy['logo'] = null;
    }

    // Base query with correct relationship column
    $query = Vehicle::with(['transporter', 'transportOrders' => function($q) use ($request) {
        $q->orderBy('pickup_date', 'desc')->limit(5);
        if ($request->vehicle_date_range) {
            $dates = explode(' - ', $request->vehicle_date_range);
            $q->whereBetween('pickup_date', [Carbon::parse($dates[0]), Carbon::parse($dates[1])]);
        }
    }])
    ->orderBy('plate_number');

    // Apply filters - using vehicle_id from request
    if ($request->vehicle_id) {
        $query->where('id', $request->vehicle_id);
    }

    $vehicles = $query->get()->map(function($vehicle) {
        $vehicle->total_orders = $vehicle->transportOrders->count();
        $vehicle->completed_orders = $vehicle->transportOrders->where('status', 'delivered')->count();
        $vehicle->total_revenue = $vehicle->transportOrders->sum('transport_rate');
        return $vehicle;
    });

    // Generate PDF
    $pdf = PDF::loadView('transport_reports.vehicles_report', [
        'vehicles' => $vehicles,
        'pharmacy' => $pharmacy,
        'filter_vehicle' => $request->vehicle_id ? Vehicle::find($request->vehicle_id)->plate_number : null,
        'filter_date_range' => $request->vehicle_date_range
    ]);

    return $pdf->stream('vehicles_report-'.now()->format('Y-m-d').'.pdf');
}

   

    public function generatePaymentReport(Request $request)
{
    // Get filter parameters from request
    $filter_order = $request->input('order_number');
    $filter_date = $request->input('payment_date');
    
    // Query payments with filters
    $payments = Payment::query()
        ->when($filter_order, function($query) use ($filter_order) {
            return $query->whereHas('transportOrder', function($q) use ($filter_order) {
                $q->where('order_number', $filter_order);
            });
        })
        ->when($filter_date, function($query) use ($filter_date) {
            return $query->whereDate('payment_date', $filter_date);
        })
        ->with(['transportOrder', 'user'])
        ->get();

    // Get pharmacy settings
    $pharmacy = [
        'name' => Setting::where('id', 100)->value('value'),
        'logo' => storage_path('app/public/' . Setting::where('id', 105)->value('value')),
        'address' => Setting::where('id', 106)->value('value'),
        'email' => Setting::where('id', 108)->value('value'),
        'website' => Setting::where('id', 109)->value('value'),
        'phone' => Setting::where('id', 107)->value('value'),
        'tin_number' => Setting::where('id', 102)->value('value')
    ];

    // Check if logo exists
    if (!file_exists($pharmacy['logo'])) {
        $pharmacy['logo'] = null;
    }

    // Generate PDF
    $title = 'Payment Report';
    $pdf = PDF::loadView('transport_reports.payment_report', [
        'payments' => $payments,
        'pharmacy' => $pharmacy,
        'filter_order' => $filter_order,
        'filter_date' => $filter_date,
        'title' => $title
    ]);

    return $pdf->stream('payment_report-'.now()->format('Y-m-d').'.pdf');
}
}