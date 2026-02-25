<?php

namespace Unusualdope\LaravelEcommerce\Models\Product;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Laravel\Scout\Searchable;
use Unusualdope\LaravelEcommerce\Models\Size\SizeChart;
use Unusualdope\LaravelModelTranslatable\Traits\HasTranslation;

class ProductCategory extends Model
{
    use Cachable, HasTranslation, Searchable;

    public static $current_cache_key = 'NAVIGATION_CATEGORY_LIST';

    protected static function boot()
    {
        parent::boot();
        static::saved(function ($category) {
            Cache::delete(self::$current_cache_key);
        });
        static::deleted(function ($category) {
            Cache::delete(self::$current_cache_key);
        });
    }

    public function setTranslatableFields(): array
    {
        $this->translatable_fields = [
            'name' => 'string',
            'description' => 'text',
        ];

        return $this->translatable_fields;
    }

    public function toSearchableArray(): array
    {
        return [
            'id' => (string) $this->id,
            'name' => (string) ($this->name ?? ''),
            'link_rewrite' => (string) ($this->currentLanguage->link_rewrite ?? ''),
            'parent_id' => (int) $this->parent_id,
            'status' => (int) $this->status,
            'products_count' => (int) $this->products()->count(),
            'created_at' => $this->created_at ? $this->created_at->unix() : 0,
        ];
    }


    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'product_product_category', // pivot table
            'product_category_id',      // foreign key on pivot table
            'product_id'                // related key on pivot table
        )->using(ProductProductCategory::class);
    }

    public function parent(): BelongsTo  // Changed from HasOne to BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'parent_id');
    }

    public function children(): HasMany  // Add this method for child categories
    {
        return $this->hasMany(ProductCategory::class, 'parent_id');
    }

    public function languages(): HasMany
    {
        return $this->hasMany(ProductCategoryLanguage::class);
    }

    public function sizechart(): HasMany
    {
        return $this->hasMany(SizeChart::class);
    }

    public function getNameAttribute()
    {
        return $this->currentLanguage->name ?? $this->attributes['name'] ?? '';
    }

    protected static function buildCategoryTree($categories, $parentId = -1, $currentDepth = 1, $maxDepth = 3)
    {
        if ($currentDepth > $maxDepth) {
            return [];
        }

        $tree = [];
        foreach ($categories as $category) {
            if ($category['parent_id'] == $parentId) {
                $category['children'] = self::buildCategoryTree($categories, $category['id'], $currentDepth + 1, $maxDepth);
                $tree[] = $category;
            }
        }

        return $tree;
    }

    public static function getAllActiveCategory($language_id = null)
    {
        if (empty($language_id)) {
            $language_id = FmtLanguage::getCurrentLanguage();
        }

        $cache = Cache::get(self::$current_cache_key);
        if (empty($cache)) {
            $active_category = ProductCategory::where('status', 1)
                ->orderBy('parent_id', 'ASC')
                ->get();

            $categories = self::buildCategoryTree($active_category);
            $cache = json_encode($categories);
            Cache::set(self::$current_cache_key, $cache, 0);
        }

        return !empty($cache) ? json_decode($cache, true) : [];
    }

    public static function getAllActiveCategoryWithoutTree($language_id = null)
    {
        if (empty($language_id)) {
            $language_id = FmtLanguage::getCurrentLanguage();
        }

        $active_category = ProductCategory::where('status', 1)
            ->orderBy('parent_id', 'ASC')
            ->get()
            ->toArray();

        return $active_category;
    }

    public static function getCategoryWithLanguage($category_id, $language_id = null, $with_products = false)
    {
        // Get the current language if none is provided
        if (empty($language_id)) {
            $language_id = b2b_get_current_language_id();
        }

        $category = DB::table('product_categories')
            ->where('product_categories.id', $category_id)
            ->join('product_category_languages', function (JoinClause $join) use ($language_id) {
                $join->on('product_categories.id', '=', 'product_category_languages.product_category_id')
                    ->where('product_category_languages.language_id', '=', $language_id);
            })->first();

        if (!$category) {
            return null; // Return null if the category does not exist
        }

        if ($with_products) {
            $products = Product::getProductDataByCategoryId($category_id, $language_id);

            // Directly attach because already a collection
            $category->products = $products;
        }

        return $category;
    }
}
