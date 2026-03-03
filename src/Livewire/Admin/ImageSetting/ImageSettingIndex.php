<?php

namespace Unusualdope\LaravelEcommerce\Livewire\Admin\ImageSetting;

use Flux\Flux;
use Livewire\Component;
use Unusualdope\LaravelEcommerce\Models\ImageSetting;

class ImageSettingIndex extends Component
{
    public function delete(int $id): void
    {
        $imageSetting = ImageSetting::findOrFail($id);
        $imageSetting->delete();
        Flux::toast(__('ecommerce::image-settings.image_type_deleted'));
    }

    public function seedDefaults(): void
    {
        foreach (ImageSetting::getDefaults() as $default) {
            ImageSetting::firstOrCreate(
                ['name' => $default['name']],
                $default
            );
        }
        Flux::toast(__('ecommerce::image-settings.defaults_seeded'));
    }

    public function regenerateThumbnails(): void
    {
        \Illuminate\Support\Facades\Artisan::call('media-library:regenerate', [
            '--force' => true,
        ]);
        Flux::toast(__('ecommerce::image-settings.thumbnails_regenerated'));
    }

    public function render()
    {
        return view('ecommerce::livewire.admin.image-setting.image-setting-index', [
            'imageSettings' => ImageSetting::orderBy('name')->get(),
        ]);
    }
}
