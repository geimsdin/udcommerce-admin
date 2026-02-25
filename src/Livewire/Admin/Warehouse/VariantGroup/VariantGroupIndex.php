<?php

namespace Unusualdope\LaravelEcommerce\Livewire\Admin\Warehouse\VariantGroup;

use App\Livewire\Traits\HandlingSortingItems;
use Livewire\Component;
use Livewire\WithPagination;
use Unusualdope\LaravelEcommerce\Models\Stock\VariantGroup;

class VariantGroupIndex extends Component
{
    use HandlingSortingItems, WithPagination;

    public string $search = '';

    public $languageModel;

    public $selected_language;

    public string $order_by = 'position';

    public bool $show_delete_modal = false;

    public int $variant_group_id = 0;

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

    protected function getSortableModelClass(): string
    {
        return VariantGroup::class;
    }

    protected function getSortableSuccessMessage(): ?string
    {
        return __('ecommerce::variantgroups.variantgroups_reordered');
    }

    public function requestDelete(int $id): void
    {
        $this->variant_group_id = $id;
        $this->show_delete_modal = true;
    }

    public function delete(): void
    {
        VariantGroup::findOrFail($this->variant_group_id)->delete();
        $this->show_delete_modal = false;
        $this->variant_group_id = 0;
        session()->flash('status', __('ecommerce::variantgroups.variantgroups_deleted'));
    }

    public function render()
    {
        return view('ecommerce::livewire.admin.warehouse.variant-group.index', [
            'variantgroups' => VariantGroup::query()
                ->with(['languages'])
                ->when($this->search, function ($query) {
                    $query->whereHas('languages', function ($q) {
                        $q->where('name', 'like', "%{$this->search}%")
                            ->when($this->selected_language, fn ($sq) => $sq->where('language_id', $this->selected_language));
                    });
                })
                ->orderBy('position')
                ->paginate(15),
        ]);
    }
}
