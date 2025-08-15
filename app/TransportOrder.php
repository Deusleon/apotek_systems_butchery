<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransportOrder extends Model
{
    protected $fillable = [
        'order_number',
        'order_type',
        'attachments',
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
        'payment_status',
        'status',
        'notes',
        'created_by'
    ];

    protected $casts = [
        'attachments' => 'array',
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


    public function product()
    {
        return $this->belongsTo(Product::class, 'product'); // or 'product_id'
    }


    public function pickupSupplier()
    {
        return $this->belongsTo(Supplier::class, 'pickup_supplier_id');
    }

    public function deliveryStore()
    {
        return $this->belongsTo(Store::class, 'delivery_store_id');
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
            'draft' => 'Loading',
            'confirmed' => 'In Transit',
            'dispatched' => 'Offloading',
            'in_transit' => 'Received',
            'delivered' => 'Completed',
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

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }


    // In TransportOrder model
public function payments()
{
    return $this->hasMany(Payment::class);
}

public function paymentSummary()
{
    $totalPaid = $this->payments()->sum('amount');
    $advancePaid = $this->payments()->where('payment_type', 'advance')->sum('amount');
    $balancePaid = $this->payments()->where('payment_type', 'balance')->sum('amount');
    
    return [
        'transport_rate' => $this->transport_rate,
        'total_paid' => $totalPaid,
        'advance_paid' => $advancePaid,
        'balance_paid' => $balancePaid,
        'advance_balance' => max(0, ($this->advance_payment ?? 0) - $advancePaid),
        'remaining_balance' => max(0, $this->transport_rate - $totalPaid),
        'is_fully_paid' => $totalPaid >= $this->transport_rate,
    ];
}

public function updatePaymentStatus()
{
    $summary = $this->paymentSummary();

    if ($summary['is_fully_paid']) {
        $this->payment_status = 'fully_paid';
    } elseif ($summary['advance_paid'] > 0 && $summary['total_paid'] < $this->transport_rate) {
        $this->payment_status = 'advance_paid';
    } else {
        $this->payment_status = 'unpaid';
    }

    $this->save();
}

public function balance()
{
    return $this->transport_rate - $this->payments()->sum('amount');
}

// public function vehicle()
// {
//     return $this->belongsTo(Vehicle::class);
// }

// app/Models/TransportOrder.php
public function vehicle()
{
    return $this->belongsTo(Vehicle::class, 'assigned_vehicle_id');
}

    
}
