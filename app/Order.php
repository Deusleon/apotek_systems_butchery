<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';
    public $timestamps = false;

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function details()
        {
            return $this->hasMany(OrderDetail::class, 'order_id', 'id')
                ->join('inv_products', 'inv_products.id', '=', 'order_details.product_id')
                ->select(
                    'order_details.id as order_item_id',
                    'order_details.order_id',
                    'order_details.product_id',
                    'inv_products.name',
                    'inv_products.brand',
                    'inv_products.pack_size',
                    'inv_products.sales_uom',
                    'order_details.ordered_qty',
                    'order_details.received_qty',
                    'order_details.unit_price as price',
                    'order_details.vat',
                    'order_details.amount'
                )
                ->groupBy(
                    'order_details.id',
                    'order_details.order_id',
                    'order_details.product_id',
                    'inv_products.name',
                    'inv_products.pack_size',
                    'order_details.ordered_qty',
                    'order_details.received_qty',
                    'order_details.unit_price',
                    'order_details.vat',
                    'order_details.amount'
                );
        }

    public function orderDetail()
    {
        return $this->hasMany(OrderDetail::class, 'order_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'ordered_by');
    }
}