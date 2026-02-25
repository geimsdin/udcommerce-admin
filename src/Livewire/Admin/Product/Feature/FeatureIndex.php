<?php

namespace Unusualdope\LaravelEcommerce\Livewire\Admin\Product\Feature;

use Livewire\Component;
use Livewire\WithPagination;
use Unusualdope\LaravelEcommerce\Models\Product\Feature;

class FeatureIndex extends Component
{
    use WithPagination;

    public $search = '';

    public $show_delete_modal = false;

    public $feature_id = 0;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function requestDelete(int $id): void
    {
        $this->feature_id = $id;
        $this->show_delete_modal = true;
    }

    public function delete(): void
    {
        Feature::findOrFail($this->feature_id)->delete();
        $this->show_delete_modal = false;
        $this->feature_id = 0;
        session()->flash('status', __('ecommerce::features.feature_deleted'));
    }

    public function render()
    {
        return view('ecommerce::livewire.admin.product.feature.feature-index', [
            'features' => Feature::query()
                ->with(['languages'])
                ->when($this->search, fn ($q) => $q->whereHas('languages', function ($q) {
                    $q->where('name', 'like', "%{$this->search}%");
                }))
                ->paginate(15),
        ]);
    }
}
