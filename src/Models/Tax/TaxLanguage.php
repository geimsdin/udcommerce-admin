<?php

namespace Unusualdope\LaravelEcommerce\Models\Tax;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaxLanguage extends Model
{
    use Cachable;

    protected $table = 'tax_languages';

    protected $fillable = [
        'tax_id',
        'language_id',
        'name',
    ];

    public function tax(): BelongsTo
    {
        return $this->belongsTo(Tax::class, 'tax_id');
    }

}

