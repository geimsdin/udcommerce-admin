<?php

namespace Unusualdope\LaravelEcommerce\Livewire\Admin\Product\ProductImage;

use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;
use Unusualdope\LaravelEcommerce\Models\Product\ProductImage;
use Unusualdope\LaravelEcommerce\Models\Product\ProductImageLanguage;

class ProductImageIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public int $product_image_id = 0;

    public bool $show_delete_modal = false;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function requestDelete(int $id): void
    {
        $this->product_image_id = $id;
        $this->show_delete_modal = true;
    }

    public function delete(): void
    {
        $productImage = ProductImage::findOrFail($this->product_image_id);
        if ($productImage->image) {
            Storage::disk('public')->delete($productImage->image);
        }
        ProductImageLanguage::where('product_image_id', $productImage->id)->delete();
        $productImage->delete();
        $this->show_delete_modal = false;
        $this->product_image_id = 0;
        session()->flash('status', __('ecommerce::product_images.image_deleted'));
    }

    public function render()
    {
        return view('ecommerce::livewire.admin.product.product-image.product-image-index', [
            'productImages' => ProductImage::query()
                ->with(['product.currentLanguage', 'productImageLanguages'])
                ->when($this->search, function ($q) {
                    $q->whereHas('product', function ($sub) {
                        $sub->whereHas('languages', function ($q2) {
                            $q2->where('name', 'like', '%'.$this->search.'%');
                        });
                    });
                })
                ->orderBy('product_id')
                ->orderBy('position')
                ->paginate(10),
        ]);
    }
}
