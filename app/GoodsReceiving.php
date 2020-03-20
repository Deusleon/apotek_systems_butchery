<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class GoodsReceiving extends Model
{
    use LogsActivity;

    protected $table = 'inv_incoming_stock';
    public $timestamps = 'false';
    protected $fillable = ['id', 'product_id', 'quantity', 'unit_cost', 'total_cost', 'supplier_id',
        'expire_date', 'total_sell', 'item_profit', 'sell_price', 'created_by', 'created_at'];

    protected static $logAttributes = ['id', 'product_id', 'quantity', 'unit_cost', 'total_cost', 'supplier_id',
        'expire_date', 'total_sell', 'item_profit', 'sell_price', 'created_by', 'created_at'];


    public function supplier()
    {

        return $this->belongsTo('App\Supplier');
    }

    public function product()
    {

        return $this->belongsTo(Product::class, 'product_id');
    }

    public function invoice()
    {

        return $this->belongsTo(Invoice::class, 'invoice_no');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

}

