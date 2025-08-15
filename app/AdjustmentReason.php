<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdjustmentReason extends Model
{
    protected $table = 'adjustment_reasons';
    protected $fillable = ['reason'];
    public $timestamps = false;
}
