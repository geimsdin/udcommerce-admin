<?php

namespace Unusualdope\LaravelEcommerce\Models\Stock;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Unusualdope\LaravelEcommerce\Models\Size\SizeChartEntry;
use Unusualdope\LaravelModelTranslatable\Traits\HasTranslation;

class Variant extends Model
{
    use Cachable, HasTranslation;

    protected $fillable = [
        'variant_group_id',
        'color',
        'position',
    ];

    protected static function booted(): void
    {
        static::created(function (Variant $variant) {
            $variant->position = Variant::max('position') + 1;
            $variant->save();
        });
    }

    public function setTranslatableFields(): array
    {
        $this->translatable_fields = [
            'name' => 'string',
        ];

        return $this->translatable_fields;
    }

    public function variantGroup(): BelongsTo
    {
        return $this->belongsTo(VariantGroup::class);
    }

    public function stock()
    {
        return $this->belongsToMany(Stock::class, 'stock_variants');
    }

    public function sizechartentry(): HasMany
    {
        return $this->hasMany(SizeChartEntry::class);
    }

    public function languages(): HasMany
    {
        return $this->hasMany(VariantLanguage::class, 'variant_id');
    }

    public function getSpecificLanguage(int $languageId)
    {
        return $this->languages()
            ->where('language_id', $languageId)
            ->first();
    }

    public function getNameCurrentLanguage(int $languageId): string
    {
        return $this->languages()
            ->where('language_id', $languageId)
            ->value('name');
    }

    public function getNameAttribute()
    {
        return $this->currentLanguage->name;
    }

    public function getVariantGroupNameAttribute()
    {
        return $this->variantGroup->getNameAttribute();
    }
}
