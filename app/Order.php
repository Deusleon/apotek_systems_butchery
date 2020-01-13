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
            ->select('order_details.order_id as order_id', 'inv_products.id as product_id', 'order_details.id as order_item_id', 'name', 'order_details.ordered_qty as quantity', 'unit_price as price', 'vat', 'discount', 'amount', 'item_status')
            ->groupBy('order_details.id');
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



