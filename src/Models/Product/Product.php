<?php

namespace Unusualdope\LaravelEcommerce\Models\Product;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Laravel\Scout\Searchable;
use Unusualdope\LaravelEcommerce\Models\Administration\Currency;
use Unusualdope\LaravelEcommerce\Models\Stock\Stock;
use Unusualdope\LaravelEcommerce\Models\Stock\Variation;
use Unusualdope\LaravelEcommerce\Models\Tax\Tax;
use Unusualdope\LaravelModelTranslatable\Traits\HasTranslation;

class Product extends Model
{
    use Cachable, HasTranslation, Searchable;

    public function setTranslatableFields(): array
    {
        $this->translatable_fields = [
            'name' => 'string',
            'description_short' => 'text',
            'description_long' => 'text',
            'link_rewrite' => 'string',
            'meta_title' => 'string',
            'meta_description' => 'text',
        ];

        return $this->translatable_fields;
    }

    public function shouldBeSearchable(): bool
    {
        return true;
        //        return $this->getTotalStockAttribute() > 0;
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('position', 'asc');
    }

    public function languages(): hasMany
    {
        return $this->hasMany(ProductLanguage::class, 'product_id');
    }

    public function seasons(): BelongsToMany
    {
        return $this->belongsToMany(Season::class, 'season_product');
    }

    public function variations(): HasMany
    {
        return $this->hasMany(Variation::class);
    }

    public function specificPrices(): HasMany
    {
        return $this->hasMany(SpecificPrice::class, 'id_product', 'id');
    }

    public function defaultImage(): HasOne
    {
        return $this->hasOne(ProductImage::class, 'product_id')
            ->orderBy('position')
            ->limit(1);
    }

    /**
     * Quantity from products.quantity column (for stock index when using product-level quantity only).
     * Use this instead of getTotalStockAttribute() when not using stocks table.
     */
    public function getQuantityForStockDisplay(): int
    {
        return (int) ($this->attributes['quantity'] ?? 0);
    }

    public function getNameAttribute(): string
    {
        return $this->currentLanguage->name ?? $this->attributes['name'] ?? '';
    }

    public function getDescriptionLongAttribute(): string
    {
        return $this->currentLanguage->description_long ?? '';
    }

    public function getDescriptionShortAttribute(): string
    {
        return $this->currentLanguage->description_short ?? '';
    }

    public function getNameCurrentLanguage(int $languageId): string
    {
        return $this->languages()
            ->where('language_id', $languageId)
            ->value('name');
    }

    /**
     * Get variant details for display (group name => variant names) for variable products.
     * Returns e.g. ["Size" => ["S", "M", "L"], "Color" => ["Red", "Blue"]].
     */
    public function getVariantDetailsForLanguage(int $languageId): array
    {
        if ($this->type !== 'variable') {
            return [];
        }

        $details = [];
        $variations = $this->variations()->with(['variants.variantGroup'])->get();

        foreach ($variations as $variation) {
            foreach ($variation->variants as $variant) {
                $groupLang = $variant->variantGroup->getSpecificLanguage($languageId);
                $groupName = ($groupLang && $groupLang->name) ? $groupLang->name : ($variant->variantGroup->getNameAttribute() ?: __('stocks.table.variant'));
                $variantName = $variant->getNameCurrentLanguage($languageId) ?: $variant->getNameAttribute();

                if (!isset($details[$groupName])) {
                    $details[$groupName] = [];
                }
                if (!in_array($variantName, $details[$groupName], true)) {
                    $details[$groupName][] = $variantName;
                }
            }
        }

        return $details;
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(
            ProductCategory::class,
            'product_product_category', // pivot table
            'product_id',               // foreign key on pivot table
            'product_category_id'       // related key on pivot table
        )->using(ProductProductCategory::class);
    }

    public function features(): BelongsToMany
    {
        return $this->belongsToMany(Feature::class, 'product_features')
            ->using(ProductFeature::class);
    }

