<?php

namespace Unusualdope\LaravelEcommerce\Livewire\Admin\Coupon;

use Carbon\Carbon;
use Livewire\Component;
use Unusualdope\LaravelEcommerce\Models\Coupon;
use Unusualdope\LaravelEcommerce\Models\Product\Brand;
use Unusualdope\LaravelEcommerce\Models\Product\Product;
use Unusualdope\LaravelEcommerce\Models\Product\ProductCategory;

class CouponCreateEdit extends Component
{
    public ?Coupon $coupon = null;

    public bool $isEditing = false;

    public string $code = '';

    public string $description = '';

    public string $type = 'fixed';

    public float $value = 0;

    public string $start_date = '';

    public string $end_date = '';

    public ?float $minimum_spend = null;

    public ?float $maximum_spend = null;

    public ?int $limit_use_by_user = null;

    public ?int $limit_use_by_coupon = null;

    public bool $active = true;

    public array $include_brand_ids = [];

    public array $exclude_brand_ids = [];

    public array $include_category_ids = [];

    public array $exclude_category_ids = [];

    public array $include_product_ids = [];

    public array $exclude_product_ids = [];

    public function mount(?Coupon $coupon = null): void
    {
        $this->coupon = $coupon;

        if ($coupon?->exists) {
            $this->isEditing = true;
            $this->code = $coupon->code;
            $this->start_date = $coupon->start_date ? $coupon->start_date->format('Y-m-d\TH:i') : '';
            $this->end_date = $coupon->end_date ? $coupon->end_date->format('Y-m-d\TH:i') : '';
            $this->minimum_spend = $coupon->minimum_spend;
            $this->maximum_spend = $coupon->maximum_spend;
            $this->limit_use_by_user = $coupon->limit_use_by_user;
            $this->limit_use_by_coupon = $coupon->limit_use_by_coupon;
            $this->active = $coupon->active;
            $this->description = $coupon->description;
            $this->type = $coupon->type;
            $this->value = $coupon->value;
            $this->include_brand_ids = $coupon->include_brands ?? [];
            $this->exclude_brand_ids = $coupon->exclude_brands ?? [];
            $this->include_category_ids = $coupon->include_categories ?? [];
            $this->exclude_category_ids = $coupon->exclude_categories ?? [];
            $this->include_product_ids = $coupon->include_products ?? [];
            $this->exclude_product_ids = $coupon->exclude_products ?? [];
        } else {
            $this->coupon = new Coupon;
        }
    }

    public function save(): void
    {
        $this->validate([
            'code' => ['string', 'max:50', 'unique:coupons,code,'.($this->coupon->id ?? '')],
            'description' => ['nullable', 'string', 'max:255'],
            'type' => ['required', 'in:fixed,percentage'],
            'value' => ['required', 'numeric', 'min:0'],
            'minimum_spend' => ['nullable', 'numeric', 'min:0'],
            'maximum_spend' => ['nullable', 'numeric', 'min:0', 'gt:minimum_spend'],
            'limit_use_by_user' => ['nullable', 'integer', 'min:1'],
            'limit_use_by_coupon' => ['nullable', 'integer', 'min:1'],
            'active' => ['boolean'],
            'include_brand_ids' => ['array'],
            'exclude_brand_ids' => ['array'],
            'include_category_ids' => ['array'],
            'exclude_category_ids' => ['array'],
            'include_product_ids' => ['array'],
            'exclude_product_ids' => ['array'],
        ]);
        $data = [
            'code' => $this->code,
            'description' => $this->description,
            'type' => $this->type,
            'value' => $this->value,
            'start_date' => $this->start_date ? Carbon::parse($this->start_date) : null,
            'end_date' => $this->end_date ? Carbon::parse($this->end_date) : null,
            'minimum_spend' => $this->minimum_spend,
            'maximum_spend' => $this->maximum_spend,
            'limit_use_by_user' => $this->limit_use_by_user,
            'limit_use_by_coupon' => $this->limit_use_by_coupon,
            'include_brands' => $this->include_brand_ids,
            'exclude_brands' => $this->exclude_brand_ids,
            'include_categories' => $this->include_category_ids,
            'exclude_categories' => $this->exclude_category_ids,
            'include_products' => $this->include_product_ids,
            'exclude_products' => $this->exclude_product_ids,
            'active' => $this->active,
        ];

        if (! $this->coupon->exists) {
            $this->coupon = Coupon::create($data);
            session()->flash('status', __('ecommerce::coupons.coupon_created'));
        } else {
            $this->coupon->update($data);
            session()->flash('status', __('ecommerce::coupons.coupon_updated'));
        }

        $this->redirect(route(config('ud-ecommerce.admin_route_prefix', 'admin').'.coupons.index'), navigate: true);
    }

    public function render()
    {
        return view('ecommerce::livewire.admin.coupon.create-edit', [
            'isEditing' => $this->isEditing,
            'brands' => Brand::orderBy('name')->get(),
            'categories' => ProductCategory::with('currentLanguage')->orderBy('id')->get(),
            'products' => Product::with('currentLanguage')->orderBy('id')->get(),
        ]);
    }
}
