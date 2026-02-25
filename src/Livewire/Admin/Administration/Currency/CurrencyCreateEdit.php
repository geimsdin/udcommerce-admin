<?php

namespace Unusualdope\LaravelEcommerce\Livewire\Admin\Administration\Currency;

use Livewire\Component;
use Unusualdope\LaravelEcommerce\Models\Administration\Currency;

class CurrencyCreateEdit extends Component
{
    public Currency $currency;

    public bool $isEditing = false;

    public string $name = '';

    public string $iso_code = '';

    public float $exchange_rate = 0;

    public bool $default = false;

    public function mount(Currency $currency): void
    {
        $this->currency = $currency;
        if ($currency?->exists) {
            $this->isEditing = true;
            $this->currency = $currency;
            $this->name = $currency->name;
            $this->iso_code = $currency->iso_code;
            $this->exchange_rate = $currency->exchange_rate;
            $this->default = $currency->default;
        }
    }

    public function updatedDefault($value): void
    {
        $this->exchange_rate = $value ? 1 : 0;
    }

    public function save(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'iso_code' => ['required', 'string', 'max:3', 'unique:currencies,iso_code,'.$this->currency->id],
            'exchange_rate' => ['required', 'numeric', 'min:0'],
            'default' => ['required', 'boolean'],
        ]);
        $fields = [
            'name' => $this->name,
            'iso_code' => $this->iso_code,
            'exchange_rate' => $this->exchange_rate,
            'default' => $this->default, 
        ];
        if($this->default) {
            Currency::where('id', '!=', $this->currency->id)->update(['default' => false]);
        }
        if ($this->currency?->exists) {
            $this->currency->update($fields);
            session()->flash('status', __('ecommerce::currencies.currency_updated'));
        } else {
            $this->currency = Currency::create($fields);
            session()->flash('status', __('ecommerce::currencies.currency_created'));
        }
        $this->redirect(route(config('ud-ecommerce.admin_route_prefix', 'admin').'.currencies.index'), navigate: true);
    }

    public function render()
    {
        return view('ecommerce::livewire.admin.administration.currency.currency-create-edit', [
            'isEditing' => $this->isEditing,
        ]);
    }
}
