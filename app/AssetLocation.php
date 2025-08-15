<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssetLocation extends Model
{
    public $table='tbl_assets_locations';

    protected $fillable = ['name', 'address'];

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }
}
