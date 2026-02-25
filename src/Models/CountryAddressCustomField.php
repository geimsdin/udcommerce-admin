<?php

namespace Unusualdope\LaravelEcommerce\Models;

use Illuminate\Database\Eloquent\Model;

class CountryAddressCustomField extends Model
{
    protected $fillable = [
        'country',
        'name',
        'label',
        'type',
        'is_required',
        'min_length',
        'max_length',
        'is_active',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active' => 'boolean',
        'min_length' => 'integer',
        'max_length' => 'integer',
    ];

    /**
     * Scope a query to only include active custom fields.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include custom fields for a specific country.
     */
    public function scopeForCountry($query, $countryCode)
    {
        return $query->where('country', strtoupper($countryCode));
    }
}
