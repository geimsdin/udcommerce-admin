<?php

namespace Unusualdope\LaravelEcommerce\Models\Size;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Unusualdope\LaravelEcommerce\Models\Stock\Variant;

class SizeChartEntry extends Model
{
    use Cachable;

    protected $fillable = [
        'size_chart_id',
        'variant_id',
        'target_unit_id',
        'converted_value',
    ];

    public function sizechart(): BelongsTo
    {
        return $this->belongsTo(SizeChart::class, 'size_chart_id');
    }

    public function targetunit(): BelongsTo
    {
        return $this->belongsTo(TargetUnit::class, 'target_unit_id');
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(Variant::class);
    }
}
