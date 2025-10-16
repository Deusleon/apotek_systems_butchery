<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentType extends Model
 {
    protected $table = 'payment_types';

    protected $fillable = [
        'name',
    ];

    public function sales()
 {
        return $this->hasMany( Sale::class );
    }

}
