<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StockTracking extends Model
{

    protected $table = 'inv_stock_tracking';
    public $timestamps = false;

    public function currentStock()
    {
        return $this->belongsTo(CurrentStock::class, 'stock_id');
    }

    public function user()
    {
	    return $this->belongsTo(User::class,'updated_by');
    }

}
