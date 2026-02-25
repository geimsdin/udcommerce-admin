<?php

namespace Unusualdope\LaravelEcommerce\Livewire\Admin\Product\ProductCategory;

use Livewire\Component;
use Livewire\WithPagination;
use Unusualdope\LaravelEcommerce\Models\Product\ProductCategory;

class ProductCategoryIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public int $product_category_id = 0;

    public bool $show_delete_modal = false;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function requestDelete(int $id): void
    {
        $this->product_category_id = $id;
        $this->show_delete_modal = true;
    }

    public function delete(): void
    {
        ProductCategory::findOrFail($this->product_category_id)->delete();
        $this->show_delete_modal = false;
        $this->product_category_id = 0;
        session()->flash('status', __('ecommerce::product-categories.product_category_deleted'));
    }

    public function render()
    {
        $productCategories = ProductCategory::query()
            ->with(['languages'])
            ->when($this->search, fn($q) => $q->whereHas('languages', function ($q) {
                $q->where('name', 'like', "%{$this->search}%");
            }))
            ->paginate(15);

        return view('ecommerce::livewire.admin.product.product-category.product-category-index', [
            'productCategories' => $productCategories,
        ]);
    }
}
