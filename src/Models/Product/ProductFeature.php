<?php

namespace Unusualdope\LaravelEcommerce\Models\Product;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ProductFeature extends Pivot
{
    use Cachable;

    protected $table = 'product_features';

    public $incrementing = false;

    public $timestamps = true;
}