    public function defaultCategoryId()
    {
        return $this->default_category_id;
    }

    public static function getProductDataByCategoryId(
        $category_id,
        $language_id = null,
        $only_in_stock = true,
        $only_active = true,
        $limit = 16,
        $offset = 0,
        $order_by = 'products.created_at',
        $order_way = 'desc'
    ): array|object {
        // Get the current language if none is provided
        if (empty($language_id)) {
            $language_id = b2b_get_current_language_id();
        }

        // Base query to fetch products with translations
        $query = DB::table('products')
            ->join('product_product_category', 'products.id', '=', 'product_product_category.product_id') // Join category association
            ->join('product_languages', function (JoinClause $join) use ($language_id) {
                $join->on('products.id', '=', 'product_languages.product_id')
                    ->where('product_languages.language_id', '=', $language_id); // Fetch product translation
            })
            ->where('product_product_category.product_category_id', $category_id) // Filter by category
            ->select([
                'products.id as product_id',
                'products.sku as sku',
                'products.price as price',
                'products.quantity as quantity',
                'product_languages.name as name',
                'product_languages.description_long as description_long',
                'product_languages.description_short as description_short',
            ])
            ->orderBy($order_by, $order_way);

        // get only the products with status active
        if ($only_active) {
            $query->where('products.status', '=', 1);
        }

        // ✅ If `$only_in_stock` is true, filter products with at least one stock entry where `quantity > 0`
        if ($only_in_stock) {
            $query->where(function ($q) {
                $q->whereExists(function ($subquery) {
                    $subquery->select(DB::raw(1))
                        ->from('stocks')
                        ->whereColumn('stocks.product_id', 'products.id') // Match product_id in stock
                        ->where('stocks.quantity', '>', 0); // Ensure at least one row with positive quantity
                })
                    ->orWhere('products.quantity', '>', 0); // OR condition for products table
            });
        }

        // Calculate the totals before applying limit and offset to get the total resulting products
        $totalProducts = self::getTotalProductsForQuery($query);

        $products = $query->limit($limit)
            ->offset($offset)
            ->get();

        // Fetch product images
        $product_ids = $products->pluck('product_id')->toArray();
        $images = ProductImage::getProductImagesByProductIds($product_ids);

        // Group images by product_id
        $groupedImages = $images->groupBy('product_id');

        // Attach images to each product, keeping them as a collection
        $finalProducts = $products->map(function ($product) use ($groupedImages) {
            $product->images = $groupedImages->get($product->product_id, collect()); // Keep as collection

            return $product;
        });

        $finalProducts->total = $totalProducts;
        $finalProducts->limit = $limit;
        $finalProducts->offset = $offset;

        return $finalProducts;
    }

