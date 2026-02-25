<?php

namespace Unusualdope\LaravelEcommerce\Livewire\Admin\Size\SizeChart;

use Livewire\Component;
use Livewire\WithPagination;
use Unusualdope\LaravelEcommerce\Models\Size\SizeChart;

class SizeChartIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function delete(int $id): void
    {
        SizeChart::findOrFail($id)->delete();
        $this->dispatch('size-chart-deleted');
    }

    public function render()
    {
        return view('ecommerce::livewire.admin.size.size-chart.index', [
            'size_charts' => SizeChart::query()
                ->orderBy('id')
                ->paginate(15),
        ]);
    }
}
