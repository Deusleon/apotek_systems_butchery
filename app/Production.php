<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Production extends Model

{

    protected $fillable = ['production_date', 'cows_received', 'total_weight', 'meat_output'];

}