<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StockIssue extends Model
{
    protected $table = 'inv_stock_issues';

    public function currentStock()
    {
        return $this->belongsTo(CurrentStock::class, 'stock_id');
    }

    public function issueReturn()
    {
        return $this->belongsTo(IssueReturn::class, 'issue_id');
    }

    public function issueLocation()
    {
        return $this->belongsTo(Location::class, 'issued_to');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

}
