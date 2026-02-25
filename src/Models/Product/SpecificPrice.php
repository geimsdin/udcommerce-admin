<?php

namespace Unusualdope\LaravelEcommerce\Models\Product;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Unusualdope\LaravelEcommerce\Models\Administration\Currency;
use Unusualdope\LaravelEcommerce\Models\Customer\Client;
use Unusualdope\LaravelEcommerce\Models\Customer\ClientGroup;

class SpecificPrice extends Model
{
    use Cachable;

    protected $fillable = [
        'id_product',
        'id_currency',
        'id_client_type',
        'id_customer',
        'price',
        'from_quantity',
        'reduction',
        'reduction_tax',
        'reduction_type',
        'from',
        'to',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'from_quantity' => 'integer',
        'reduction' => 'decimal:2',
        'reduction_tax' => 'boolean',
        'from' => 'datetime',
        'to' => 'datetime',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'id_product', 'id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'id_currency', 'id');
    }

    public function clientGroup(): BelongsTo
    {
        return $this->belongsTo(ClientGroup::class, 'id_client_type', 'id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'id_customer', 'id');
    }
}
