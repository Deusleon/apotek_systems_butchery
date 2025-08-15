<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    public $table='tbl_budgets';

    protected $fillable = ['category', 'amount', 'period', 'start_date', 'end_date'];
}
