<?php

namespace Unusualdope\LaravelEcommerce\Livewire\Admin\Administration\CountryAddressCustomField;

use Livewire\Component;
use Livewire\WithPagination;
use Unusualdope\LaravelEcommerce\Models\CountryAddressCustomField;

class CountryAddressCustomFieldIndex extends Component
{
    use WithPagination;

    public $search = '';

    public $field_id = 0;

    public function delete(int $id): void
    {
        CountryAddressCustomField::findOrFail($id)->delete();
        $this->dispatch('field-deleted');
        session()->flash('status', __('ecommerce::country_address_custom_fields.field_deleted'));
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('ecommerce::livewire.admin.administration.country-address-custom-field.country-address-custom-field-index', [
            'fields' => CountryAddressCustomField::query()
                ->when($this->search, function ($query) {
                    $query->where('country', 'like', "%{$this->search}%")
                        ->orWhere('label', 'like', "%{$this->search}%")
                        ->orWhere('name', 'like', "%{$this->search}%");
                })
                ->paginate(15),
        ]);
    }
}
