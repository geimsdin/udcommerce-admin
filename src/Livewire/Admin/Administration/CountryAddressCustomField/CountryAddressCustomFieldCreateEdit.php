<?php

namespace Unusualdope\LaravelEcommerce\Livewire\Admin\Administration\CountryAddressCustomField;

use Livewire\Component;
use Unusualdope\LaravelEcommerce\Models\CountryAddressCustomField;

class CountryAddressCustomFieldCreateEdit extends Component
{
    public ?CountryAddressCustomField $field = null;

    public $country = '';
    public $name = '';
    public $label = '';
    public $type = 'text';
    public $is_required = false;
    public $min_length = null;
    public $max_length = null;
    public $is_active = true;

    protected function rules(): array
    {
        return [
            'country' => 'required|string|size:2',
            'name' => 'required|string|max:255',
            'label' => 'required|string|max:255',
            'type' => 'required|string|in:text,textarea,number',
            'is_required' => 'boolean',
            'min_length' => 'nullable|integer|min:0',
            'max_length' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ];
    }

    public function mount(?CountryAddressCustomField $field = null): void
    {
        if ($field && $field->exists) {
            $this->field = $field;
            $this->country = $field->country;
            $this->name = $field->name;
            $this->label = $field->label;
            $this->type = $field->type;
            $this->is_required = $field->is_required;
            $this->min_length = $field->min_length;
            $this->max_length = $field->max_length;
            $this->is_active = $field->is_active;
        }
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'country' => strtoupper($this->country),
            'name' => $this->name,
            'label' => $this->label,
            'type' => $this->type,
            'is_required' => $this->is_required,
            'min_length' => $this->min_length,
            'max_length' => $this->max_length,
            'is_active' => $this->is_active,
        ];

        if ($this->field) {
            $this->field->update($data);
            session()->flash('status', __('ecommerce::country_address_custom_fields.field_updated'));
        } else {
            CountryAddressCustomField::create($data);
            session()->flash('status', __('ecommerce::country_address_custom_fields.field_created'));
        }

        $this->redirect(route(config('ud-ecommerce.admin_route_prefix', 'ecommerce') . '.configs.country_address_custom_fields.index'), navigate: true);
    }

    public function render()
    {
        return view('ecommerce::livewire.admin.administration.country-address-custom-field.country-address-custom-field-create-edit');
    }
}
