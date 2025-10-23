<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Sale extends Model
{
    protected $table = 'sales';
    public $timestamps = false;

    public function details()
    {
        try {
            return $this->hasMany(SalesDetail::class, 'sale_id', 'id')
                ->leftJoin('inv_current_stock', 'inv_current_stock.id', '=', 'sales_details.stock_id')
                ->leftJoin('inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id')
                ->select('sales_details.id as id', 'inv_products.name', 'inv_products.brand', 'inv_products.pack_size', 'inv_products.sales_uom', 'sales_details.quantity as quantity', 'sales_details.price', 'sales_details.vat', 'sales_details.discount', 'sales_details.amount', 'sales_details.status')
                ->groupBy('sales_details.id', 'inv_products.name', 'inv_products.brand', 'inv_products.pack_size', 'inv_products.sales_uom', 'sales_details.quantity', 'sales_details.price', 'sales_details.vat', 'sales_details.discount', 'sales_details.amount', 'sales_details.status');
        } catch (\Exception $e) {
            Log::error('Error in Sale details relationship: ' . $e->getMessage());
            return collect(); // Return empty collection on error
        }
    }

    public function cost()
    {
        return $this->hasOne('App\SalesDetail', 'sale_id')
            ->join('sales', 'sales.id', '=', 'sales_details.sale_id')
            ->join('price_categories', 'price_categories.id', '=', 'sales.price_category_id')
            ->select('name', DB::raw('COALESCE(sum(discount),0.00) as discount'),
                DB::raw('COALESCE(sum(price),0.00) as sub_total'),
                DB::raw('COALESCE(sum(vat),0.00) as vat'),
                DB::raw('COALESCE(sum(amount),0.00) as amount')
            )
            ->groupBy('sale_id');
    }

    public function salesDetail()
    {
        return $this->hasMany(SalesDetail::class, 'sale_id');
    }

    public function user()
    {
       return $this->belongsTo(User::class, 'created_by');
    }

    public function customer()
    {
       return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function paymentType()
    {
        return $this->belongsTo(PaymentType::class);
    }

}
