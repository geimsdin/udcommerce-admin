<?php

namespace Unusualdope\LaravelEcommerce\Models\Order;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderStatusLanguage extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * Define relationships.
     */
    public function orderStatus(): BelongsTo
    {
        return $this->belongsTo(OrderStatus::class);
    }
}
