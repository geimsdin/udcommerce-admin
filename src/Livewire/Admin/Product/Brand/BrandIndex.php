<?php

namespace Unusualdope\LaravelEcommerce\Livewire\Admin\Product\Brand;

use Livewire\Component;
use Livewire\WithPagination;
use Unusualdope\LaravelEcommerce\Models\Product\Brand;

class BrandIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public int $brand_id = 0;

    public bool $show_delete_modal = false;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function requestDelete(int $id): void
    {
        $this->brand_id = $id;
        $this->show_delete_modal = true;
    }

    public function delete(): void
    {
        Brand::findOrFail($this->brand_id)->delete();
        $this->show_delete_modal = false;
        $this->brand_id = 0;
        session()->flash('status', __('ecommerce::brands.brand_deleted'));
    }

    public function render()
    {
        return view('ecommerce::livewire.admin.product.brand.brand-index', [
            'brands' => Brand::query()
                ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
                ->orderBy('name')
                ->paginate(10),
        ]);
    }
}
