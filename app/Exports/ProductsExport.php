<?php

namespace App\Exports;

use App\Product;
use App\CurrentStock;
use App\PriceList;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ProductsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $products;
    protected $priceCategoryId;
    protected $storeId;

    public function __construct($products, $priceCategoryId = null, $storeId = null)
    {
        $this->products = $products;
        $this->priceCategoryId = $priceCategoryId;
        $this->storeId = $storeId;
    }

    public function collection()
    {
        return $this->products;
    }

    public function headings(): array
    {
        return [
            'Code',
            'Product Name',
            'Quantity',
            'Buy Price',
            'Sell Price'
        ];
    }

    public function map($product): array
    {
        // Get current stock for the specific store
        $currentStock = CurrentStock::where('product_id', $product->id)
            ->where('store_id', $this->storeId)
            ->first();
        $quantity = $currentStock ? $currentStock->quantity : 0;

        // Get buy price from current stock
        $buyPrice = $currentStock ? $currentStock->unit_cost : 0;

        // Get sell price from price list based on selected price category
        $sellPrice = 0;
        if ($this->priceCategoryId && $currentStock) {
            $priceList = PriceList::where('stock_id', $currentStock->id)
                ->where('price_category_id', $this->priceCategoryId)
                ->first();
            $sellPrice = $priceList ? $priceList->price : 0;
        }

        return [
            $product->id,
            $product->name,
            $quantity,
            number_format($buyPrice, 2, '.', ''),
            number_format($sellPrice, 2, '.', '')
        ];
    }
}