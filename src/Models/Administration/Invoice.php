<?php

namespace Unusualdope\LaravelEcommerce\Models\Administration;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Unusualdope\LaravelEcommerce\Models\Customer\Client;

class Invoice extends Model
{
    use Cachable;

    public static array $payment_type = [1, 2, 3, 4, 5];

    protected $casts = [
        'file' => 'array',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
