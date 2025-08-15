<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ProductsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $products;

    public function __construct($products)
    {
        $this->products = $products;
    }

    public function collection()
    {
        return $this->products;
    }

    public function headings(): array
    {
        return [
            'Product Name',
            'Brand',
            'Pack Size',
            'Category',
            'Type',
            'Status',
            'Min Stock',
            'Max Stock',
            'Created At'
        ];
    }

    public function map($product): array
    {
        return [
            $product->name,
            $product->brand,
            $product->pack_size,
            $product->category->name ?? 'N/A',
            $product->type,
            $product->status ? 'Active' : 'Inactive',
            $product->min_quantinty,
            $product->max_quantinty,
            $product->created_at->format('Y-m-d H:i:s')
        ];
    }
} 