<?php

namespace Unusualdope\LaravelEcommerce\Models\Product;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Unusualdope\FilamentModelTranslatable\Models\FmtLanguage;

class ProductCategoryLanguage extends Model
{
    use Cachable;

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(FmtLanguage::class, 'language_id');
    }
}
