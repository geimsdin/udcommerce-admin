<?php

namespace Unusualdope\LaravelEcommerce\Models\Stock;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VariantGroupLanguage extends Model
{
    use Cachable;

    protected $fillable = [
        'variant_group_id',
        'language_id',
        'name',
        'tooltip',
    ];

    public function variantGroup(): BelongsTo
    {
        return $this->belongsTo(VariantGroup::class);
    }
}