    /**
     * Builds and executes a product query based on various filters and parameters.
     * Caches the results for performance.
     *
     * @param  array|null  $product_ids  Specific product IDs to filter by.
     * @param  int|null  $limit  Custom limit for pagination.
     * @param  int|null  $offset  Custom offset for pagination.
     * @return array The query results including products, filters, and totals.
     */
    public static function productQueryBuilder($product_ids = [], $limit = null, $offset = null)
    {
        // 1. Retrieve all query parameters
        $product_query = b2b_context()->getProductQuery();

        // Standardize limit and offset based on method arguments or product_query defaults
        $search_limit = $limit ?? $product_query['search_limit'];
        $search_offset = $offset ?? $product_query['search_offset'];

        $search_string = $product_query['search_string'];
        $search_limit = is_null($limit) ? $product_query['search_limit'] : $limit;
        $search_offset = is_null($offset) ? $product_query['search_offset'] : $offset;
        $search_order_by = $product_query['search_order_by'];
        $search_order_way = $product_query['search_order_way'];
        $filter_brands = $product_query['filter_brands'];
        $filter_categories = $product_query['filter_categories'];
        $filter_features = $product_query['filter_features'];
        $filter_min_price = $product_query['filter_min_price'] ?? 0;
        $filter_max_price = $product_query['filter_max_price'] ?? 0;
        $category_page_id = $product_query['category_page_id'];
        $season_id = $product_query['season_id'];

        // Get the current language for translations
        $language_id = b2b_get_current_language_id();

        if (Season::isReadyStock($season_id)) {
            config(['b2b.product_query.only_in_stock' => true]);
        } else {
            config(['b2b.product_query.only_in_stock' => false]);
        }

        // 2. Prepare parameters for cache key generation
        // Ensure all possible influencing variables are included and normalized
        $cacheParams = [
            'product_ids' => empty($product_ids) ? [] : array_map('intval', $product_ids), // Ensure ints and sorted
            'search_string' => $product_query['search_string'] ?? '',
            'search_limit' => $search_limit,
            'search_offset' => $search_offset,
            'search_order_by' => $product_query['search_order_by'] ?? 'products.quantity', // Default for order by
            'search_order_way' => $product_query['search_order_way'] ?? 'desc', // Default for order way
            'filter_brands' => self::normalizeFilterParam($product_query['filter_brands'] ?? []),
            'filter_categories' => self::normalizeFilterParam($product_query['filter_categories'] ?? []),
            'filter_features' => self::normalizeFilterParam($product_query['filter_features'] ?? []),
            'filter_min_price' => (float) ($product_query['filter_min_price'] ?? 0),
            'filter_max_price' => (float) ($product_query['filter_max_price'] ?? 0),
            'category_page_id' => (int) ($product_query['category_page_id'] ?? 0),
            'season_id' => (int) ($product_query['season_id'] ?? 0),
            'language_id' => $language_id,
            'quantity' => config('b2b.product_query.only_in_stock'),
        ];

        // Sort arrays and top-level keys for consistent cache key generation
        array_walk_recursive($cacheParams, function (&$item) {
            if (is_array($item)) {
                sort($item);
            }
        });
        ksort($cacheParams);

        // Generate a unique hash for the cache key
        $cacheKey = 'product_query_results_' . hash('sha256', json_encode($cacheParams));

        // Define cache TTL (Time To Live) - e.g., 60 minutes
        $cacheTtl = now()->addMinutes(60);

        // Try to retrieve results from cache, or execute the query and store them
        return Cache::remember($cacheKey, $cacheTtl, function () use ($product_ids, $search_limit, $search_offset, $language_id, $cacheParams, $search_string, $filter_categories, $category_page_id, $filter_features, $filter_brands, $filter_min_price, $filter_max_price, $season_id) {
            // Build the base query inside the cache callback
            $query = DB::table('products')
                ->when(config('b2b.product_query.only_in_stock'), function (Builder $query) {
                    $query->where('products.quantity', '>', 0);
                })
                ->join('product_product_category', 'products.id', '=', 'product_product_category.product_id')
                ->join('brands', 'products.brand_id', '=', 'brands.id')
                ->join('product_features', 'products.id', '=', 'product_features.product_id')
                ->where('products.status', 1)
                ->join('product_languages', function (JoinClause $join) use ($language_id) {
                    $join->on('products.id', '=', 'product_languages.product_id')
                        ->where('product_languages.language_id', '=', $language_id);
                })
                ->join('season_product', function (JoinClause $join) use ($season_id) {
                    $join->on('products.id', '=', 'season_product.product_id')
                        ->where('season_product.season_id', '=', $season_id);
                });

            // Apply filters based on product_ids or other search parameters
            if (!empty($product_ids)) {
                $query->whereIn('products.id', $product_ids);
            } else {
                if (!empty($search_string) && is_string($search_string)) {
                    $query->where(function ($q) use ($search_string) {
                        $q->where('products.sku', 'LIKE', "%{$search_string}%")
                            ->orWhere('product_languages.name', 'LIKE', "%{$search_string}%")
                            ->orWhere('product_languages.description_long', 'LIKE', "%{$search_string}%")
                            ->orWhere('product_languages.description_short', 'LIKE', "%{$search_string}%");
                    });
                }

                // Category filtering logic
                if (!empty($filter_categories) && is_string($filter_categories)) {
                    $filter_categories_array = self::normalizeFilterParam($filter_categories);
                    if (!empty($filter_categories_array)) {
                        $query->whereIn('product_product_category.product_category_id', $filter_categories_array);
                    } else {
                        $query->where('product_product_category.product_category_id', $category_page_id);
                    }
                } elseif ($category_page_id > 0) {
                    $query->where('product_product_category.product_category_id', $category_page_id);
                }

                // Feature filtering
                if (!empty($filter_features) && is_string($filter_features)) {
                    $filter_features_array = self::normalizeFilterParam($filter_features);
                    $query->whereIn('product_features.feature_id', $filter_features_array);
                }

                // Brand filtering
                if (!empty($filter_brands) && is_string($filter_brands)) {
                    $filter_brands_array = self::normalizeFilterParam($filter_brands);
                    if (!empty($filter_brands_array)) {
                        $query->whereIn('products.brand_id', $filter_brands_array);
                    }
                }

                // Price filtering
                if ($filter_min_price > 0) {
                    $query->where('products.price', '>=', $filter_min_price);
                }
                if ($filter_max_price > 0) {
                    $query->where('products.price', '<=', $filter_max_price);
                }
            }

            // --- Sub-queries for related data (Brands, Features, Categories) ---
            // These methods will clone the main query to calculate their specific totals/data
            $totalProducts = self::getTotalProductsForQuery($query);
            $productBrands = self::getCurrentProductsBrands($query);
            $productFeatures = self::getCurrentProductsFeatures($query, $language_id);
            $productCategories = self::getCurrentProductsCategories($query, $language_id);

            // --- Main Product Data Fetch ---
            $products = $query
                ->limit($search_limit)
                ->offset($search_offset)
                ->orderBy($cacheParams['search_order_by'], $cacheParams['search_order_way']) // Use order from cached params
                ->select([
                    'products.id as product_id',
                    'products.brand_id as brand_id',
                    'products.sku as sku',
                    'products.price as price',
                    'products.quantity as quantity',
                    'product_languages.name as name',
                    'product_languages.description_long as description_long',
                    'product_languages.description_short as description_short',
                ])
                ->distinct()
                ->get();

            // --- Product Images Fetch & Attach ---
            if ($products->isNotEmpty()) {
                $images = DB::table('product_images')
                    ->join('product_image_languages', function ($join) use ($language_id) {
                        $join->on('product_images.id', '=', 'product_image_languages.product_image_id')
                            ->where('product_image_languages.language_id', '=', $language_id);
                    })
                    ->whereIn('product_images.product_id', $products->pluck('product_id'))
                    ->select(
                        'product_images.product_id', // No alias needed if directly mapping
                        'product_images.id',
                        'product_images.image',
                        'product_images.position as image_position',
                        'product_image_languages.caption as caption'
                    )
                    ->orderBy('product_images.position', 'asc')
                    ->get()
                    ->groupBy('product_id');

                $products = $products->map(function ($product) use ($images) {
                    $product->images = $images[$product->product_id] ?? collect(); // Use collect() for consistency

                    return $product;
                });
            } else {
                $products = collect(); // Ensure products is a collection even if empty
            }

            // --- Assemble Final Result ---
            $result = [
                'products' => $products,
                'categories' => $productCategories,
                'brands' => $productBrands,
                'features' => $productFeatures,
                'minPrice' => $products->min('price') ?? 0, // Handle empty collection
                'maxPrice' => $products->max('price') ?? 0, // Handle empty collection
                'totalProducts' => $totalProducts,
                'offset' => $search_offset,
                'limit' => $search_limit,
            ];

            // Store in session (if still required, though caching might make this redundant for some use cases)
            session()->put('products_data', $result);

            return $result;
        }); // End Cache::remember closure
    }

