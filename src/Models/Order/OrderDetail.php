<?php

namespace Unusualdope\LaravelEcommerce\Models\Order;

use App\Models\Report\SalesData;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Unusualdope\LaravelEcommerce\Models\Product\Product;
use Unusualdope\LaravelEcommerce\Models\Product\ProductImage;
use Unusualdope\LaravelEcommerce\Models\Stock\Variation;

class OrderDetail extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::updated(function ($orderDetail) {
            if ($orderDetail->isDirty()) {
                SalesData::deleteOrderData(order_id: $orderDetail->order_id);
            }
        });

        static::deleted(function ($orderDetail) {
            SalesData::deleteOrderData(order_detail: $orderDetail);
        });

        parent::booted();
    }

    protected function setKeysForSaveQuery($query)
    {
        return $query->where('order_id', $this->getAttribute('order_id'))
            ->where('product_id', $this->getAttribute('product_id'))
            ->where('stock_id', $this->getAttribute('stock_id'))
            ->where('customization_id', $this->getAttribute('customization_id'));
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function variation(): BelongsTo
    {
        return $this->belongsTo(Variation::class, 'variation_id');
    }

    public function defaultProductImage(): HasOne
    {
        return $this->hasOne(ProductImage::class, 'product_id', 'product_id')
            ->orderBy('position')
            ->limit(1);
    }
}
