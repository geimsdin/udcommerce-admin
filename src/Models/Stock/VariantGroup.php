<?php

namespace Unusualdope\LaravelEcommerce\Models\Stock;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Unusualdope\LaravelModelTranslatable\Traits\HasTranslation;

class VariantGroup extends Model
{
    use Cachable, HasTranslation;

    protected static function booted(): void
    {
        static::created(function (VariantGroup $variantGroup) {
            $variantGroup->position = VariantGroup::max('position') + 1;
            $variantGroup->save();
        });
        static::deleted(function (VariantGroup $variantGroup) {
            $variantGroup->variants()->delete();

            static::where('position', '>', $variantGroup->position)
                ->decrement('position');
        });
    }

    public function setTranslatableFields(): array
    {
        $this->translatable_fields = [
            'name' => 'string',
            'tooltip' => 'string',
        ];

        return $this->translatable_fields;
    }

    public function variants(): HasMany
    {
        return $this->hasMany(Variant::class);
    }

    public function languages(): HasMany
    {
        return $this->hasMany(VariantGroupLanguage::class, 'variant_group_id');
    }

    public function getSpecificLanguage(int $languageId)
    {
        return $this->languages()
            ->where('language_id', $languageId)
            ->first();
    }

    public function getNameAttribute()
    {
        return $this->currentLanguage->name ?? '';
    }

    public function getGroupNameAttribute()
    {
        return $this->currentLanguage->name ?? '';
    }
}
