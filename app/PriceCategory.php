<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PriceCategory extends Model
{
    protected $table = 'price_categories';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'type',
    ];

    protected $casts = [
        'default_markup_percentage' => 'decimal:2'
    ];

    public function priceLists()
    {
        return $this->hasMany(PriceList::class);
    }

    public function updatePricesWithNewMarkup()
    {
        foreach ($this->priceLists()->where('is_custom', false)->get() as $priceList) {
            $priceList->setDefaultPrice();
        }
    }

    public function price(){

    	return $this->belongsTo('App\PriceList');
    }

    public function priceList()
    {
        return $this->hasMany(PriceList::class, 'price_category_id');
    }

}
