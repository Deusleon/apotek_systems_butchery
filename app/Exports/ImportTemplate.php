<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ImportTemplate implements FromArray, WithHeadings, ShouldAutoSize
{
    public function array(): array
    {
        // Return a sample row to show the format
        return [
            [
                '100001',        // Product Code
                'Sample name',   // Product Name
                'Sample Brand',  // Brand
                '100 tablets',   // Pack Size
                'tablets',       // Unit
                '10',           // Min Stock
                '100',          // Max Stock
                '1234567890',   // Barcode
                'Medicine',     // Category
                'stockable',    // Type
                '1',           // Status
                '2000.00',     // Buy Price
                '3000.00',     // Sell Price
                '30.00',       // Quantity
                'Jun/22'       // Expiry
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'Product Code*',
            'Product Name*',
            'Brand*',
            'Pack Size*',
            'Unit*',
            'Min Stock*',
            'Max Stock*',
            'Barcode',
            'Category',
            'Type (stockable/consumable)',
            'Status (1=active, 0=inactive)',
            'Buy Price*',
            'Sell Price*',
            'Quantity*',
            'Expiry*'
        ];
    }
} 