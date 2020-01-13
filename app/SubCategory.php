<?php

namespace App;

use App\Category as category;
use App\Product as product;
use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    protected $table = 'inv_sub_categories';

    public $timestamps = false;

    public function category()
    {
        return $this->belongsTo(category::class);
    }

    public function subCategory()
    {
        return $this->hasMany(product::class, 'sub_category_id', 'id');

    }

}
