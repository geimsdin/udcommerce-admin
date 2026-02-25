<?php

namespace Unusualdope\LaravelEcommerce\Livewire\Admin\Size\SizeChart;

use Flux\Flux;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Unusualdope\LaravelEcommerce\Models\Product\Brand;
use Unusualdope\LaravelEcommerce\Models\Product\ProductCategory;
use Unusualdope\LaravelEcommerce\Models\Size\SizeChart;

class SizeChartCreateEdit extends Component
{
    public ?SizeChart $size_chart = null;

    protected string $routePrefix;

    #[Validate('string')]
    public string $name = '';

    #[Validate('int')]
    public int $brand_id;

    #[Validate('int')]
    public int $category_id;

    public function mount(?SizeChart $size_chart = null): void
    {
        $this->routePrefix = config('ud-ecommerce.admin_route_prefix', 'admin');

        if ($size_chart?->exists) {
            $this->size_chart = $size_chart;
            $this->name = $size_chart->name;
            $this->brand_id = $size_chart->brand_id;
            $this->category_id = $size_chart->category_id;
        }
    }

    #[Computed]
    public function getBrand()
    {
        return Brand::all();
    }

    #[Computed]
    public function getCategory()
    {
        return ProductCategory::all();
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'brand_id' => ['required', 'integer'],
            'category_id' => ['required', 'integer'],
        ]);

        DB::transaction(function () {
            if ($this->size_chart?->exists) {
                $this->size_chart->update([
                    'name' => $this->name,
                    'brand_id' => $this->brand_id,
                    'category_id' => $this->category_id,
                ]);
            } else {
                $this->size_chart = SizeChart::create([
                    'name' => $this->name,
                    'brand_id' => $this->brand_id,
                    'category_id' => $this->category_id,
                ]);
            }
        });

        $isEditing = $this->size_chart->wasRecentlyCreated === false && $this->size_chart->exists;

        Flux::toast(
            variant: 'success',
            heading: $isEditing ? __('general.updated') : __('general.created'),
            text: $isEditing ? __('ecommerce::size-charts.size_chart_updated') : __('ecommerce::size-charts.size_chart_created'),
        );

        $this->redirect(route($this->routePrefix.'.size-charts.index'));
    }

    public function render()
    {
        return view('ecommerce::livewire.admin.size.size-chart.create-edit', [
            'isEditing' => $this->size_chart?->exists ?? false,
        ]);
    }
}
