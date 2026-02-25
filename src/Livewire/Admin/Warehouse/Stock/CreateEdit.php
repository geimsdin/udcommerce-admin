<?php

namespace Unusualdope\LaravelEcommerce\Livewire\Admin\Warehouse\Stock;

use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Unusualdope\LaravelEcommerce\Models\Product\Product;
use Unusualdope\LaravelEcommerce\Models\Stock\Stock;

class CreateEdit extends Component
{
    public ?Stock $stock = null;

    public $product_id = null;

    public int $quantity = 0;

    public int $minimal_quantity = 0;

    public int $low_stock_alert = 0;

    public float $price = 0;

    public bool $is_variant = false;

    public $available_from = null;

    public $available_to = null;

    public array $variant_id = [];

    public $sku = null;

    public $ean = null;

    public $mpn = null;

    public $upc = null;

    public $isbn = null;

    public $languageModel;

    public $selected_language;

    public $isEditing = false;

    public function mount(?Stock $stock = null): void
    {
        $languageModel = config('lmt.language_model', 'App\Models\Configuration\Language');
        $this->languageModel = $languageModel;
        $this->isEditing = $stock?->exists ?? false;
        if ($stock?->exists) {
            $this->stock = $stock;
            $this->product_id = $stock->product_id;
            $this->is_variant = $stock->product->type == 'variable';
            $this->quantity = $stock->quantity;
            $this->price = $stock->price;
            $this->sku = $stock->sku;
            $this->ean = $stock->ean;
            $this->mpn = $stock->mpn;
            $this->upc = $stock->upc;
            $this->isbn = $stock->isbn;
            $this->minimal_quantity = $stock->minimal_quantity;
            $this->low_stock_alert = $stock->low_stock_alert;
            $this->available_from = $stock->available_from;
            $this->available_to = $stock->available_to;
            $this->variant_id = $stock->variant->pluck('id', 'variant_group_id')->toArray();

        }
        $this->selected_language = $this->languageModel::getDefaultLanguage();
    }

    public function updatedProductId()
    {
        $product = Product::find($this->product_id);
        if ($product) {
            $this->is_variant = $product->type == 'variable';
            if ($this->is_variant) {
                if ($this->isEditing) {
                    $this->variant_id = $this->stock->variant->pluck('id', 'variant_group_id')->toArray();
                } else {
                    $this->variant_id = [];
                }
            }
        }
    }

    public function save(): void
    {
        $this->validate([
            'product_id' => ['required', 'int'],
            'quantity' => ['required', 'int'],
            'price' => ['required', 'numeric'],
        ]);

        $filtered = array_filter($this->variant_id, fn ($value) => ! is_null($value));
        $totalStockinProduct = Stock::getTotalByProductId($this->product_id, $this->stock->id ?? 0) + $this->quantity;

        $fields = [
            'product_id' => $this->product_id,
            'is_variant' => $this->is_variant,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'sku' => ($this->sku == '') ? null : $this->sku,
            'ean' => ($this->ean == '') ? null : $this->ean,
            'mpn' => ($this->mpn == '') ? null : $this->mpn,
            'upc' => ($this->upc == '') ? null : $this->upc,
            'isbn' => ($this->isbn == '') ? null : $this->isbn,
            'minimal_quantity' => $this->minimal_quantity,
            'low_stock_alert' => $this->low_stock_alert,
            'available_from' => ($this->available_from == '') ? null : $this->available_from,
            'available_to' => ($this->available_to == '') ? null : $this->available_to,
        ];
        if ($this->isEditing) {
            $this->stock->update($fields);
            session()->flash('status', __('ecommerce::stocks.stock_updated'));
        } else {
            $this->stock = Stock::create($fields);
            session()->flash('status', __('ecommerce::stocks.stock_created'));
        }
        if ($this->is_variant) {
            $this->stock->variant()->sync(array_values($filtered));
        }
        Product::where('id', $this->product_id)->update([
            'quantity' => $totalStockinProduct,
        ]);

        $this->redirect(route(config('ud-ecommerce.admin_route_prefix', 'admin').'.stocks.index'), navigate: true);

    }

    #[Computed]
    public function getCombinationVariantList()
    {
        $languageId = $this->selected_language;
        $results = DB::table('variant_groups as vg')
            ->leftJoin('variant_group_languages as vgl', function ($join) use ($languageId) {
                $join->on('vg.id', '=', 'vgl.variant_group_id')
                    ->where('vgl.language_id', '=', $languageId);
            })
            ->leftJoin('variants as v', 'vg.id', '=', 'v.variant_group_id')
            ->leftJoin('variant_languages as vl', function ($join) use ($languageId) {
                $join->on('v.id', '=', 'vl.variant_id')
                    ->where('vl.language_id', '=', $languageId);
            })
            ->select(
                'vg.id as group_id',
                'vg.type',
                'vg.position as group_position',
                'vgl.name as group_name',
                'vgl.tooltip',
                'v.id as variant_id',
                'v.position as variant_position',
                'vl.name as variant_name',
                'v.color as variant_color'
            )
            ->orderBy('vg.position')
            ->orderBy('v.position')
            ->get();
        $formatted = [];

        foreach ($results as $row) {
            $groupId = $row->group_id;

            if (! isset($formatted[$groupId])) {
                $formatted[$groupId] = [
                    'type' => $row->type,
                    'tooltip' => $row->tooltip,
                    'options' => [],
                ];
            }

            if ($row->variant_id) {
                $formatted[$groupId]['options'][$row->variant_id] = [
                    'name' => $row->variant_name,
                    'color' => $row->variant_color,
                ];
            }
        }

        return $formatted;
    }

    #[Computed]
    public function getProducts()
    {
        return Product::all();
    }

    public function render()
    {
        return view('ecommerce::livewire.admin.warehouse.stock.create-edit', [
            'isEditing' => $this->stock?->exists ?? false,
        ]);
    }
}
