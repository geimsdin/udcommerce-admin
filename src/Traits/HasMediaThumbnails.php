<?php

namespace Unusualdope\LaravelEcommerce\Traits;

use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Unusualdope\LaravelEcommerce\Models\ImageSetting;

trait HasMediaThumbnails
{
    use InteractsWithMedia;

    /**
     * Register media conversions dynamically from the image_settings table.
     * Each model defines getMediaEntityType() returning 'product', 'brand', or 'category'.
     * All matching image types for that entity will be registered as Spatie conversions.
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        $entityType = $this->getMediaEntityType();

        try {
            $imageSettings = ImageSetting::getForEntity($entityType);
        } catch (\Exception $e) {
            // Table might not exist yet (during migrations), silently skip
            return;
        }

        foreach ($imageSettings as $imageSetting) {
            $this->addMediaConversion($imageSetting->name)
                ->width($imageSetting->width)
                ->height($imageSetting->height)
                ->sharpen(10)
                ->nonQueued();
        }
    }

    /**
     * Get the entity type string used for filtering image types.
     * Override in each model: 'product', 'brand', 'category'
     */
    abstract public function getMediaEntityType(): string;
}
