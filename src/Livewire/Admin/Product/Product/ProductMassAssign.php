<?php

namespace Unusualdope\LaravelEcommerce\Livewire\Admin\Product\Product;

use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use Unusualdope\LaravelEcommerce\Models\Product\Brand;
use Unusualdope\LaravelEcommerce\Models\Product\Product;
use Unusualdope\LaravelEcommerce\Models\Product\ProductCategory;
use Unusualdope\LaravelEcommerce\Models\Product\SpecificPrice;

class ProductMassAssign extends Component
{
    use WithPagination;

    public $languageModel;

    public $selected_language;

    public array $mass_brand_ids = [];
    public array $mass_category_ids = [];
    public string $mass_name = '';
    public ?string $mass_date_start = null;
    public ?string $mass_date_end = null;
    public bool $mass_filter_active = false;
    public string $mass_action_subject = 'category';
    public string $mass_action = 'assign';
    public array $mass_target_category_ids = [];
    public int $mass_target_brand_id = 0;
    public bool $mass_show_confirm = false;

    // Specific Price form data
    public array $mass_specific_price = [
        'id_currency' => null,
        'id_client_type' => null,
        'id_customer' => null,
        'price' => 0,
        'from_quantity' => 1,
        'reduction' => 0,
        'reduction_tax' => false,
        'reduction_type' => 'amount',
        'from' => null,
        'to' => null,
    ];
    public bool $applyToAllCustomers = true;
    public bool $unlimitedDuration = true;
    public bool $applyDiscount = false;
    public bool $setSpecificPrice = false;

    public ?string $mass_success = null;
    public ?string $mass_error   = null;

    public function mount(): void
    {
        $languageModel = config('lmt.language_model', 'App\\Models\\Configuration\\Language');
        $this->languageModel    = $languageModel;
        $this->selected_language = $this->languageModel::getDefaultLanguage();
    }

    private function buildMassQuery()
    {
        $language = $this->selected_language;
        $name     = $this->mass_name;

        return Product::query()
            ->with(['languages', 'categories', 'brand'])
            ->when(
                !empty($this->mass_brand_ids),
                fn($q) => $q->whereIn('brand_id', array_filter(array_map('intval', $this->mass_brand_ids)))
            )
            ->when(
                !empty($this->mass_category_ids),
                fn($q) => $q->whereHas('categories', function ($sq) {
                    $sq->whereIn('product_categories.id', array_filter(array_map('intval', $this->mass_category_ids)));
                })
            )
            ->when(
                $name !== '',
                fn($q) => $q->whereHas('languages', function ($sq) use ($name, $language) {
                    $sq->where('name', 'like', "%{$name}%")
                        ->when($language, fn($sq2) => $sq2->where('language_id', $language));
                })
            )
            ->when(
                ! empty($this->mass_date_start),
                fn($q) => $q->whereDate('created_at', '>=', $this->mass_date_start)
            )
            ->when(
                ! empty($this->mass_date_end),
                fn($q) => $q->whereDate('created_at', '<=', $this->mass_date_end)
            );
    }

    public function runMassFilter(): void
    {
        $this->mass_success      = null;
        $this->mass_error        = null;
        $this->mass_filter_active = true;
        $this->resetPage('massPage');
    }

    public function confirmMassAction(): void
    {
        $this->mass_success = null;
        $this->mass_error   = null;

        if (! $this->mass_filter_active) {
            $this->mass_error = __('ecommerce::products.mass.error_no_products');
            return;
        }

        if ($this->mass_action_subject === 'category' && empty($this->mass_target_category_ids)) {
            $this->mass_error = __('ecommerce::products.mass.error_no_categories');
            return;
        }

        if (
            $this->mass_action_subject === 'brand'
            && $this->mass_action === 'assign'
            && $this->mass_target_brand_id === 0
        ) {
            $this->mass_error = __('ecommerce::products.mass.error_no_brand');
            return;
        }

        if ($this->mass_action_subject === 'specific_price') {
            if ($this->mass_action === 'assign') {
                if (!$this->applyDiscount && !$this->setSpecificPrice) {
                    $this->mass_error = __('ecommerce::products.form.apply_discount_or_specific_price');
                    return;
                }
            }
        }

        $this->mass_show_confirm = true;
    }

