<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Requisition extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'req_no',
        'notes',
        'remarks',
        'evidence_document', // Add this line
        'from_store',
        'to_store',
        'status',
        'created_by',
        'updated_by'
        // Add any other fields you need to mass assign
    ];

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