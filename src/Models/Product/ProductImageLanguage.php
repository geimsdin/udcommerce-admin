<?php

namespace Unusualdope\LaravelEcommerce\Models\Product;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Unusualdope\FilamentModelTranslatable\Models\FmtLanguage;

class ProductImageLanguage extends Model
{
    use Cachable;

    public function productImage(): BelongsTo
    {
        return $this->belongsTo(ProductImage::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(FmtLanguage::class);
    }

    public static function getLangData($product_image_id)
    {
        return self::where('product_image_id', $product_image_id)->get();
    }
}
