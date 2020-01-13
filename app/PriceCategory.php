<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PriceCategory extends Model
{
    protected $table = 'price_categories';
    public $timestamps = false;

    public function price(){

    	return $this->belongsTo('App\PriceList');
    }

    public function priceList()
    {
        return $this->hasMany(PriceList::class, 'price_category_id');
    }

}
