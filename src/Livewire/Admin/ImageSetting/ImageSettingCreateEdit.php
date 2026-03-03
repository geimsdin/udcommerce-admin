<?php

namespace Unusualdope\LaravelEcommerce\Livewire\Admin\ImageSetting;

use Livewire\Component;
use Unusualdope\LaravelEcommerce\Models\ImageSetting;

class ImageSettingCreateEdit extends Component
{
    public ?ImageSetting $imageSetting = null;

    public bool $isEditing = false;

    public string $name = '';
    public int $width = 300;
    public int $height = 300;
    public bool $products = false;
    public bool $categories = false;
    public bool $brands = false;

    public function mount(?ImageSetting $imageSetting = null): void
    {
        $this->imageSetting = $imageSetting;
        if ($imageSetting?->exists) {
            $this->isEditing = true;
            $this->name = $imageSetting->name;
            $this->width = $imageSetting->width;
            $this->height = $imageSetting->height;
            $this->products = $imageSetting->products;
            $this->categories = $imageSetting->categories;
            $this->brands = $imageSetting->brands;
        }
    }

    public function save(): void
    {
        $rules = [
            'name' => ['required', 'string', 'max:64', 'regex:/^[a-z0-9_]+$/'],
            'width' => ['required', 'integer', 'min:10', 'max:4000'],
            'height' => ['required', 'integer', 'min:10', 'max:4000'],
            'products' => ['boolean'],
            'categories' => ['boolean'],
            'brands' => ['boolean'],
        ];

        if ($this->isEditing) {
            $rules['name'][] = 'unique:image_settings,name,' . $this->imageSetting->id;
        } else {
            $rules['name'][] = 'unique:image_settings,name';
        }

        $this->validate($rules, [
            'name.regex' => __('ecommerce::image-settings.form.name_regex_error'),
        ]);

        $data = [
            'name' => $this->name,
            'width' => $this->width,
            'height' => $this->height,
            'products' => $this->products,
            'categories' => $this->categories,
            'brands' => $this->brands,
        ];

        if ($this->isEditing) {
            $this->imageSetting->update($data);
            session()->flash('status', __('ecommerce::image-settings.image_type_updated'));
        } else {
            ImageSetting::create($data);
            session()->flash('status', __('ecommerce::image-settings.image_type_created'));
        }

        $this->redirect(
            route(config('ud-ecommerce.admin_route_prefix', 'admin') . '.image-settings.index'),
            navigate: true
        );
    }

    public function render()
    {
        return view('ecommerce::livewire.admin.image-setting.image-setting-create-edit', [
            'isEditing' => $this->isEditing,
        ]);
    }
}
