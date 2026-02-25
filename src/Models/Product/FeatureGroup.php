<?php

namespace Unusualdope\LaravelEcommerce\Models\Product;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Unusualdope\LaravelModelTranslatable\Traits\HasTranslation;

class FeatureGroup extends Model
{
    use Cachable, HasTranslation;

    public function setTranslatableFields(): array
    {
        $this->translatable_fields = [
            'name' => 'string',
            'tooltip' => 'string',
        ];

        return $this->translatable_fields;
    }

    public function features(): HasMany
    {
        return $this->hasMany(Feature::class);
    }

    public function getNameAttribute()
    {
        return $this->currentLanguage->name;
    }

    public function getTooltipAttribute()
    {
        return $this->currentLanguage->tooltip;
    }
}
