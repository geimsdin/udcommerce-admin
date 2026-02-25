<?php

namespace Unusualdope\LaravelEcommerce\Models\Size;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Unusualdope\LaravelEcommerce\Models\Product\Brand;
use Unusualdope\LaravelEcommerce\Models\Product\ProductCategory;

class SizeChart extends Model
{
    use Cachable;

    protected $fillable = [
        'name',
        'brand_id',
        'category_id',
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function sizechartentry(): HasMany
    {
        return $this->hasMany(SizeChartEntry::class);
    }
}
