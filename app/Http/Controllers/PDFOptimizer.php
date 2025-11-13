<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade as PDF;

class PDFOptimizer
{
    // Increase limits for PDF generation
    public static function initializePdfLimits()
    {
        ini_set('max_execution_time', 1800); // 30 minutes
        set_time_limit(1800);
        ini_set('memory_limit', '1536M'); // 1.5GB
        
        // Clear any output buffers
        if (ob_get_level()) {
            ob_end_clean();
        }
        
        // Force garbage collection
        if (function_exists('gc_collect_cycles')) {
            gc_collect_cycles();
        }
    }
    
    /**
     * Optimized PDF generation for large datasets
     */
    public static function generateOptimizedPDF($view, $data, $filename = 'report.pdf', $options = [])
    {
        try {
            self::initializePdfLimits();
            
            $defaultOptions = [
                'paper' => 'a4',
                'orientation' => '',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
                'dpi' => 150, // Lower DPI for faster generation
                'defaultFont' => 'dejavusans',
                'tempDir' => storage_path('app/temp'),
            ];
            
            $pdfOptions = array_merge($defaultOptions, $options);
            
            // Start timer
            $startTime = microtime(true);
            
            // Generate PDF
            $pdf = PDF::loadView($view, compact(array_keys($data)));
            $pdf->setPaper($pdfOptions['paper'], $pdfOptions['orientation']);
            
            // Apply PDF options for performance
            $pdf->setOptions([
                'isHtml5ParserEnabled' => $pdfOptions['isHtml5ParserEnabled'],
                'isRemoteEnabled' => $pdfOptions['isRemoteEnabled'],
                'dpi' => $pdfOptions['dpi'],
                'defaultFont' => $pdfOptions['defaultFont'],
                'tempDir' => $pdfOptions['tempDir'],
                'chroot' => base_path(),
            ]);
            
            // Force output to browser
            $pdf->output();
            
            // Calculate performance
            $duration = microtime(true) - $startTime;
            $memoryUsage = round(memory_get_peak_usage(true) / 1024 / 1024, 2);
            
            // Log performance
            Log::info("Optimized PDF Generated", [
                'filename' => $filename,
                'duration' => round($duration, 2) . ' seconds',
                'memory_peak_mb' => $memoryUsage,
                'data_records' => isset($data['data']) ? count($data['data']) : 'N/A',
                'view' => $view
            ]);
            
            return $pdf->stream($filename);
            
        } catch (\Exception $e) {
            Log::error("Optimized PDF generation failed: " . $e->getMessage(), [
                'view' => $view,
                'filename' => $filename,
                'exception' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }
    
    /**
     * Process large data arrays in chunks
     */
    public static function processInChunks($data, $chunkSize = 1000, $callback)
    {
        if (empty($data)) {
            return [];
        }
        
        $chunks = array_chunk($data, $chunkSize);
        $processed = [];
        
        foreach ($chunks as $chunk) {
            $result = $callback($chunk);
            $processed = array_merge($processed, $result);
            
            // Force garbage collection between chunks
            if (function_exists('gc_collect_cycles')) {
                gc_collect_cycles();
            }
        }
        
        return $processed;
    }
    
    /**
     * Memory-efficient data transformation
     */
    public static function transformData($items, $transformCallback, $chunkSize = 1000)
    {
        if (empty($items)) {
            return [];
        }
        
        return self::processInChunks($items, $chunkSize, function($chunk) use ($transformCallback) {
            $result = [];
            foreach ($chunk as $item) {
                $result[] = $transformCallback($item);
            }
            return $result;
        });
    }
    
    /**
     * Get optimized query with limits for large datasets
     */
    public static function optimizeQuery($query, $limit = null, $offset = 0)
    {
        if ($limit) {
            $query->limit($limit);
        }
        
        if ($offset > 0) {
            $query->offset($offset);
        }
        
        return $query;
    }
    
    /**
     * Check if dataset is too large and suggest chunking
     */
    public static function checkDatasetSize($query, $threshold = 10000)
    {
        try {
            $countQuery = clone $query;
            $count = $countQuery->count();
            
            if ($count > $threshold) {
                Log::warning("Large dataset detected", [
                    'record_count' => $count,
                    'threshold' => $threshold,
                    'suggestion' => 'Consider using chunk processing'
                ]);
                
                return [
                    'too_large' => true,
                    'count' => $count,
                    'suggest_chunk_size' => ceil($count / 10) // Suggest 10 chunks
                ];
            }
            
            return ['too_large' => false, 'count' => $count];
            
        } catch (\Exception $e) {
            Log::warning("Failed to estimate dataset size: " . $e->getMessage());
            return ['too_large' => false, 'count' => 0];
        }
    }
}