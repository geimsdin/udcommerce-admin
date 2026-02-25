<?php

namespace Unusualdope\LaravelEcommerce\Livewire\Admin\Product\Season;

use Livewire\Component;
use Livewire\WithPagination;
use Unusualdope\LaravelEcommerce\Models\Product\Season;

class SeasonIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public int $season_id = 0;

    public bool $show_delete_modal = false;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function requestDelete(int $id): void
    {
        $this->season_id = $id;
        $this->show_delete_modal = true;
    }

    public function delete(): void
    {
        Season::findOrFail($this->season_id)->delete();
        $this->show_delete_modal = false;
        $this->season_id = 0;
        session()->flash('status', __('ecommerce::seasons.season_deleted'));
    }

    public function render()
    {
        return view('ecommerce::livewire.admin.product.season.season-index', [
            'seasons' => Season::query()
                ->with(['languages'])
                ->when($this->search, fn ($q) => $q->whereHas('languages', function ($q) {
                    $q->where('name', 'like', "%{$this->search}%");
                }))
                ->paginate(15),
        ]);
    }
}
