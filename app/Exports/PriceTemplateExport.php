<?php

namespace App\Exports;

use App\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PriceTemplateExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    public function collection()
    {
        // Get all products with their codes and names
        return Product::select('id', 'name')->get();
    }

    public function headings(): array
    {
        return [
            'code',
            'product name',
            'price'
        ];
    }

    public function map($product): array
    {
        return [
            $product->id,  // Using product ID as code
            $product->name,
            ''  // Empty price column for user to fill
        ];
    }
}