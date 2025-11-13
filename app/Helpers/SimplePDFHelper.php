<?php

namespace App\Helpers;

use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\Log;

class SimplePDFHelper
{
    public static function generate($view, $data, $filename = 'report.pdf')
    {
        // Store original limits
        $originalMemory = ini_get('memory_limit');
        $originalTime = ini_get('max_execution_time');
        
        try {
            // Set very high limits
            ini_set('memory_limit', '4096M');
            set_time_limit(3600);
            
            // Clear buffers
            while (ob_get_level()) {
                ob_end_clean();
            }
            
            // Garbage collection
            if (function_exists('gc_collect_cycles')) {
                gc_collect_cycles();
            }
            
            $startTime = microtime(true);
            
            $pdf = PDF::loadView($view, $data);
            $pdf->setPaper('a4', '');
            
            // Optimized settings
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
                'dpi' => 150,
                'defaultFont' => 'dejavusans',
            ]);
            
            $pdf->stream($filename);
            
            // Log performance
            $duration = round(microtime(true) - $startTime, 2);
            Log::info('PDF Generated', [
                'file' => $filename,
                'duration' => $duration . 's',
                'memory' => round(memory_get_peak_usage(true) / 1024 / 1024, 2) . 'MB'
            ]);
            
        } catch (\Exception $e) {
            Log::error('PDF Generation Failed: ' . $e->getMessage());
            throw $e;
        } finally {
            // Restore limits
            ini_set('memory_limit', $originalMemory);
            set_time_limit($originalTime);
        }
    }
}