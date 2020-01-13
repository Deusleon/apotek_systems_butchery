<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    //
	protected $table = 'inv_issue_locations';
	public $timestamps = false;

	
	public function stockIssue()
	{
		return $this->hasMany(StockIssue::class, 'issued_to');
	}      


}
