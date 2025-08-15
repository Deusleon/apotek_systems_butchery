<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $table = 'order_details';
    public $timestamps = false;

    /**
     * The attributes that should be appended to the model's array form.
     *
     * @var array
     */
    protected $appends = ['quantity', 'received_quantity'];

    /**
     * Accessor for the 'quantity' attribute.
     *
     * @return mixed
     */
    public function getQuantityAttribute()
    {
        return $this->ordered_qty;
    }

    /**
     * Accessor for the 'received_quantity' attribute.
     *
     * @return mixed
     */
    public function getReceivedQuantityAttribute()
    {
        return $this->received_qty;
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }


    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
