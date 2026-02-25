<?php

namespace Unusualdope\LaravelEcommerce\Models\Product;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeatureLanguage extends Model
{
    use Cachable;

    public function variant(): BelongsTo
    {
        return $this->belongsTo(Feature::class);
    }
}
