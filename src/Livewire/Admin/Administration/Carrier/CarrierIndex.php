<?php

namespace Unusualdope\LaravelEcommerce\Livewire\Admin\Administration\Carrier;

use Livewire\Component;
use Livewire\WithPagination;
use Unusualdope\LaravelEcommerce\Models\Administration\Carrier;

class CarrierIndex extends Component
{
    use WithPagination;

    public $search = '';

    public $carrier_id = 0;

    public $show_delete_modal = false;

    public function requestDelete(int $id): void
    {
        $this->carrier_id = $id;
        $this->show_delete_modal = true;
    }

    public function delete(): void
    {
        Carrier::findOrFail($this->carrier_id)->delete();
        $this->show_delete_modal = false;
        $this->carrier_id = 0;
        session()->flash('status', __('ecommerce::carriers.carrier_deleted'));
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('ecommerce::livewire.admin.administration.carrier.carrier-index', [
            'carriers' => Carrier::query()
                ->with('languages')
                ->when($this->search, function ($query) {
                    $query->whereHas('languages', function ($q) {
                        $q->where('name', 'like', "%{$this->search}%");
                    });
                })
                ->paginate(15),
        ]);
    }
}
