<?php

namespace Unusualdope\LaravelEcommerce\Models\Administration;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use Cachable;

    public static function getCurrentCurrency(): Currency
    {
        if (session()->has('currency')) {
            return session()->get('currency');
        }
        return self::getDefaultCurrency();
    }

    public static function getDefaultCurrency(): Currency
    {
        return self::where('default', 1)->first();
    }

    public function getSymbolAttribute(): string
    {
        return config('currencies.' . $this->iso_code);
    }

}
