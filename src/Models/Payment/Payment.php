<?php

namespace Unusualdope\LaravelEcommerce\Models\Payment;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use Cachable;

    protected $fillable = [
        'user_id',
        'order_id',
        'transaction_id',
        'gateway_slug',
        'amount',
        'status',
        'gateway_reference',
        'idempotency_key',
        'completed_at'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(\Unusualdope\LaravelEcommerce\Models\Order\Order::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