    /**
     * Helper to normalize filter parameters (comma-separated string to sorted int array).
     *
     * @param  string|array  $param
     */
    private static function normalizeFilterParam($param): array
    {
        if (is_string($param) && !empty($param)) {
            $array = explode(',', $param);
            $array = array_filter($array, 'is_numeric');

            return array_map('intval', $array);
        }
        if (is_array($param)) {
            return array_map('intval', array_filter($param, 'is_numeric'));
        }

        return [];
    }

    public static function getTotalProductsForQuery($query): int
    {
        $totalQuery = clone $query;

        return $totalQuery->select(DB::raw('COUNT(DISTINCT products.id) as total'))->value('total') ?? 0;
    }

    public static function getCurrentProductsBrands($query): object
    {
        $productBrands_ids = $query
            ->distinct('products.brand_id')
            ->select('products.brand_id as brand_id')
            ->get();

        // Get distinct brands from the filtered products
        return DB::table('brands')
            ->whereIn('id', $productBrands_ids->pluck('brand_id'))
            ->distinct()
            ->get()
            ->keyBy('id');
    }

    public static function getCurrentProductsFeatures($query, $language_id): object
    {
        $features = $query
            ->join('product_features as pf', 'pf.product_id', '=', 'products.id')
            ->join('feature_languages as fl', function (JoinClause $join) use ($language_id) {
                $join->on('pf.feature_id', '=', 'fl.feature_id')
                    ->where('fl.language_id', '=', $language_id); // Fetch category translation
            })
            ->join('features as feat', 'pf.feature_id', '=', 'feat.id')
            ->join('feature_groups as fg', 'feat.feature_group_id', '=', 'fg.id')
            ->join('feature_group_languages as fgl', function (JoinClause $join) use ($language_id) {
                $join->on('fg.id', '=', 'fgl.feature_group_id')
                    ->where('fgl.language_id', '=', $language_id); // Fetch category translation
            })
            ->select('pf.feature_id as id', 'fl.name as name', 'fgl.name as group_name', 'fgl.id as group_id')
            ->get();

        return $features;
    }

