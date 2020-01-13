<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Sub_Category as subCategory;
use App\Product as product;

class Category extends Model
{
    protected $table = 'inv_categories';
    public $timestamps = false;

    public function category(){
        return $this->hasMany(product::class, 'category_id', 'id');

    }

    public function subCategory(){
        return $this->hasMany(subCategory::class,'category_id','id');
    }

}

