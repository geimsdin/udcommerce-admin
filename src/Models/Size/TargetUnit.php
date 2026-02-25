<?php

namespace Unusualdope\LaravelEcommerce\Models\Size;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TargetUnit extends Model
{
    use Cachable;

    protected $fillable = [
        'name',
    ];

    public function sizechartentry(): HasMany
    {
        return $this->hasMany(SizeChartEntry::class);
    }
}
