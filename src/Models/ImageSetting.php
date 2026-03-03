<?php

namespace Unusualdope\LaravelEcommerce\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;

class ImageSetting extends Model
{
    use Cachable;

    protected $table = 'image_settings';

    protected $fillable = [
        'name',
        'width',
        'height',
        'products',
        'categories',
        'brands',
    ];

    protected $casts = [
        'width' => 'integer',
        'height' => 'integer',
        'products' => 'boolean',
        'categories' => 'boolean',
        'brands' => 'boolean',
    ];

    /**
     * Get all image settings for a given entity type.
     *
     * @param string $entityType 'product', 'category', or 'brand'
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getForEntity(string $entityType): \Illuminate\Database\Eloquent\Collection
    {
        $column = match ($entityType) {
            'product' => 'products',
            'category' => 'categories',
            'brand' => 'brands',
            default => throw new \InvalidArgumentException("Unknown entity type: {$entityType}"),
        };

        return static::where($column, true)->get();
    }

    /**
     * Get the default image settings to seed.
     */
    public static function getDefaults(): array
    {
        return [
            [
                'name' => 'cart_default',
                'width' => 80,
                'height' => 80,
                'products' => true,
                'categories' => false,
                'brands' => false,
            ],
            [
                'name' => 'small_default',
                'width' => 125,
                'height' => 125,
                'products' => true,
                'categories' => true,
                'brands' => true,
            ],
            [
                'name' => 'medium_default',
                'width' => 452,
                'height' => 452,
                'products' => true,
                'categories' => true,
                'brands' => true,
            ],
            [
                'name' => 'large_default',
                'width' => 800,
                'height' => 800,
                'products' => true,
                'categories' => false,
                'brands' => false,
            ],
            [
                'name' => 'home_default',
                'width' => 250,
                'height' => 250,
                'products' => true,
                'categories' => true,
                'brands' => false,
            ],
            [
                'name' => 'category_default',
                'width' => 141,
                'height' => 180,
                'products' => false,
                'categories' => true,
                'brands' => false,
            ],
        ];
    }
}
