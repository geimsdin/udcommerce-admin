<?php

namespace Unusualdope\LaravelEcommerce\Models\Stock;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Unusualdope\LaravelEcommerce\Models\Product\Product;

class Stock extends Model
{
    use Cachable;

    protected $table = 'stocks';

    protected $fillable = [
        'product_id',
        'is_variant',
        'quantity',
        'price',
        'sku',
        'ean',
        'mpn',
        'upc',
        'isbn',
        'minimal_quantity',
        'low_stock_alert',
        'available_from',
        'available_to',
    ];

    protected $casts = [
        'is_variant' => 'boolean',
        'quantity' => 'integer',
        'price' => 'float',
        'minimal_quantity' => 'integer',
        'low_stock_alert' => 'integer',
        'available_from' => 'datetime',
        'available_to' => 'datetime',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsToMany
    {
        return $this->belongsToMany(Variant::class, 'stock_variants');
    }

    public static function getTotalByProductId(int $productId, int $excludeStockId = 0): int
    {
        $query = static::where('product_id', $productId);
        if ($excludeStockId > 0) {
            $query->where('id', '!=', $excludeStockId);
        }

        return (int) $query->sum('quantity');
    }

    public static function increaseQuantity(int $stockId, int $quantity): void
    {
        $stock = static::find($stockId);
        if ($stock) {
            $stock->increment('quantity', $quantity);
        }
    }

    public static function decreaseQuantity(int $stockId, int $quantity): void
    {
        $stock = static::find($stockId);
        if ($stock) {
            $stock->decrement('quantity', $quantity);
        }
    }
}
