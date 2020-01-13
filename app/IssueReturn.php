<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IssueReturn extends Model
{
    protected $table = 'inv_issue_returns';
    public $timestamps = false;


    public function currentStock()
    {
        return $this->belongsTo(CurrentStock::class, 'stock_id');
    }

    public function issue()
    {
        return $this->belongsTo(StockIssue::class, 'issue_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'returned_by');
    }

}
