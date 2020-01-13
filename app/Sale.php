<?php

namespace App;

use DB;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $table = 'sales';
    public $timestamps = false;

    public function details()
    {
        return $this->hasMany(SalesDetail::class, 'sale_id', 'id')
            ->join('inv_current_stock', 'inv_current_stock.id', '=', 'sales_details.stock_id')
            ->join('inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id')
            ->select('sales_details.id as id', 'name', 'sales_details.quantity as quantity', 'price', 'vat', 'discount', 'amount', 'sales_details.status')
            ->groupBy('sales_details.id');
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
        return $this->hasMany(SalesDetail::class, 'sales_id');
    }

    public function user()
    {
       return $this->belongsTo(User::class, 'created_by');
    }

    public function customer()
    {
       return $this->belongsTo(Customer::class, 'customer_id');
    }

}