        /**
     * Calculate the price for this product including specific price rules.
     * Returns the base price with the best applicable specific price applied (in base currency).
     *
     * @param  float  $basePrice  Base price from product or variation
     * @param  int  $quantity  Quantity for quantity-based specific prices
     * @param  int|null  $currencyId  Currency ID (default: current currency)
     * @param  int|null  $clientGroupId  Client group ID for group-specific prices
     * @param  int|null  $customerId  Customer ID for customer-specific prices
     * @return float
     */
    public static function getPrice(
        int $productId,
        ?int $variationId = null,
        ?float $price = null,
        int $quantity = 1,
        bool $with_taxes = false,
        ?int $currencyId = null,
        ?array $clientGroupIds = null,
        ?int $customerId = null,
    ): array {
        $currencyId = $currencyId ?? Currency::getCurrentCurrency()?->id;
        $data = [
            'price_without_taxes' => $price,
            'price_with_taxes' => $price,
            'tax' => null,
        ];
        //filter by specific prices
        $data['price_without_taxes'] = self::applySpecificPrices($productId, $variationId, $price, $quantity, $with_taxes, $currencyId, $clientGroupIds, $customerId);
        if ($with_taxes) {
            $data = self::applyTaxes($data['price_without_taxes']);
        }
        return $data;
    }

