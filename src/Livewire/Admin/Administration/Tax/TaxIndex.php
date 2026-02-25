<?php

namespace Unusualdope\LaravelEcommerce\Livewire\Admin\Administration\Tax;

use Livewire\Component;
use Livewire\WithPagination;
use Unusualdope\LaravelEcommerce\Models\Tax\Tax;

class TaxIndex extends Component
{
    use WithPagination;

    public int $tax_id = 0;

    public bool $show_delete_modal = false;

    public function requestDelete(int $id): void
    {
        $this->tax_id = $id;
        $this->show_delete_modal = true;
    }

    public function delete(): void
    {
        Tax::findOrFail($this->tax_id)->delete();
        $this->show_delete_modal = false;
        $this->tax_id = 0;
        session()->flash('status', __('ecommerce::taxes.tax_deleted'));
    }

    public function render()
    {
        return view('ecommerce::livewire.admin.administration.tax.tax-index', [
            'taxes' => Tax::query()
                ->orderBy('id', 'desc')
                ->paginate(15),
        ]);
    }
}

