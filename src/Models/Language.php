<?php

namespace Unusualdope\LaravelEcommerce\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Context;

class Language extends Model
{
    public static function getLanguagesForMultilangForm()
    {
        if (Context::has('lmt_languages_fmlf')) {
            return Context::get('lmt_languages_fmlf');
        }
        $result = Language::select('id', 'name', 'is_default', 'iso_code')->get()->toArray();
        Context::add('lmt_languages_fmlf', $result);

        return $result;
    }

    public static function getDefaultLanguage()
    {
        if (Cache::has('lmt_lang_default')) {
            return Cache::get('lmt_lang_default');
        }
        $result = self::where('is_default', true)->value('id');
        Cache::set('lmt_lang_default', $result);

        return $result;
    }

    public static function getCurrentLanguage()
    {
        if (Context::has('lmt_current_language')) {
            return Context::get('lmt_current_language');
        }
        $result = self::where('is_default', true)->value('id');
        Cache::set('lmt_current_language', $result);

        return $result;
    }
}
