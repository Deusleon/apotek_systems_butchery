<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $fillable = [
        'plate_number',
        'transporter_id',
        'vehicle_type',
        'capacity',
        'make',
        'model',
        'year',
        'color',
        'chassis_number',
        'engine_number',
        'fitness_expiry',
        'insurance_expiry',
        'permit_expiry',
        'status',
        'notes'
    ];

    protected $dates = [
        'fitness_expiry',
        'insurance_expiry',
        'permit_expiry',
        'created_at',
        'updated_at'
    ];

    // Relationship with transporter
    public function transporter()
    {
        return $this->belongsTo(Transporter::class);
    }

    // Relationship with documents
    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    // Vehicle type options
    public static function typeOptions()
    {
        return [
            'truck' => 'Truck',
            'van' => 'Van',
            'pickup' => 'Pickup',
            'trailer' => 'Trailer',
            'container' => 'Container',
            'bus' => 'Bus',
            'other' => 'Other'
        ];
    }

    // Status options
    public static function statusOptions()
    {
        return [
            'active' => 'Active',
            'inactive' => 'Inactive',
            'maintenance' => 'Under Maintenance',
            'On trip' => 'On Trip',
            
        ];
    }




// app/Models/Vehicle.php
public function transportOrders()
{
    return $this->hasMany(TransportOrder::class, 'assigned_vehicle_id');
}
}