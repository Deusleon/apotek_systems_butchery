<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transporter extends Model
{
    protected $fillable = [
        // Basic Information
        'name',
        'registration_number',
        'business_type',
        'tin_number',
        'national_id',
        
        // Contact Information
        'contact_person',
        'phone',
        'email',
        'physical_address',
        'region',
        'district',
        'postal_address',
        
        // Transport Type
        'transport_type',
        'other_transport_type',
        
        // Fleet Details
        'number_of_vehicles',
        'vehicle_types',
        'average_capacity',
        'vehicle_registration_numbers',
        
        // Driver Information
        'total_drivers',
        'driver_licensing_status',
        'insurance_coverage',
        
        // Bank & Payment
        'bank_name',
        'account_number',
        'payment_terms',
        'preferred_payment_method',
        
        // Contract Details
        'contract_start_date',
        'contract_expiry_date',
        'rate_per_km',
        'rate_per_trip',
        'rate_per_tonne',
        'agreed_routes',
        
        // Status
        'status',
        
        // Notes
        'notes'
    ];

    protected $casts = [
        'vehicle_types' => 'array',
        'vehicle_registration_numbers' => 'array',
        'contract_start_date' => 'date',
        'contract_expiry_date' => 'date',
    ];

    // Relationship with documents
    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    // Business type options
    public static function businessTypes()
    {
        return [
            'individual' => 'Individual',
            'company' => 'Company',
            'subcontractor' => 'Subcontractor'
        ];
    }

    // Transport type options
    public static function vehicle_types()
    {
        return [
            'road' => 'Road',
            'air' => 'Air',
            'sea' => 'Sea',
            'rail' => 'Rail',
            'other' => 'Other'
        ];
    }

    public static function transportTypes()
    {
        return [
            'road' => 'Road',
            'air' => 'Air',
            'sea' => 'Sea',
            'rail' => 'Rail',
            'other' => 'Other'
        ];
    }

    // Status options
    public static function statusOptions()
    {
        return [
            'active' => 'Active',
            'pending_approval' => 'Pending Approval',
            'suspended' => 'Suspended',
            'blacklisted' => 'Blacklisted'
        ];
    }

    // Payment method options
    public static function paymentMethods()
    {
        return [
            'bank' => 'Bank Transfer',
            'mobile_money' => 'Mobile Money',
            'cheque' => 'Cheque',
            'cash' => 'Cash'
        ];
    }
}