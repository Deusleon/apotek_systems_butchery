<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DailyStockCountExport implements FromCollection, WithHeadings, WithMapping
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect($this->data);
    }

    public function headings(): array
    {
        return [
            'Product ID',
            'Product Name',
            'Brand',
            'Pack Size',
            'Sold Quantity',
            'Quantity On Hand',
            'Physical Stock',
            'Difference',
        ];
    }

    public function map($row): array
    {
        // Ensure all keys exist to prevent errors
        $product_id = $row['product_id'] ?? 'N/A';
        $product_name = $row['product_name'] ?? 'N/A';
        $brand = $row['brand'] ?? 'N/A';
        $pack_size = $row['pack_size'] ?? 'N/A';
        $quantity_sold = $row['quantity_sold'] ?? 0;
        $quantity_on_hand = $row['quantity_on_hand'] ?? 0;
        
        // For export, we might not have 'physical_stock' and 'difference' directly in $this->data
        // if the export is from the original daily stock count data.
        // If this export is for the discrepancy report, these values would be present.
        // For now, let's assume this export is for the data presented on the daily stock count screen,
        // where physical stock and difference are calculated client-side. 
        // We will need to re-evaluate this if the export is specifically for recorded discrepancies.
        
        // For now, setting them to N/A or 0 as they might not be directly available from the initial summation data
        // This needs clarification based on exactly what data should be exported.
        $physical_stock = $row['physical_stock'] ?? 'N/A'; 
        $difference = $row['difference'] ?? 'N/A';

        return [
            $product_id,
            $product_name,
            $brand,
            $pack_size,
            $quantity_sold,
            $quantity_on_hand,
            $physical_stock,
            $difference,
        ];
    }
} 