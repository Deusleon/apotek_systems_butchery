<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ProductionDistribution;
use App\Store;
use App\Setting;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\DB;

class DistributionReportController extends Controller
{
    public function index()
    {
        $stores = Store::where('id', '>', 1)->orderBy('name')->get();
        return view('distribution_reports.index', compact('stores'));
    }

    public function filter(Request $request)
    {
        $date_range = explode('-', $request->date_range);
        $from = trim($date_range[0]);
        $to = trim($date_range[1]);
        $store_id = $request->store_id;

        $pharmacy['name'] = Setting::where('id', 100)->value('value');
        $pharmacy['logo'] = Setting::where('id', 105)->value('value');
        $pharmacy['address'] = Setting::where('id', 106)->value('value');
        $pharmacy['email'] = Setting::where('id', 108)->value('value');
        $pharmacy['website'] = Setting::where('id', 109)->value('value');
        $pharmacy['phone'] = Setting::where('id', 107)->value('value');
        $pharmacy['tin_number'] = Setting::where('id', 102)->value('value');
        $pharmacy['from_date'] = date('Y-m-d', strtotime($from));
        $pharmacy['to_date'] = date('Y-m-d', strtotime($to));

        $data = $this->getDistributions($from, $to, $store_id);
        
        if (empty($data['dateGroups'])) {
            return response()->view('error_pages.pdf_zero_data');
        }

        $stores = Store::where('id', '>', 1)->orderBy('name')->get();
        $selectedStore = $store_id ? Store::find($store_id) : null;

        $pdf = PDF::loadView('distribution_reports.report_pdf', 
            compact('data', 'pharmacy', 'stores', 'selectedStore'))
            ->setPaper('a4', 'landscape');
        
        return $pdf->stream('Distribution_Report.pdf');
    }

    private function getDistributions($from, $to, $store_id = null)
    {
        // Get all stores (branches)
        $stores = Store::where('id', '>', 1)->orderBy('name')->get();
        
        // Get distributions grouped by date and recipient
        $query = ProductionDistribution::with(['production', 'store', 'customer'])
            ->whereHas('production', function($q) use ($from, $to) {
                $q->whereBetween('production_date', [$from, $to]);
            });

        if ($store_id) {
            $query->where('store_id', $store_id);
        }

        $distributions = $query->get();

        // Group by date and recipient (using a unique key)
        $groupedData = [];
        foreach ($distributions as $dist) {
            $date = date('Y-m-d', strtotime($dist->production->production_date));
            $distributionType = $dist->distribution_type ?? 'branch';
            
            // Create unique key for grouping
            $recipientKey = $distributionType . '_';
            switch ($distributionType) {
                case 'branch':
                    $recipientKey .= $dist->store_id ?? 'unknown';
                    $recipientName = $dist->store ? $dist->store->name : 'Unknown Branch';
                    break;
                case 'cash_sale':
                    $recipientKey .= $dist->customer_id ?? 'cash_sale_' . $dist->id;
                    $recipientName = $dist->customer ? $dist->customer->name : 'Cash Sale';
                    break;
                case 'order':
                    $recipientKey .= $dist->order_to ?? 'order_' . $dist->id;
                    $recipientName = $dist->order_to ? 'Order: ' . $dist->order_to : 'Order';
                    break;
                default:
                    $recipientKey .= 'order_' . $dist->id;
                    $recipientName = 'Order';
            }
            
            $meatType = $dist->meat_type;
            $weight = floatval($dist->weight_distributed);

            if (!isset($groupedData[$date])) {
                $groupedData[$date] = [];
            }
            if (!isset($groupedData[$date][$recipientKey])) {
                $groupedData[$date][$recipientKey] = [
                    'distribution_type' => $distributionType,
                    'recipient_name' => $recipientName,
                    'meat' => 0,
                    'steak' => 0,
                    'beef_fillet' => 0,
                    'beef_liver' => 0,
                    'tripe' => 0,
                ];
            }

            // Map meat types to columns
            switch (strtolower($meatType)) {
                case 'meat':
                    $groupedData[$date][$recipientKey]['meat'] += $weight;
                    break;
                case 'steak':
                    $groupedData[$date][$recipientKey]['steak'] += $weight;
                    break;
                case 'fillet':
                case 'beef fillet':
                    $groupedData[$date][$recipientKey]['beef_fillet'] += $weight;
                    break;
                case 'beef liver':
                    $groupedData[$date][$recipientKey]['beef_liver'] += $weight;
                    break;
                case 'tripe':
                    $groupedData[$date][$recipientKey]['tripe'] += $weight;
                    break;
            }
        }

        // Sort dates in ascending order
        ksort($groupedData);

        // Structure data grouped by date for the report
        $dateGroups = [];
        $totals = [
            'meat' => 0,
            'steak' => 0,
            'beef_fillet' => 0,
            'beef_liver' => 0,
            'tripe' => 0,
        ];

        foreach ($groupedData as $date => $recipientData) {
            $dateGroups[$date] = [
                'rows' => [],
                'totals' => [
                    'meat' => 0,
                    'steak' => 0,
                    'beef_fillet' => 0,
                    'beef_liver' => 0,
                    'tripe' => 0,
                ],
            ];

            foreach ($recipientData as $recipientKey => $data) {
                $dateGroups[$date]['rows'][] = [
                    'distribution_type' => $data['distribution_type'],
                    'recipient_name' => $data['recipient_name'],
                    'meat' => $data['meat'],
                    'steak' => $data['steak'],
                    'beef_fillet' => $data['beef_fillet'],
                    'beef_liver' => $data['beef_liver'],
                    'tripe' => $data['tripe'],
                ];

                // Date totals
                $dateGroups[$date]['totals']['meat'] += $data['meat'];
                $dateGroups[$date]['totals']['steak'] += $data['steak'];
                $dateGroups[$date]['totals']['beef_fillet'] += $data['beef_fillet'];
                $dateGroups[$date]['totals']['beef_liver'] += $data['beef_liver'];
                $dateGroups[$date]['totals']['tripe'] += $data['tripe'];

                // Grand totals
                $totals['meat'] += $data['meat'];
                $totals['steak'] += $data['steak'];
                $totals['beef_fillet'] += $data['beef_fillet'];
                $totals['beef_liver'] += $data['beef_liver'];
                $totals['tripe'] += $data['tripe'];
            }
        }

        return [
            'dateGroups' => $dateGroups,
            'totals' => $totals,
        ];
    }
}