    public static function applySpecificPrices(
        int $productId,
        int $variationId = null,
        ?float $basePrice = null,
        int $quantity = 1,
        bool $with_taxes = false,
        ?int $currencyId = null,
        ?array $clientGroupIds = null,
        ?int $customerId = null
    )
    {
        $specificPrices = SpecificPrice::where('id_product', $productId)
            ->where(function ($query) use ($currencyId) {
                $query->where('id_currency', $currencyId)
                    ->orWhere('id_currency', 0)
                    ->orWhereNull('id_currency');
            })
            ->where(function ($query) use ($clientGroupIds) {
                if ($clientGroupIds) {
                    $query->whereIn('id_client_type', $clientGroupIds)
                        ->orWhereNull('id_client_type');
                }
            })
            ->where(function ($query) use ($customerId) {
                if ($customerId) {
                    $query->where('id_customer', $customerId)
                        ->orWhere('id_customer', 0);
                } else {
                    $query->where('id_customer', 0)
                        ->orWhereNull('id_customer');
                }
            })
            ->where(function ($query) use ($quantity) {
                $query->where('from_quantity', '<=', $quantity)
                    ->orWhereNull('from_quantity')
                    ->orWhere('from_quantity', 0);
            })
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->where('from', '<=', now())
                        ->orWhereNull('from');
                })
                    ->where(function ($q) {
                        $q->where('to', '>=', now())
                            ->orWhereNull('to');
                    });
            })
            ->orderBy('from_quantity', 'desc')
            ->orderBy('id', 'desc')
            ->get();
        $bestPrice = $basePrice;
        foreach ($specificPrices as $specificPrice) {
            $calculatedPrice = $basePrice;

            if ($specificPrice->price && $specificPrice->price > 0) {
                $calculatedPrice = (float) $specificPrice->price;
            } elseif ($specificPrice->reduction && $specificPrice->reduction > 0) {
                if ($specificPrice->reduction_type === 'percentage') {
                    $calculatedPrice = $basePrice * (1 - ($specificPrice->reduction / 100));
                } else {
                    $calculatedPrice = $basePrice - $specificPrice->reduction;
                }
            }

            if ($calculatedPrice < $bestPrice) {
                $bestPrice = $calculatedPrice;
            }
        }

        return max(0.0, $bestPrice);
    }

        /**
     * Apply taxes to the provided price.
     *
     * @param  float  $price  The original price.
     * @return float Price after applying taxes.
     */
    public static function applyTaxes(
        float $price,
        ?int $id_country = null,
        ?int $id_state = null,
        ?int $id_zip = null,
    ): array
    {
        $tax = Tax::where('active', true)
            ->when($id_country, function (Builder $query) use ($id_country) {
                $query->where('id_country', $id_country);
            })
            ->when($id_state, function (Builder $query) use ($id_state) {
                $query->where('id_state', $id_state);
            })
            ->when($id_zip, function (Builder $query) use ($id_zip) {
                $query->where('id_zip', $id_zip);
            })->first();
        $price_with_taxes = $price;
        $tax_name = null;
        if ($tax) {
            $taxRate = $tax->rate / 100;
            $price_with_taxes = $price + ($price * $taxRate);
            $tax_name = $tax->name;
        }
        return [
            'price_without_taxes' => $price,
            'price_with_taxes' => $price_with_taxes,
            'tax' => $tax_name,
        ];
    }

    // /**
    //  * Retrieve the product's price based on season, stock, or default product pricing.
    //  *
    //  * @param  int  $product_id  The product ID to fetch the price for.
    //  * @param  int|null  $stock_id  (Optional) Stock ID for stock-specific pricing.
    //  * @param  int|null  $season_id  (Optional) Season ID for season-specific pricing.
    //  * @param  bool  $with_taxes  (Optional) Whether the price should include taxes.
    //  * @return float The calculated price, or 0 if no valid price is found.
    //  */
    // public static function getPrice(int $product_id, ?int $stock_id = null, ?int $season_id = null, bool $with_taxes = false): float
    // {
    //     // Fetch the product
    //     $product = static::find($product_id);
    //     if (!$product) {
    //         return 0.0; // Return 0 if product is not found
    //     }

    //     // Check season-specific pricing if season_id is provided
    //     if (!empty($season_id)) {
    //         $seasonPrice = self::getSeasonPrice($product_id, $season_id);
    //         if ($seasonPrice !== null) {
    //             return $with_taxes ? self::applyTaxes($seasonPrice) : $seasonPrice;
    //         }
    //     }

    //     // Check stock-specific pricing if stock_id is provided
    //     if (!empty($stock_id)) {
    //         $stockPrice = self::getStockPrice($stock_id);
    //         if ($stockPrice !== null) {
    //             return $with_taxes ? self::applyTaxes($stockPrice) : $stockPrice;
    //         }
    //     }

    //     // Default to product price
    //     $productPrice = $product->price > 0 ? (float) $product->price : 0.0;

    //     return $with_taxes ? self::applyTaxes($productPrice) : $productPrice;
    // }

    /**
     * Retrieve the season-specific price for a product.
     *
     * @param  int  $product_id  Product ID.
     * @param  int  $season_id  Season ID.
     * @return float|null The season price, or null if not found.
     */
    private static function getSeasonPrice(int $product_id, int $season_id): ?float
    {
        return SeasonProduct::where('product_id', $product_id)
            ->where('season_id', $season_id)
            ->where('price', '>', 0)
            ->value('price'); // Use value() to directly fetch the price
    }

    /**
     * Retrieve the stock-specific price for a product.
     *
     * @param  int  $stock_id  Stock ID.
     * @return float|null The stock price, or null if not found.
     */
    private static function getStockPrice(int $stock_id): ?float
    {
        return Stock::where('id', $stock_id)
            ->where('price', '>', 0)
            ->value('price');
    }


    public static function getCurrentProductsCategories($query, $language_id): object
    {
        return $query
            ->join('product_product_category as ppc', 'ppc.product_id', '=', 'products.id')
            ->join('product_category_languages as pcl', function (JoinClause $join) use ($language_id) {
                $join->on('ppc.product_category_id', '=', 'pcl.product_category_id')
                    ->where('pcl.language_id', '=', $language_id); // Fetch category translation
            })
            ->select('ppc.product_category_id as id', 'pcl.name as name')
            ->distinct()
            ->get();
    }

    public static function updateTotQuantity($product_id, $save = true)
    {
        $product = static::find($product_id);

        if (!empty($product) && $product->type === 'variable') {
            $newQuantity = Stock::where('product_id', $product->id)->sum('quantity');

            if ($save === true) {
                DB::table('products')
                    ->where('id', $product->id)
                    ->update(['quantity' => $newQuantity]);
            } else {
                $product->quantity = $newQuantity;
            }
        }
    }

    /**
     * Modify the collection of models being made searchable.
     */
    public function makeSearchableUsing(Collection $models): Collection
    {
        return $models->load('currentLanguage')->load('images');
    }

    public function toSearchableArray(): array
    {
        $array = [
            'id' => (string) $this->id,
            'sku' => (string) ($this->sku ?? ''),
            'name' => (string) ($this->name ?? ''),
            'price' => (float) $this->price,
            'status' => (int) $this->status,
            'brand_id' => (int) $this->brand_id,
            'category_ids' => $this->categories->pluck('id')->map(fn($id) => (int) $id)->toArray(),
            'language_name' => '',
            'language_description_long' => '',
            'language_description_short' => '',
            'link_rewrite' => '',
            'season_ids' => [],
            'quantity' => (int) ($this->getQuantityForStockDisplay() ?? 0),
            'created_at' => $this->created_at ? $this->created_at->unix() : 0,
            'updated_at' => $this->updated_at ? $this->updated_at->unix() : 0,
        ];

        if ($this->currentLanguage) {
            $array['language_name'] = (string) ($this->currentLanguage->name ?? '');
            $array['language_description_long'] = (string) ($this->currentLanguage->description_long ?? '');
            $array['language_description_short'] = (string) ($this->currentLanguage->description_short ?? '');
            $array['link_rewrite'] = (string) ($this->currentLanguage->link_rewrite ?? '');
        }

        if (!empty($this->seasons) && is_countable($this->seasons)) {
            $array['season_ids'] = $this->seasons->pluck('id')->map(fn($id) => (int) $id)->toArray();
        }

        return $array;
    }


    public static function getProductBySku(string $sku, bool $only_id = false)
    {
        $product = Product::where('sku', $sku)->first();
        if ($only_id === true) {
            return $product->id ?? null;
        }

        return $product;
    }

    public static function export($brand = '')
    {
        $products_array = [];
        if (!empty($brand)) {
            $list_products = self::where('brand_id', $brand)->with(['currentLanguage', 'categories', 'features', 'stocks']);
        } else {
            $list_products = self::with(['currentLanguage', 'categories', 'features', 'stocks']);
        }

        $list_products->chunk(100, function ($products) use (&$products_array) {
            foreach ($products as $product) {
                foreach ($product->stocks as $stock) {
                    $variantLabel = optional($stock->variant->first())->getNameAttribute() ?? '';
                    $products_array[] = [
                        'SKU' => $product->sku,
                        'EAN' => $stock->ean,
                        'NOME' => $product->currentLanguage->name,
                        'DESCRIZIONE LUNGA' => $product->currentLanguage->description_long,
                        'DESCRIZIONE CORTA' => $product->currentLanguage->description_short,
                        'PREZZO' => $product->price,
                        'BRAND' => $product->brand->name ?? '',
                        'TAGLIA' => $variantLabel,
                        'CATEGORIA' => $product->categories->where('id', $product->defaultCategoryId())->pluck('title')->first(),
                        'SOTTOCATEGORIA' => implode(';', $product->categories->where('id', '<>', $product->defaultCategoryId())->pluck('title')->toArray()),
                        'GENERE' => $product->features->where('feature_group_id', 1)->pluck('title')->first(),
                        'ETA' => $product->features->where('feature_group_id', 2)->pluck('title')->first(),
                        'COLORE' => $product->features->where('feature_group_id', 3)->pluck('title')->first(),
                    ];
                }
            }
        });

        return $products_array;
    }

    /**
     * Export one row per product using products.quantity (no stocks table).
     * Use when stocks table is not used.
     */
    public static function exportUsingProductQuantity(string $brand = ''): array
    {
        $products_array = [];
        $query = !empty($brand)
            ? self::where('brand_id', $brand)->with(['currentLanguage', 'categories', 'features'])
            : self::with(['currentLanguage', 'categories', 'features']);

        $query->chunk(100, function ($products) use (&$products_array) {
            foreach ($products as $product) {
                $products_array[] = [
                    'SKU' => $product->sku,
                    'EAN' => $product->ean ?? '',
                    'NOME' => $product->currentLanguage->name,
                    'DESCRIZIONE LUNGA' => $product->currentLanguage->description_long,
                    'DESCRIZIONE CORTA' => $product->currentLanguage->description_short,
                    'PREZZO' => $product->price,
                    'BRAND' => $product->brand->name ?? '',
                    'TAGLIA' => '',
                    'CATEGORIA' => $product->categories->where('id', $product->defaultCategoryId())->pluck('title')->first(),
                    'SOTTOCATEGORIA' => implode(';', $product->categories->where('id', '<>', $product->defaultCategoryId())->pluck('title')->toArray()),
                    'GENERE' => $product->features->where('feature_group_id', 1)->pluck('title')->first(),
                    'ETA' => $product->features->where('feature_group_id', 2)->pluck('title')->first(),
                    'COLORE' => $product->features->where('feature_group_id', 3)->pluck('title')->first(),
                ];
            }
        });

        return $products_array;
    }
}
