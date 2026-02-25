<?php

namespace Unusualdope\LaravelEcommerce\Livewire\Admin\Product\Product;

use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use Unusualdope\LaravelEcommerce\Models\Product\Brand;
use Unusualdope\LaravelEcommerce\Models\Product\Product;
use Unusualdope\LaravelEcommerce\Models\Product\ProductCategory;

class ProductIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public int $product_id = 0;

    public bool $show_delete_modal = false;

    public $languageModel;

    public $selected_language;

    public ?int $filter_brand_id = null;
    public ?int $filter_category_id = null;
    public ?string $filter_type = null;

    public bool $show_filters = false;

    public function mount(): void
    {
        $languageModel = config('lmt.language_model', 'App\\Models\\Configuration\\Language');
        $this->languageModel    = $languageModel;
        $this->selected_language = $this->languageModel::getDefaultLanguage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function getProductTypes(): array
    {
        return [
            'simple' => __('ecommerce::products.form.product_type_simple'),
            'virtual' => __('ecommerce::products.form.product_type_virtual'),
            'variable' => __('ecommerce::products.form.product_type_variable'),
        ];
    }

    public function applyFilters(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->filter_brand_id    = null;
        $this->filter_category_id = null;
        $this->filter_type        = null;
        $this->resetPage();
    }

    public function requestDelete(int $id): void
    {
        $this->product_id        = $id;
        $this->show_delete_modal = true;
    }

    public function delete(): void
    {
        Product::findOrFail($this->product_id)->delete();
        $this->show_delete_modal = false;
        $this->product_id        = 0;
        session()->flash('status', __('ecommerce::products.product_deleted'));
    }

    public function render()
    {
        return view('ecommerce::livewire.admin.product.product.product-index', [
            'products' => Product::query()
                ->with(['languages'])
                ->when($this->search, function ($query) {
                    $query->whereHas('languages', function ($q) {
                        $q->where('name', 'like', "%{$this->search}%")
                            ->when($this->selected_language, fn($sq) => $sq->where('language_id', $this->selected_language));
                    });
                })
                ->when($this->filter_brand_id, fn($q) => $q->where('brand_id', $this->filter_brand_id))
                ->when(
                    $this->filter_category_id,
                    fn($q) => $q->whereHas('categories', fn($sq) => $sq->where('product_categories.id', $this->filter_category_id))
                )
                ->when($this->filter_type, fn($q) => $q->where('type', $this->filter_type))
                ->orderBy('id')
                ->paginate(15),

            'all_brands'     => Brand::orderBy('name')->get(),
            'all_categories' => ProductCategory::with('languages')->orderBy('id')->get(),
        ]);
    }
}
