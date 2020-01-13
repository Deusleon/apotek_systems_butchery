<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class SalesDetail extends Model
{
    protected $table = 'sales_details';
    public $timestamps = false;


    public function sale()
    {
        return $this->belongsTo(Sale::class,'sale_id','id');
    }


    public function sale_return()
    {
        return $this->hasOne(SalesReturn::class,'sale_detail_id','id');
    }

    public function currentStock()
    {
    	return $this->belongsTo(CurrentStock::class, 'stock_id');
    }




}
