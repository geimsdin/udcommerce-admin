<?php

namespace Unusualdope\LaravelEcommerce\Models\Tax;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Unusualdope\LaravelModelTranslatable\Traits\HasTranslation;

class Tax extends Model
{
    use HasTranslation;

    protected $fillable = [
        'rate',
        'active',
        'id_country',
        'id_state',
        'zipcode_from',
        'zipcode_to',
    ];

    protected $casts = [
        'rate' => 'decimal:6',
        'active' => 'boolean',
        'id_country' => 'integer',
        'id_state' => 'integer',
    ];

    public function setTranslatableFields(): array
    {
        $this->translatable_fields = [
            'name' => 'string',
        ];

        return $this->translatable_fields;
    }

    public function getNameAttribute()
    {
        return $this->currentLanguage->name ?? '';
    }

    /**
     * Calculate tax amount for a given price
     */
    public function calculateTax(float $price, bool $taxIncluded = false): float
    {
        if ($taxIncluded) {
            return $price - ($price / (1 + ($this->rate / 100)));
        }
        return $price * ($this->rate / 100);
    }
}

