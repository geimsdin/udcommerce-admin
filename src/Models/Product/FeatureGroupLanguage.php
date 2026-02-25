<?php

namespace Unusualdope\LaravelEcommerce\Models\Product;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeatureGroupLanguage extends Model
{
    use Cachable;

    public function featureGroup(): BelongsTo
    {
        return $this->belongsTo(FeatureGroup::class);
    }
}
