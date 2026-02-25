<?php

namespace Unusualdope\LaravelEcommerce\Models\Product;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeasonLanguage extends Model
{
    use Cachable;

    protected $fillable = [
        'season_id',
        'language_id',
        'name',
    ];

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }
}
