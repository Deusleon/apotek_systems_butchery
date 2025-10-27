<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GeneralSetting extends Model
{
    protected $table = 'general_settings';

    protected $fillable = [
        'cash_sale_terms',
        'credit_sale_terms',
        'proforma_invoice_terms',
        'purchase_order_terms',
        'delivery_note_terms',
        'credit_note_terms',
    ];

    public function setCreatedAt($value)
    {
        return NULL;
    }
}
