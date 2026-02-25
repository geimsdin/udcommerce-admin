<?php

namespace Unusualdope\LaravelEcommerce\Models;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    protected $table = 'ec_configurations';

    public static function getValue($name, $default = null)
    {
        $config = static::where('key', $name)->first();

        return $config ? $config->value : $default;
    }

    public static function getValues($names, $default = null)
    {
        $configs = static::whereIn('key', $names)->get()->keyBy('key');
        $results = [];

        foreach ($names as $name) {
            if ($configs->has($name)) {
                $results[$name] = $configs->get($name)->value;
            } else {
                $results[$name] = $default;
            }
        }

        return $results;
    }

    public static function setValue($key, $value)
    {
        $config = self::where('key', $key)->first();

        if ($config) {
            $config->update(['value' => $value]);
        } else {
            self::create(['key' => $key, 'value' => $value]);
        }
    }

    public static function setValues($values)
    {
        if (empty($values)) {
            return;
        }
        $data = [];

        foreach ($values as $key => $value) {
            $data[] = [
                'key' => $key,
                'value' => $value,
            ];
        }

        self::upsert(
            $data,
            ['key'],
            ['value']
        );
    }

    public static function resetValue($key)
    {
        self::where('key', $key)->delete();
    }

    public static function resetValues($keys)
    {
        self::whereIn('key', $keys)->delete();
    }
}
