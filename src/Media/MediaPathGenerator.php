<?php

namespace Unusualdope\LaravelEcommerce\Media;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

/**
 * Custom path generator that organizes media files by collection name and model ID.
 *
 * Result structure:
 *   storage/app/public/media/product-images/42/original.jpg
 *   storage/app/public/media/product-images/42/conversions/original-cart_default.jpg
 *   storage/app/public/media/brand-images/7/logo.jpg
 *   storage/app/public/media/category-images/3/banner.jpg
 */
class MediaPathGenerator implements PathGenerator
{
    public function getPath(Media $media): string
    {
        return $this->getBasePath($media) . '/';
    }

    public function getPathForConversions(Media $media): string
    {
        return $this->getBasePath($media) . '/conversions/';
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getBasePath($media) . '/responsive-images/';
    }

    protected function getBasePath(Media $media): string
    {
        $collection = $media->collection_name ?: 'default';

        // Convert collection names to readable folder names
        // e.g. "product_image" -> "product-images", "brand_image" -> "brand-images"
        $folder = str_replace('_', '-', $collection);
        if (!str_ends_with($folder, 's')) {
            $folder .= 's';
        }

        return "media/{$folder}/{$media->id}";
    }
}
