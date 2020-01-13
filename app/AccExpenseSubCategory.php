<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccExpenseSubCategory extends Model
{
	protected $table='acc_expense_sub_categories';
    public  $timestamps = false;

    public function expenseCategory()
    {
        return $this->belongsTo(AccExpenseCategory::class);
    }

   }
