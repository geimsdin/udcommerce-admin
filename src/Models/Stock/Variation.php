<?php

namespace Unusualdope\LaravelEcommerce\Models\Stock;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Unusualdope\LaravelEcommerce\Models\Product\Product;
use Unusualdope\LaravelEcommerce\Models\Product\ProductImage;

class Variation extends Model
{
    use Cachable;

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variants()
    {
        return $this->belongsToMany(Variant::class, 'variant_variations', 'variation_id', 'variant_id');
    }

    public function image()
    {
        return $this->hasOne(ProductImage::class, 'variation_id');
    }

    /**
     * Get variant labels for this variation (e.g. ["Size" => "S", "Color" => "Red"]) for display in one row.
     */
    public function getVariantLabelsForLanguage(int $languageId): array
    {
        $labels = [];
        $variants = $this->relationLoaded('variants') ? $this->variants : $this->variants()->with('variantGroup')->get();

        foreach ($variants as $variant) {
            $groupLang = $variant->variantGroup->getSpecificLanguage($languageId);
            $groupName = ($groupLang && $groupLang->name) ? $groupLang->name : $variant->variantGroup->getNameAttribute();
            $variantName = $variant->getNameCurrentLanguage($languageId) ?: $variant->getNameAttribute();
            if ($groupName) {
                $labels[$groupName] = $variantName;
            }
        }

        return $labels;
    }

    public function getCombinationNameAttribute()
    {
        return $this->variants->map(function ($variant) {
            return $variant->name;
        })->join(' ');
    }

    public function increaseQuantity($quantity)
    {
        $this->quantity += $quantity;
        $this->save();
    }

    public function decreaseQuantity($quantity)
    {
        $this->quantity -= $quantity;
        $this->save();
    }
}
