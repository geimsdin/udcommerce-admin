<?php

namespace Unusualdope\LaravelEcommerce\Models\Product;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\Pivot;

class BrandSeason extends Pivot
{
    use Cachable;

    public function season(): HasOne
    {
        return $this->hasOne(Season::class);
    }

    public function brand(): HasOne
    {
        return $this->hasOne(Brand::class);
    }
}
