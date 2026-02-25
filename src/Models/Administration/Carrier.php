<?php

namespace Unusualdope\LaravelEcommerce\Models\Administration;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Unusualdope\LaravelModelTranslatable\Traits\HasTranslation;

class Carrier extends Model
{
    use Cachable, HasTranslation;

    public function setTranslatableFields(): array
    {
        $this->translatable_fields = [
            'name' => 'string',
            'description' => 'string',
        ];

        return $this->translatable_fields;
    }

    public function getNameAttribute(): ?string
    {
        return $this->currentLanguage?->name ?? null;
    }

    public function getDescriptionAttribute(): ?string
    {
        return $this->currentLanguage?->description ?? null;
    }
}
