<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\SalesQuote;

class SalesQuoteDetail extends Model
{
    protected $table = 'sales_quote_details';
    public $timestamps = false;


    public function quote(){
        return $this->belongsTo(SalesQuote::class,'quote_id','id');
    }
}
