<?php

namespace Unusualdope\LaravelEcommerce\Livewire\Admin\Size\TargetUnit;

use Livewire\Component;
use Livewire\WithPagination;
use Unusualdope\LaravelEcommerce\Models\Size\TargetUnit;

class TargetUnitIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function delete(int $id): void
    {
        TargetUnit::findOrFail($id)->delete();
        $this->dispatch('target-unit-deleted');
    }

    public function render()
    {
        return view('ecommerce::livewire.admin.size.target-unit.index', [
            'target_units' => TargetUnit::query()
                ->orderBy('id')
                ->paginate(15),
        ]);
    }
}
