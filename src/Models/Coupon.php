<?php

namespace Unusualdope\LaravelEcommerce\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Unusualdope\LaravelEcommerce\Models\Cart\Cart;

class Coupon extends Model
{
    protected $casts = [
        'start_date'        => 'datetime',
        'type'              => 'string',
        'value'             => 'decimal:2',
        'end_date'          => 'datetime',
        'minimum_spend'     => 'decimal:2',
        'maximum_spend'     => 'decimal:2',
        'limit_use_by_user' => 'integer',
        'limit_use_by_coupon' => 'integer',
        'include_brands'    => 'array',
        'exclude_brands'    => 'array',
        'include_categories' => 'array',
        'exclude_categories' => 'array',
        'include_products'  => 'array',
        'exclude_products'  => 'array',
        'active'            => 'boolean',
    ];

    // =========================================================================
    // Lookups
    // =========================================================================

    /**
     * Find an active coupon by code (case-insensitive).
     * Returns null when not found.
     */
    public static function findByCode(string $code): ?static
    {
        return static::whereRaw('LOWER(code) = ?', [strtolower(trim($code))])->first();
    }

    /**
     * Retrieve the coupon currently attached to the given cart, or null.
     */
    public static function getFromCart(Cart $cart): ?static
    {
        if (! $cart->coupon_id) {
            return null;
        }

        $coupon = static::find($cart->coupon_id);

        return ($coupon && $coupon->isActive()) ? $coupon : null;
    }

    // =========================================================================
    // Status check
    // =========================================================================

    public function isActive(): bool
    {
        $now = now();

        return $this->active
            && $this->start_date <= $now
            && $this->end_date   >= $now;
    }

    // =========================================================================
    // Cart operations
    // =========================================================================

    /**
     * Attach this coupon to the given cart and persist it.
     */
    public function applyToCart(Cart $cart): void
    {
        DB::table('carts')
            ->where('id', $cart->id)
            ->update(['coupon_id' => $this->id, 'updated_at' => now()]);
    }

    /**
     * Detach any coupon from the given cart and persist.
     */
    public static function removeFromCart(Cart $cart): void
    {
        DB::table('carts')
            ->where('id', $cart->id)
            ->update(['coupon_id' => null, 'updated_at' => now()]);
    }

    // =========================================================================
    // Validation
    // =========================================================================

    /**
     * Validate this coupon against the given cart's contents and totals.
     *
     * Returns a translated error string on failure, or null on success.
     *
     * @param  Cart        $cart
     * @param  object|null $totals  Result of Cart::getTotalsForSummary()
     */
    public function validateForCart(Cart $cart, ?object $totals): ?string
    {
        // Fetch product IDs + brand IDs from cart_details
        $cartProducts = DB::table('cart_details as cd')
            ->join('products as p', 'cd.product_id', '=', 'p.id')
            ->where('cd.cart_id', $cart->id)
            ->select('p.id as product_id', 'p.brand_id')
            ->distinct()
            ->get();

        if ($cartProducts->isEmpty()) {
            return __('front-ecommerce::cart.coupon_invalid');
        }

        $productIds  = $cartProducts->pluck('product_id')->toArray();
        $brandIds    = $cartProducts->pluck('brand_id')->filter()->unique()->toArray();

        // Category IDs for all cart products
        $categoryIds = DB::table('product_product_category')
            ->whereIn('product_id', $productIds)
            ->pluck('product_category_id')
            ->unique()
            ->toArray();

        // --- include_products ---
        $includeProducts = $this->include_products ?? [];
        if (! empty($includeProducts)) {
            if (empty(array_intersect($productIds, array_map('intval', $includeProducts)))) {
                return __('front-ecommerce::cart.coupon_not_applicable');
            }
        }

        // --- exclude_products (invalid only if ALL items are excluded) ---
        $excludeProducts = $this->exclude_products ?? [];
        if (! empty($excludeProducts)) {
            $excluded = array_intersect($productIds, array_map('intval', $excludeProducts));
            if (count($excluded) === count($productIds)) {
                return __('front-ecommerce::cart.coupon_not_applicable');
            }
        }

        // --- include_categories ---
        $includeCategories = $this->include_categories ?? [];
        if (! empty($includeCategories)) {
            if (empty(array_intersect($categoryIds, array_map('intval', $includeCategories)))) {
                return __('front-ecommerce::cart.coupon_not_applicable');
            }
        }

        // --- exclude_categories (invalid only if ALL categories are excluded) ---
        $excludeCategories = $this->exclude_categories ?? [];
        if (! empty($excludeCategories)) {
            if (empty(array_diff($categoryIds, array_map('intval', $excludeCategories)))) {
                return __('front-ecommerce::cart.coupon_not_applicable');
            }
        }

        // --- include_brands ---
        $includeBrands = $this->include_brands ?? [];
        if (! empty($includeBrands)) {
            if (empty(array_intersect($brandIds, array_map('intval', $includeBrands)))) {
                return __('front-ecommerce::cart.coupon_not_applicable');
            }
        }

        // --- exclude_brands (invalid only if ALL brands are excluded) ---
        $excludeBrands = $this->exclude_brands ?? [];
        if (! empty($excludeBrands)) {
            if (empty(array_diff($brandIds, array_map('intval', $excludeBrands)))) {
                return __('front-ecommerce::cart.coupon_not_applicable');
            }
        }

        // --- minimum_spend ---
        if ($this->minimum_spend && $totals && $totals->grand_total < (float) $this->minimum_spend) {
            return __('front-ecommerce::cart.coupon_minimum_spend', [
                'amount' => number_format($this->minimum_spend, 2),
            ]);
        }

        // --- maximum_spend ---
        if ($this->maximum_spend && $totals && $totals->grand_total > (float) $this->maximum_spend) {
            return __('front-ecommerce::cart.coupon_maximum_spend', [
                'amount' => number_format($this->maximum_spend, 2),
            ]);
        }

        return null;
    }

    // =========================================================================
    // Discount calculation
    // =========================================================================

    /**
     * Calculate the discount amount against the given grand total.
     */
    public function calculateDiscount(float $grandTotal): float
    {
        if ($grandTotal <= 0) {
            return 0.0;
        }

        if ($this->type === 'percentage') {
            return round($grandTotal * ((float) $this->value / 100), 2);
        }

        // fixed — cannot exceed the grand total
        return min(round((float) $this->value, 2), $grandTotal);
    }
}
