<?php

namespace Unusualdope\LaravelEcommerce\Models\Payment;

use Illuminate\Database\Eloquent\Model;

class PaymentGateway extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'driver',
        'active',
        'is_default',
        'config',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'is_default' => 'boolean',
            'config' => 'array',
        ];
    }
}
