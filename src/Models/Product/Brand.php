<?php

namespace Unusualdope\LaravelEcommerce\Models\Product;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Laravel\Scout\Searchable;
use Unusualdope\LaravelEcommerce\Models\Size\SizeChart;

class Brand extends Model
{
    use Cachable, Searchable;

    public array $cache_keys = [
        'full_brands_list',
    ];

    public function toSearchableArray(): array
    {
        return [
            'id' => (string) $this->id,
            'name' => (string) ($this->name ?? ''),
            'slug' => (string) ($this->slug ?? ''),
            'status' => (int) $this->status,
            'products_count' => (int) $this->products()->count(),
            'created_at' => $this->created_at ? $this->created_at->unix() : 0,
        ];
    }


    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function sizechart(): HasMany
    {
        return $this->hasMany(SizeChart::class);
    }

    public function seasons(): BelongsToMany
    {
        return $this->belongsToMany(Season::class, 'brand_season')
            ->using(BrandSeason::class)
            ->withPivot(['date_start', 'date_end'])
            ->withTimestamps();
    }

    public static function getAllBrandsArray()
    {
        return Cache::remember('full_brands_list', 3600, function () {
            return Brand::all()->map(function ($brand) {
                return [
                    'id' => $brand->id,
                    'name' => $brand->name,
                ];
            })->toArray();
        });
    }
}
