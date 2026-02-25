<?php

namespace Unusualdope\LaravelEcommerce\Models\Cart;

use Unusualdope\LaravelEcommerce\Models\Product\Product;
use Unusualdope\LaravelEcommerce\Models\Product\ProductImage;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Unusualdope\LaravelEcommerce\Models\Stock\Variation;

class CartDetail extends Model
{
    protected $primaryKey = ['cart_id', 'product_id', 'variation_id'];
    public $incrementing = false;

    protected function setKeysForSaveQuery($query)
    {
        return $query->where('cart_id', $this->getAttribute('cart_id'))
            ->where('product_id', $this->getAttribute('product_id'))
            ->where('variation_id', $this->getAttribute('variation_id'))
            ->first();
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class, 'cart_id', 'id');
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
        if( $this->variation ) {
            return $this->variation->defaultProductImage;
        }
        return $this->hasOne(ProductImage::class, 'product_id', 'product_id')
            ->orderBy('position')
            ->limit(1);
    }
}
