<?php

namespace Unusualdope\LaravelEcommerce\Livewire\Admin\Warehouse\Stock;

use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use Unusualdope\LaravelEcommerce\Models\Product\Product;
use Unusualdope\LaravelEcommerce\Models\Stock\Variation;

class StockIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public bool $showLowStockFirst = false;

    public string $bulkQuantity = '0';

    public array $selectedProductIds = [];

    /** @var array<string, string> Row key (product_{id} or variation_{id}) => quantity */
    public array $rowQuantities = [];

    /** @var array<int, string> Selected row keys for bulk actions */
    public array $selectedRowKeys = [];

    public $languageModel;

    public $product_id = null;

    public $selected_language;

    public function mount(): void
    {
        $languageModel = config('lmt.language_model', 'App\Models\Configuration\Language');
        $this->languageModel = $languageModel;
        $this->selected_language = $this->languageModel::getDefaultLanguage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedProductId(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function getProductList()
    {
        return Product::query()
            ->with(['languages'])
            ->orderBy('id')
            ->get();
    }

    public function updatedShowLowStockFirst(): void
    {
        $this->resetPage();
    }

    public function toggleSelectPage(array $rowKeys): void
    {
        $current = $this->selectedRowKeys;
        sort($current);
        sort($rowKeys);
        if ($current === $rowKeys) {
            $this->selectedRowKeys = [];
        } else {
            $this->selectedRowKeys = $rowKeys;
        }
    }

    public function applyBulkQuantity(): void
    {
        $qty = (int) $this->bulkQuantity;
        if ($qty >= 0 && count($this->selectedRowKeys) > 0) {
            foreach ($this->selectedRowKeys as $rowKey) {
                $this->updateQuantityByRowKey($rowKey, $qty);
                // Sinkronkan input "modify quantity" di tiap baris dengan quantity baru
                $this->rowQuantities[$rowKey] = (string) $qty;
            }
            $this->bulkQuantity = '0';
            $this->selectedRowKeys = [];
            session()->flash('status', __('stocks.stocks_updated'));
        }
    }

    public function applyRowQuantityByKey(string $rowKey): void
    {
        $qty = (int) ($this->rowQuantities[$rowKey] ?? 0);
        if ($qty >= 0) {
            $this->updateQuantityByRowKey($rowKey, $qty);
            session()->flash('status', __('stocks.stocks_updated'));
        }
    }

    private function updateQuantityByRowKey(string $rowKey, int $qty): void
    {
        if (str_starts_with($rowKey, 'product_')) {
            $id = (int) substr($rowKey, 8);
            Product::where('id', $id)->update(['quantity' => $qty]);
        } elseif (str_starts_with($rowKey, 'variation_')) {
            $id = (int) substr($rowKey, 10);
            Variation::where('id', $id)->update(['quantity' => $qty]);
        }
    }

    public function render()
    {
        $products = Product::query()
            ->with(['variations' => fn ($q) => $q->orderBy('id'), 'variations.variants.variantGroup.languages', 'variations.variants.languages', 'languages', 'defaultImage'])
            ->when($this->search, function ($query) {
                $query->whereHas('languages', function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->when($this->selected_language, fn ($sq) => $sq->where('language_id', $this->selected_language));
                });
            })
            ->when($this->showLowStockFirst, function ($query) {
                $query->orderByRaw('COALESCE(products.quantity, 0) < COALESCE(products.minimal_quantity, 0) DESC');
            })
            ->orderBy('id')
            ->paginate(15);

        foreach ($products as $product) {
            $isVariable = strtolower((string) ($product->type ?? '')) === 'variable';
            $hasVariations = $product->relationLoaded('variations') && $product->variations->isNotEmpty();

            if ($isVariable && $hasVariations) {
                foreach ($product->variations as $variation) {
                    $key = 'variation_'.$variation->id;
                    if (! array_key_exists($key, $this->rowQuantities)) {
                        $this->rowQuantities[$key] = (string) ($variation->quantity ?? 0);
                    }
                }
            } else {
                $key = 'product_'.$product->id;
                if (! array_key_exists($key, $this->rowQuantities)) {
                    $this->rowQuantities[$key] = (string) $product->getQuantityForStockDisplay();
                }
            }
        }

        return view('ecommerce::livewire.admin.warehouse.stock.stock-index', [
            'products' => $products,
        ]);
    }
}
