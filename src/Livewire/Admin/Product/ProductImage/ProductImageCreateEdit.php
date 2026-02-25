<?php

namespace Unusualdope\LaravelEcommerce\Livewire\Admin\Product\ProductImage;

use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Unusualdope\LaravelEcommerce\Models\Product\Product;
use Unusualdope\LaravelEcommerce\Models\Product\ProductImage;
use Unusualdope\LaravelEcommerce\Models\Product\ProductImageLanguage;
use Unusualdope\LaravelEcommerce\Models\Stock\Variation;

class ProductImageCreateEdit extends Component
{
    use WithFileUploads;

    public ?ProductImage $productImage = null;

    public ?int $product_id = null;

    public ?int $variation_id = null;

    public $image = null;

    public ?string $existingImage = null;

    public int $position = 0;

    /** @var array<int, string> Caption per language_id */
    public array $caption = [];

    public $languageModel;

    public $selected_language;

    public function mount(?ProductImage $productImage = null): void
    {
        $this->productImage = $productImage;

        $languageModel = config('lmt.language_model', 'App\Models\Configuration\Language');
        $this->languageModel = $languageModel;
        if ($productImage?->exists) {
            $this->product_id = $productImage->product_id;
            $this->variation_id = $productImage->variation_id;
            $this->existingImage = $productImage->image;
            $this->position = (int) $productImage->position;
            $this->loadTranslatableData();
        } else {
            $this->initializeTranslatableData();
        }
        $this->selected_language = $this->languageModel::getDefaultLanguage();
    }

    protected function initializeTranslatableData(): void
    {
        $languages = $this->languageModel::getLanguagesForMultilangForm();
        foreach ($languages as $language) {
            $this->caption[$language['id']] = '';
        }
    }

    protected function loadTranslatableData(): void
    {
        $languages = $this->languageModel::getLanguagesForMultilangForm();
        foreach ($languages as $language) {
            $pil = ProductImageLanguage::where('product_image_id', $this->productImage->id)
                ->where('language_id', $language['id'])
                ->first();
            $this->caption[$language['id']] = $pil?->caption ?? '';
        }
    }

    public function deleteImage(): void
    {
        if ($this->existingImage && $this->productImage?->exists) {
            Storage::disk('public')->delete($this->existingImage);
            $this->productImage->not_filament = true;
            $this->productImage->update(['image' => null]);
            $this->existingImage = null;
        }
        $this->image = null;
    }

    public function save(): void
    {
        $rules = [
            'product_id' => ['required', 'exists:products,id'],
            'variation_id' => ['nullable', 'exists:variations,id'],
            'position' => ['required', 'integer', 'min:0'],
            'caption' => ['nullable', 'array'],
            'caption.*' => ['nullable', 'string', 'max:255'],
        ];
        if (! $this->productImage?->exists) {
            $rules['image'] = ['required', 'image', 'max:2048'];
        } else {
            $rules['image'] = ['nullable', 'image', 'max:2048'];
        }
        $this->validate($rules);

        $languages = $this->languageModel::getLanguagesForMultilangForm();
        $imagePath = $this->existingImage;

        if ($this->image) {
            if ($this->existingImage) {
                Storage::disk('public')->delete($this->existingImage);
            }
            $imagePath = $this->image->store('product-images/'.$this->product_id, 'public');
        }

        if ($this->productImage?->exists) {
            $this->productImage->not_filament = true;
            $this->productImage->update([
                'product_id' => $this->product_id,
                'variation_id' => $this->variation_id,
                'image' => $imagePath,
                'position' => $this->position,
            ]);
            $productImage = $this->productImage;
            session()->flash('status', __('ecommerce::product_images.image_updated'));
        } else {
            $productImage = new ProductImage;
            $productImage->not_filament = true;
            $productImage->product_id = $this->product_id;
            $productImage->variation_id = $this->variation_id;
            $productImage->image = $imagePath;
            $productImage->position = $this->position;
            $productImage->save();
            session()->flash('status', __('ecommerce::product_images.image_created'));
        }

        foreach ($languages as $language) {
            ProductImageLanguage::updateOrCreate(
                [
                    'product_image_id' => $productImage->id,
                    'language_id' => $language['id'],
                ],
                ['caption' => $this->caption[$language['id']] ?? '']
            );
        }

        $this->redirect(route(config('ud-ecommerce.admin_route_prefix', 'admin').'.productimages.index'), navigate: true);
    }

    public function getVariationsProperty()
    {
        if (! $this->product_id) {
            return collect();
        }

        return Variation::where('product_id', $this->product_id)->orderBy('id')->get();
    }

    public function render()
    {
        return view('ecommerce::livewire.admin.product.product-image.product-image-create-edit', [
            'products' => Product::with('languages')->orderBy('id')->get(),
            'variations' => $this->variations,
            'isEditing' => $this->productImage?->exists ?? false,
        ]);
    }
}
