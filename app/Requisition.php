<?php

namespace App;


use Illuminate\Database\Eloquent\Model;

class Requisition extends Model
{
    public function reqDetails()
    {
        return $this->hasMany(RequisitionDetail::class, 'req_id', 'id');
    }

    public function reqTo()
    {
        return $this->belongsTo(User::class, 'req_to', 'id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
