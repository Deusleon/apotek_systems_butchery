<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransportOrder extends Model
{
    protected $fillable = [
        'order_number',
        'transporter_id',
        'pickup_location',
        'delivery_location',
        'pickup_date',
        'delivery_date',
        'product',
        'quantity',
        'unit',
        'priority',
        'assigned_vehicle_id',
        'transport_rate',
        'advance_payment',
        'payment_method',
        'status',
        'notes'
    ];

    protected $casts = [
        'pickup_date' => 'datetime',
        'delivery_date' => 'datetime',
    ];

    // Static location options
    public static function pickupLocations()
    {
        return [
            'factory_1' => 'Main Factory (Nairobi)',
            'factory_2' => 'Secondary Factory (Mombasa)',
            'warehouse_1' => 'Central Warehouse',
            'warehouse_2' => 'Regional Distribution Center',
            'port' => 'Mombasa Port',
            'other_pickup' => 'Other Pickup Location'
        ];
    }

    public static function deliveryLocations()
    {
        return [
            'client_a' => 'Client A - Downtown',
            'client_b' => 'Client B - Industrial Area',
            'warehouse_1' => 'Central Warehouse',
            'warehouse_2' => 'Regional Distribution Center',
            'retail_1' => 'Retail Outlet 1',
            'retail_2' => 'Retail Outlet 2',
            'other_delivery' => 'Other Delivery Location'
        ];
    }

    // Static product options
    public static function productOptions()
    {
        return [
            'cement_50kg' => 'Cement (50kg bags)',
            'wheat_ton' => 'Wheat (per ton)',
            'maize_90kg' => 'Maize (90kg bags)',
            'sugar_25kg' => 'Sugar (25kg bags)',
            'construction_materials' => 'Construction Materials',
            'other_product' => 'Other Product'
        ];
    }

    // Status options
    public static function statusOptions()
    {
        return [
            'draft' => 'Draft',
            'confirmed' => 'Confirmed',
            'dispatched' => 'Dispatched',
            'in_transit' => 'In Transit',
            'delivered' => 'Delivered',
            'closed' => 'Closed'
        ];
    }

    // Priority options
    public static function priorityOptions()
    {
        return [
            'normal' => 'Normal',
            'urgent' => 'Urgent',
            'very_urgent' => 'Very Urgent'
        ];
    }

    // Unit options
    public static function unitOptions()
    {
        return [
            'tons' => 'Tons',
            'bags' => 'Bags',
            'kg' => 'Kilograms',
            'units' => 'Units'
        ];
    }

    // Payment method options
    public static function paymentMethods()
    {
        return [
            'cash' => 'Cash',
            'bank_transfer' => 'Bank Transfer',
            'mobile_money' => 'Mobile Money',
            'cheque' => 'Cheque'
        ];
    }

    // Relationships
    public function transporter()
    {
        return $this->belongsTo(Transporter::class);
    }

    public function assignedVehicle()
    {
        return $this->belongsTo(Vehicle::class, 'assigned_vehicle_id');
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }
}