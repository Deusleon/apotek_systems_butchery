<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SalesQuoteDetail extends Model
{
    protected $table = 'sales_quote_details';
    public $timestamps = false;


    public function quote()
    {
        return $this->belongsTo(SalesQuote::class, 'quote_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

}
