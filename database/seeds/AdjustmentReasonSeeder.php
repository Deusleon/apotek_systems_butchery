<?php

use Illuminate\Database\Seeder;
use App\AdjustmentReason;

class AdjustmentReasonSeeder extends Seeder
{
    public function run()
    {
        $reasons = [
            'Damaged Stock',
            'Expired Stock',
            'Stock Count Adjustment',
            'Quality Control',
            'Theft/Loss',
            'System Error Correction',
            'Returned to Supplier',
            'Sample Use',
            'Internal Use',
            'Other'
        ];

        foreach ($reasons as $reason) {
            AdjustmentReason::create(['reason' => $reason]);
        }
    }
} 