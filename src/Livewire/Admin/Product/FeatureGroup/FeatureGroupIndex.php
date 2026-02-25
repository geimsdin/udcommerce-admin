<?php

namespace Unusualdope\LaravelEcommerce\Livewire\Admin\Product\FeatureGroup;

use Livewire\Component;
use Livewire\WithPagination;
use Unusualdope\LaravelEcommerce\Models\Product\FeatureGroup;

class FeatureGroupIndex extends Component
{
    use WithPagination;

    public $search = '';

    public $show_delete_modal = false;

    public $feature_group_id = 0;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function requestDelete(int $id): void
    {
        $this->feature_group_id = $id;
        $this->show_delete_modal = true;
    }

    public function delete(): void
    {
        FeatureGroup::findOrFail($this->feature_group_id)->delete();
        $this->show_delete_modal = false;
        $this->feature_group_id = 0;
        session()->flash('status', __('ecommerce::feature-groups.feature_group_deleted'));
    }

    public function render()
    {
        return view('ecommerce::livewire.admin.product.feature-group.feature-group-index', [
            'featureGroups' => FeatureGroup::query()
                ->with(['languages'])
                ->when($this->search, fn ($q) => $q->whereHas('languages', function ($q) {
                    $q->where('name', 'like', "%{$this->search}%");
                }))
                ->paginate(15),
        ]);
    }
}
