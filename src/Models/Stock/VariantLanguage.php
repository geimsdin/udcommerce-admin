<?php

namespace Unusualdope\LaravelEcommerce\Models\Stock;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VariantLanguage extends Model
{
    use Cachable;

    protected $fillable = [
        'variant_id',
        'language_id',
        'name',
    ];

    public function variant(): BelongsTo
    {
        return $this->belongsTo(Variant::class);
    }
}
