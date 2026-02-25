<?php

namespace Unusualdope\LaravelEcommerce\Livewire\Admin\Product\ProductCategory;

use Livewire\Attributes\Computed;
use Livewire\Component;
use Unusualdope\LaravelEcommerce\Models\Product\ProductCategory;
use Unusualdope\LaravelEcommerce\Models\Product\ProductCategoryLanguage;

class ProductCategoryCreateEdit extends Component
{
    public ?ProductCategory $productCategory = null;

    public bool $isEditing = false;

    public $languageModel;

    public string $selected_language;

    public array $name = [];

    public array $description = [];

    public int $parent_id = 0;

    public int $sort_order = 0;

    public bool $status = true;

    public function mount(?ProductCategory $productCategory = null): void
    {
        $languageModel = config('lmt.language_model');
        $this->languageModel = $languageModel;
        $this->productCategory = $productCategory;
        if ($productCategory?->exists) {
            $this->isEditing = true;
            $this->parent_id = $productCategory->parent_id == null ? 0 : $productCategory->parent_id;
            $this->sort_order = $productCategory->sort_order;
            $this->status = $productCategory->status;
            $this->loadTranslatableData();
        } else {
            $this->productCategory = new ProductCategory;
            $this->initializeTranslatableData();
        }
        $this->selected_language = $this->languageModel::getDefaultLanguage();
    }

    #[Computed]
    public function productCategories()
    {
        if ($this->isEditing) {
            return ProductCategory::where('id', '!=', $this->productCategory->id)->get();
        }

        return ProductCategory::all();
    }

    protected function initializeTranslatableData(): void
    {
        $languages = $this->languageModel::getLanguagesForMultilangForm();
        foreach ($languages as $language) {
            $this->name[$language['id']] = '';
            $this->description[$language['id']] = '';
        }
    }

    protected function loadTranslatableData(): void
    {
        $languages = $this->languageModel::getLanguagesForMultilangForm();
        foreach ($languages as $language) {
            $langData = $this->productCategory->getSpecificLanguage($language['id']);
            $this->name[$language['id']] = $langData?->name ?? '';
            $this->description[$language['id']] = $langData?->description ?? '';
        }
    }

    public function save(): void
    {
        $languages = $this->languageModel::getLanguagesForMultilangForm();
        if (!$this->productCategory->exists) {
            $this->productCategory = ProductCategory::create([
                'parent_id' => $this->parent_id == 0 ? null : $this->parent_id,
                'sort_order' => $this->sort_order,
                'status' => $this->status,
            ]);
        } else {
            $this->productCategory->update([
                'parent_id' => $this->parent_id == 0 ? null : $this->parent_id,
                'sort_order' => $this->sort_order,
                'status' => $this->status,
            ]);
        }
        foreach ($languages as $language) {
            ProductCategoryLanguage::updateOrCreate(
                [
                    'product_category_id' => $this->productCategory->id,
                    'language_id' => $language['id'],
                ],
                [
                    'name' => $this->name[$language['id']] ?? '',
                    'description' => $this->description[$language['id']] ?? '',
                ]
            );
        }
        if ($this->isEditing) {
            session()->flash('status', __('ecommerce::product-categories.product_category_updated'));
        } else {
            session()->flash('status', __('ecommerce::product-categories.product_category_created'));
        }
        $this->redirect(route(config('ud-ecommerce.admin_route_prefix', 'admin') . '.product-categories.index'), navigate: true);
    }

    public function render()
    {
        return view('ecommerce::livewire.admin.product.product-category.product-category-create-edit', [
            'isEditing' => $this->isEditing,
        ]);
    }
}
