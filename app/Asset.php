<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    public $table = 'tbl_assets';

    protected $fillable = ['name', 'serial_number', 'category_id', 'location_id', 'assigned_user_id', 'value', 'purchase_date', 'description', 'status'];

    public function category()
    {
        return $this->belongsTo(AssetCategory::class);
    }

    public function location()
    {
        return $this->belongsTo(AssetLocation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }
}