    public function executeMassAction(): void
    {
        $this->mass_show_confirm = false;
        $this->mass_success      = null;
        $this->mass_error        = null;

        $count = $this->buildMassQuery()->count();

        if ($count === 0) {
            $this->mass_error = __('ecommerce::products.mass.error_no_products');
            return;
        }

        if ($this->mass_action_subject === 'category') {
            $categoryIds = array_map('intval', $this->mass_target_category_ids);

            $this->buildMassQuery()
                ->orderBy('id')
                ->chunk(100, function ($products) use ($categoryIds) {
                    foreach ($products as $product) {
                        if ($this->mass_action === 'assign') {
                            $product->categories()->syncWithoutDetaching($categoryIds);
                        } else {
                            $product->categories()->detach($categoryIds);
                        }
                    }
                });

            $this->mass_success = $this->mass_action === 'assign'
                ? __('ecommerce::products.mass.success_cat_assigned', ['count' => $count])
                : __('ecommerce::products.mass.success_cat_removed', ['count' => $count]);
        } elseif ($this->mass_action_subject === 'brand') {
            if ($this->mass_action === 'assign') {
                $this->buildMassQuery()
                    ->orderBy('id')
                    ->chunk(100, function ($products) {
                        foreach ($products as $product) {
                            $product->update(['brand_id' => $this->mass_target_brand_id]);
                        }
                    });
                $this->mass_success = __('ecommerce::products.mass.success_brand_assigned', ['count' => $count]);
            } else {
                $this->buildMassQuery()
                    ->orderBy('id')
                    ->chunk(100, function ($products) {
                        foreach ($products as $product) {
                            $product->update(['brand_id' => null]);
                        }
                    });
                $this->mass_success = __('ecommerce::products.mass.success_brand_removed', ['count' => $count]);
            }
        } elseif ($this->mass_action_subject === 'specific_price') {
            if ($this->mass_action === 'assign') {
                // Prepare specific price data
                $specificPriceData = [
                    'id_currency' => $this->mass_specific_price['id_currency'] ?? 0,
                    'id_client_type' => $this->mass_specific_price['id_client_type'] ?? 0,
                    'id_customer' => $this->applyToAllCustomers ? 0 : ($this->mass_specific_price['id_customer'] ?? 0),
                    'price' => $this->mass_specific_price['price'] ?? 0,
                    'from_quantity' => $this->mass_specific_price['from_quantity'] ?? 1,
                    'reduction' => $this->mass_specific_price['reduction'] ?? 0,
                    'reduction_tax' => $this->mass_specific_price['reduction_tax'] ?? false,
                    'reduction_type' => $this->mass_specific_price['reduction_type'] ?? 'amount',
                    'from' => $this->unlimitedDuration ? null : ($this->mass_specific_price['from'] ?? null),
                    'to' => $this->unlimitedDuration ? null : ($this->mass_specific_price['to'] ?? null),
                ];

                $this->buildMassQuery()
                    ->orderBy('id')
                    ->chunk(100, function ($products) use ($specificPriceData) {
                        foreach ($products as $product) {
                            SpecificPrice::create([
                                'id_product' => $product->id,
                                'id_currency' => $specificPriceData['id_currency'],
                                'id_client_type' => $specificPriceData['id_client_type'],
                                'id_customer' => $specificPriceData['id_customer'],
                                'price' => $specificPriceData['price'],
                                'from_quantity' => $specificPriceData['from_quantity'],
                                'reduction' => $specificPriceData['reduction'],
                                'reduction_tax' => $specificPriceData['reduction_tax'],
                                'reduction_type' => $specificPriceData['reduction_type'],
                                'from' => $specificPriceData['from'],
                                'to' => $specificPriceData['to'],
                            ]);
                        }
                    });

                $this->mass_success = __('ecommerce::products.mass.success_specific_price_assigned', ['count' => $count]);
            } else {
                $productIds = $this->buildMassQuery()->pluck('id')->toArray();
                SpecificPrice::whereIn('id_product', $productIds)->delete();
                $this->mass_success = __('ecommerce::products.mass.success_specific_price_removed', ['count' => $count]);
            }
        }

        $this->resetPage('massPage');
    }

    public function updatedApplyToAllCustomers(): void
    {
        if ($this->applyToAllCustomers) {
            $this->mass_specific_price['id_customer'] = null;
        }
    }

    public function updatedUnlimitedDuration(): void
    {
        if ($this->unlimitedDuration) {
            $this->mass_specific_price['from'] = null;
            $this->mass_specific_price['to'] = null;
        }
    }

    public function updatedApplyDiscount(): void
    {
        if (!$this->applyDiscount) {
            $this->mass_specific_price['reduction'] = 0;
        }
    }

    public function updatedSetSpecificPrice(): void
    {
        if (!$this->setSpecificPrice) {
            $this->mass_specific_price['price'] = 0;
        }
    }

    public function updatedMassAction(): void
    {
        // Reset specific price form when switching actions
        if ($this->mass_action_subject === 'specific_price') {
            $this->mass_specific_price = [
                'id_currency' => null,
                'id_client_type' => null,
                'id_customer' => null,
                'price' => 0,
                'from_quantity' => 1,
                'reduction' => 0,
                'reduction_tax' => false,
                'reduction_type' => 'amount',
                'from' => null,
                'to' => null,
            ];
            $this->applyToAllCustomers = true;
            $this->unlimitedDuration = true;
            $this->applyDiscount = false;
            $this->setSpecificPrice = false;
        }
    }

    public function updatedMassActionSubject(): void
    {
        // Reset specific price form when switching subjects
        $this->mass_specific_price = [
            'id_currency' => null,
            'id_client_type' => null,
            'id_customer' => null,
            'price' => 0,
            'from_quantity' => 1,
            'reduction' => 0,
            'reduction_tax' => false,
            'reduction_type' => 'amount',
            'from' => null,
            'to' => null,
        ];
        $this->applyToAllCustomers = true;
        $this->unlimitedDuration = true;
        $this->applyDiscount = false;
        $this->setSpecificPrice = false;
    }

    #[Computed]
    public function getCurrencies()
    {
        return \Unusualdope\LaravelEcommerce\Models\Administration\Currency::all();
    }

    #[Computed]
    public function getClientGroups()
    {
        return \Unusualdope\LaravelEcommerce\Models\Customer\ClientGroup::all();
    }

    #[Computed]
    public function getClients()
    {
        return \Unusualdope\LaravelEcommerce\Models\Customer\Client::with('user')->get();
    }

    public function render()
    {
        $massProducts = $this->mass_filter_active
            ? $this->buildMassQuery()->orderBy('id')->paginate(15, ['*'], 'massPage')
            : null;

        return view('ecommerce::livewire.admin.product.product.product-mass-assign', [
            'mass_products'   => $massProducts,
            'all_brands'      => Brand::orderBy('name')->get(),
            'all_categories'  => ProductCategory::with('languages')->orderBy('id')->get(),
        ]);
    }
}
