<?php

namespace Unusualdope\LaravelEcommerce\Livewire\Admin\Size\SizeChartEntry;

use Livewire\Component;
use Livewire\WithPagination;
use Unusualdope\LaravelEcommerce\Models\Size\SizeChartEntry;

class SizeChartEntryIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public $languageModel;

    public $selected_language;

    public function mount(): void
    {
        $languageModel = config('lmt.language_model', 'App\Models\Configuration\Language');
        $this->languageModel = $languageModel;
        $this->selected_language = $this->languageModel::getDefaultLanguage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function delete(int $id): void
    {
        SizeChartEntry::findOrFail($id)->delete();
        $this->dispatch('size-chart-entry-deleted');
    }

    public function render()
    {
        return view('ecommerce::livewire.admin.size.size-chart-entry.index', [
            'size_chart_entries' => SizeChartEntry::query()
                ->orderBy('id')
                ->paginate(15),
        ]);
    }
}
