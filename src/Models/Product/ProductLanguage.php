<?php

namespace Unusualdope\LaravelEcommerce\Models\Product;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Unusualdope\FilamentModelTranslatable\Models\FmtLanguage;

class ProductLanguage extends Model
{
    use Cachable;

    public function language(): BelongsTo
    {
        return $this->belongsTo(FmtLanguage::class, 'language_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
