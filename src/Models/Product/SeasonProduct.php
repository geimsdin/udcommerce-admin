<?php

namespace Unusualdope\LaravelEcommerce\Models\Product;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Relations\Pivot;

class SeasonProduct extends Pivot
{
    use Cachable;

    protected $table = 'season_product';

    public $incrementing = false;

    public $timestamps = false;
}
