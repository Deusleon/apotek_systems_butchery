<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    //
    protected $table = 'inv_invoices';

    public function supplier()
    {

        return $this->belongsTo('App\Supplier');
    }

    public function incomingStock()
    {
        return $this->hasMany(GoodsReceiving::class, 'invoice_no');
    }


}

