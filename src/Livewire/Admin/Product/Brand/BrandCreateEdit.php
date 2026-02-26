<?php

namespace Unusualdope\LaravelEcommerce\Livewire\Admin\Product\Brand;

use Flux\Flux;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Unusualdope\LaravelEcommerce\Models\Product\Brand;

class BrandCreateEdit extends Component
{
    use WithFileUploads;

    public ?Brand $brand = null;
    public $name;
    public $company_name;
    public $description;
    public $address;
    public $city;
    public $state;
    public $country;
    public $tel;
    public $email;
    public $image = null;

    public ?string $existingImage = null;

    public function mount(?Brand $brand = null): void
    {
        $this->brand = $brand;
        if ($brand?->exists) {
            $this->name = $brand->name;
            $this->description = $brand->description;
            $this->company_name = $brand->company_name;
            $this->address = $brand->address;
            $this->city = $brand->city;
            $this->state = $brand->state;
            $this->country = $brand->country;
            $this->tel = $brand->tel;
            $this->email = $brand->email;
            $this->existingImage = $brand->image;
        }
    }

    public function deleteImage(): void
    {
        if ($this->existingImage && $this->brand?->exists) {
            Storage::disk('public')->delete($this->existingImage);
            $this->brand->update(['image' => null]);
            $this->existingImage = null;
            Flux::toast(__('ecommerce::brands.brand_logo_deleted'));
        }

        // Clear temporary upload
        $this->image = null;
    }

    public function save(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'tel' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'image' => ['nullable', 'image', 'max:2048'], // 2MB max
        ]);

        $fields = [
            'name' => $this->name,
            'description' => $this->description,
            'company_name' => $this->company_name,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'country' => $this->country,
            'tel' => $this->tel,
            'email' => $this->email,
        ];

        // Handle new image upload
        if ($this->image) {
            // Delete old image if exists
            if ($this->existingImage) {
                Storage::disk('public')->delete($this->existingImage);
            }
            $fields['image'] = $this->image->store('brands', 'public');
        }

        if ($this->brand?->exists) {
            $this->brand->update($fields);
            session()->flash('status', __('ecommerce::brands.brand_updated'));
        } else {
            $this->brand = Brand::create($fields);
            session()->flash('status', __('ecommerce::brands.brand_created'));
        }

        $this->redirect(route(config('ud-ecommerce.admin_route_prefix', 'admin').'.brands.index'), navigate: true);
    }

    public function render()
    {
        return view('ecommerce::livewire.admin.product.brand.brand-create-edit', [
            'isEditing' => $this->brand?->exists ?? false,
        ]);
    }
}
