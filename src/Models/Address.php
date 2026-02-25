<?php

namespace Unusualdope\LaravelEcommerce\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Unusualdope\LaravelEcommerce\Models\Customer\Client;

class Address extends Model
{
    use Cachable;

    protected $fillable = [
        'user_id',
        'client_id',
        'destination_name',
        'default',
        'name',
        'first_name',
        'last_name',
        'company',
        'vat_number',
        'address1',
        'address2',
        'postcode',
        'city',
        'province',
        'country',
        'phone',
        'custom_fields',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'default' => 'boolean',
        'custom_fields' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        // Handle the "saving" event
        static::saving(function ($address) {
            if (!$address->client_id) {
                return;
            }

            // If this address is being set as default
            if ($address->default) {
                // Remove "default" from all other addresses with the same client_id
                self::where('client_id', $address->client_id)
                    ->where('default', true)
                    ->update(['default' => false]);
            } else {
                // If no address is set as default for this client, set this one as default
                $hasDefault = self::where('client_id', $address->client_id)
                    ->where('default', true)
                    ->exists();

                if (!$hasDefault) {
                    $address->default = true;
                }
            }
        });
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }
}
