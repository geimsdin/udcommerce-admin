<?php

namespace Unusualdope\LaravelEcommerce\Models\Product;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ProductProductCategory extends Pivot
{
    use Cachable;

    protected $table = 'product_product_category';

    // If your pivot table doesn't have an auto-incrementing ID:
    public $incrementing = false;

    // Enable timestamps if the table has `created_at` & `updated_at`:
    public $timestamps = true;
}
