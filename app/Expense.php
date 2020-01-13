<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $table = 'acc_expenses';
    public $timestamps = false;

    public function accExpenseCategory()
    {
        return $this->belongsTo(AccExpenseCategory::class, 'expense_category_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

}
