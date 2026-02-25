<?php

namespace Unusualdope\LaravelEcommerce\Models\Order;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Unusualdope\LaravelModelTranslatable\Traits\HasTranslation;

class OrderStatus extends Model
{
    use HasFactory, HasTranslation, SoftDeletes;

    public function setTranslatableFields(): array
    {
        $this->translatable_fields = [
            'name' => 'string',
        ];

        return $this->translatable_fields;
    }

    /**
     * Define relationships.
     */
    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'order_order_status');
    }

    public function languages(): HasMany
    {
        return $this->hasMany(OrderStatusLanguage::class);
    }

    public function getNameAttribute()
    {
        return $this->currentLanguage->name;
    }

    /**
     * Prevent deletion of native statuses.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected static function booted()
    {
        static::deleting(function (OrderStatus $orderStatus) {
            if ($orderStatus->is_native) {
                return false; // Prevent deletion
            }
        });
    }
}
