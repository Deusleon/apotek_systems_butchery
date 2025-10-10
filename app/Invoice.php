<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $table = 'inv_invoices';

    protected $fillable = [
        'invoice_no',
        'supplier_id',
        'invoice_date',
        'invoice_amount',
        'paid_amount',
        'received_amount',
        'grace_period',
        'received_status',
        'payment_due_date',
        'remarks',
        'updated_by'
    ];

    public function supplier()
    {

        return $this->belongsTo('App\Supplier');
    }

    public function incomingStock()
    {
        return $this->hasMany(GoodsReceiving::class, 'invoice_no');
    }


}

