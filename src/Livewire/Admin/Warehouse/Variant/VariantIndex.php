<?php

namespace Unusualdope\LaravelEcommerce\Livewire\Admin\Warehouse\Variant;

use App\Livewire\Traits\HandlingSortingItems;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use Unusualdope\LaravelEcommerce\Models\Stock\Variant;
use Unusualdope\LaravelEcommerce\Models\Stock\VariantGroup;

class VariantIndex extends Component
{
    use HandlingSortingItems, WithPagination;

    public string $search = '';

    public $languageModel;

    public $selected_language;

    public bool $show_delete_modal = false;

    public int $variant_id = 0;

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
        return Variant::class;
    }

    protected function getSortableSuccessMessage(): ?string
    {
        return __('ecommerce::variants.variants_reordered');
    }

    public function requestDelete(int $id): void
    {
        $this->variant_id = $id;
        $this->show_delete_modal = true;
    }

    public function delete(): void
    {
        Variant::findOrFail($this->variant_id)->delete();
        $this->show_delete_modal = false;
        $this->variant_id = 0;
        session()->flash('status', __('ecommerce::variants.variants_deleted'));
    }

    #[Computed]
    public function getVariantGroupList()
    {
        return VariantGroup::query()
            ->with(['languages'])
            ->orderBy('position')
            ->get();
    }

    public function render()
    {
        return view('ecommerce::livewire.admin.warehouse.variant.variant-index', [
            'variants' => Variant::query()
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
