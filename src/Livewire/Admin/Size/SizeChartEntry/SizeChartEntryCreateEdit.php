<?php

namespace Unusualdope\LaravelEcommerce\Livewire\Admin\Size\SizeChartEntry;

use Flux\Flux;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Unusualdope\LaravelEcommerce\Models\Size\SizeChart;
use Unusualdope\LaravelEcommerce\Models\Size\SizeChartEntry;
use Unusualdope\LaravelEcommerce\Models\Size\TargetUnit;
use Unusualdope\LaravelEcommerce\Models\Stock\Variant;

class SizeChartEntryCreateEdit extends Component
{
    public ?SizeChartEntry $size_chart_entry = null;

    public string $converted_value = '';

    public int $size_chart_id;

    public int $target_unit_id;

    public int $variant_id;

    public $languageModel;

    public $selected_language;

    protected string $routePrefix;

    public function mount(?SizeChartEntry $size_chart_entry = null): void
    {
        $this->routePrefix = config('ud-ecommerce.admin_route_prefix', 'admin');

        $languageModel = config('lmt.language_model', 'App\Models\Configuration\Language');
        $this->languageModel = $languageModel;
        $this->selected_language = $this->languageModel::getDefaultLanguage();

        if ($size_chart_entry?->exists) {
            $this->size_chart_entry = $size_chart_entry;
            $this->converted_value = $size_chart_entry->converted_value;
            $this->size_chart_id = $size_chart_entry->size_chart_id;
            $this->target_unit_id = $size_chart_entry->target_unit_id;
            $this->variant_id = $size_chart_entry->variant_id;
        }
    }

    #[Computed]
    public function getSizeChart()
    {
        return SizeChart::all();
    }

    #[Computed]
    public function getTargetUnit()
    {
        return TargetUnit::all();
    }

    #[Computed]
    public function getVariant()
    {
        return Variant::all();
    }

    public function save(): void
    {
        $validated = $this->validate([
            'converted_value' => ['required', 'string', 'max:255'],
            'size_chart_id' => ['required', 'numeric'],
            'target_unit_id' => ['required', 'numeric'],
            'variant_id' => ['numeric'],
        ]);

        DB::transaction(function () {
            if ($this->size_chart_entry?->exists) {
                $this->size_chart_entry->update([
                    'converted_value' => $this->converted_value,
                    'size_chart_id' => $this->size_chart_id,
                    'target_unit_id' => $this->target_unit_id,
                    'variant_id' => $this->variant_id,
                ]);
            } else {
                $this->size_chart_entry = SizeChartEntry::create([
                    'converted_value' => $this->converted_value,
                    'size_chart_id' => $this->size_chart_id,
                    'target_unit_id' => $this->target_unit_id,
                    'variant_id' => $this->variant_id,
                ]);
            }
        });

        $isEditing = $this->size_chart_entry->wasRecentlyCreated === false && $this->size_chart_entry->exists;

        Flux::toast(
            variant: 'success',
            heading: $isEditing ? __('general.updated') : __('general.created'),
            text: $isEditing ? __('ecommerce::size-chart-entries.size_chart_entry_updated') : __('ecommerce::size-chart-entries.size_chart_entry_created'),
        );

        $this->redirect(route($this->routePrefix.'.size-chart-entries.index'));
    }

    public function render()
    {
        return view('ecommerce::livewire.admin.size.size-chart-entry.create-edit', [
            'isEditing' => $this->size_chart?->exists ?? false,
        ]);
    }
}
