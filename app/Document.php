<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'name',
        'path',
        'type',
        'documentable_id',
        'documentable_type'
    ];

    public function documentable()
    {
        return $this->morphTo();
    }
}