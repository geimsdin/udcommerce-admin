<?php

namespace Unusualdope\LaravelEcommerce\Livewire\Admin\Administration\Currency;

use Livewire\Component;
use Livewire\WithPagination;
use Unusualdope\LaravelEcommerce\Models\Administration\Currency;

class CurrencyIndex extends Component
{
    use WithPagination;

    public int $currency_id = 0;

    public bool $show_delete_modal = false;

    public function requestDelete(int $id): void
    {
        $this->currency_id = $id;
        $this->show_delete_modal = true;
    }

    public function delete(): void
    {
        Currency::findOrFail($this->currency_id)->delete();
        $this->show_delete_modal = false;
        $this->currency_id = 0;
        session()->flash('status', __('ecommerce::currencies.currency_deleted'));
    }

    public function render()
    {
        return view('ecommerce::livewire.admin.administration.currency.currency-index', [
            'currencies' => Currency::query()
                ->paginate(15),
        ]);
    }
}
