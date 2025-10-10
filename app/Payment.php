<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    
    protected $fillable = [
        'transport_order_id',
        'invoice_id',
        'user_id',
        'amount',
        'payment_type',
        'payment_method',
        'payment_date',
        'receipt_number',
        'transaction_reference',
        'payment_proof',
        'notes',
        'status'
    ];

    // protected $fillable = [
    //     'transport_order_id',
    //     'user_id',
    //     'amount',
    //     'payment_type', // 'advance' or 'balance'
    //     'payment_method', // 'cash', 'bank_transfer', etc.
    //     'transaction_reference',
    //     'status', // 'pending', 'completed', etc.
    //     'notes',
    //     'payment_date',
    //     'receipt_number',
    //     'payment_proof',
    // ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    // Relationships
    public function transportOrder()
    {
        return $this->belongsTo(TransportOrder::class, 'transport_order_id');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Accessors / Helpers
    public function isFullyPaid()
    {
        return $this->transportOrder && $this->transportOrder->payments->sum('amount') >= $this->transportOrder->transport_rate;
    }

    public function getStatusBadgeClassAttribute()
    {
        switch ($this->status) {
            case 'completed':
                return 'success';
            case 'pending':
                return 'warning';
            case 'failed':
                return 'danger';
            case 'refunded':
                return 'secondary';
            default:
                return 'dark';
        }
    }

    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 2);
    }

    public function getPaymentProofUrlAttribute()
    {
        return $this->payment_proof
            ? asset('storage/' . $this->payment_proof)
            : null;
    }
}
