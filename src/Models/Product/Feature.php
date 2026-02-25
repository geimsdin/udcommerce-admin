<?php

namespace Unusualdope\LaravelEcommerce\Models\Product;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Unusualdope\LaravelModelTranslatable\Traits\HasTranslation;

class Feature extends Model
{
    use Cachable, HasTranslation;

    public function setTranslatableFields(): array
    {
        $this->translatable_fields = [
            'name' => 'string',
        ];

        return $this->translatable_fields;
    }

    public function featureGroup(): BelongsTo
    {
        return $this->belongsTo(FeatureGroup::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'product_features',   // pivot table name
            'feature_id',         // foreign key on pivot table
            'product_id'          // related key on pivot table
        )
            ->using(ProductFeature::class) // use the custom pivot class
            ->withTimestamps();            // only if you have created_at/updated_at
    }

    public function getNameAttribute()
    {
        return $this->currentLanguage->name;
    }
}
