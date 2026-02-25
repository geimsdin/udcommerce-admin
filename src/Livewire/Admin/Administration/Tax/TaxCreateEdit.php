<?php

namespace Unusualdope\LaravelEcommerce\Livewire\Admin\Administration\Tax;

use Livewire\Component;
use Unusualdope\LaravelEcommerce\Models\Tax\Tax;
use Unusualdope\LaravelEcommerce\Models\Tax\TaxLanguage;

class TaxCreateEdit extends Component
{
    public ?Tax $tax = null;

    public bool $isEditing = false;

    public $languageModel;

    public $selected_language;

    public array $name = [];

    public float $rate = 0;

    public bool $active = true;

    public ?int $id_country = null;

    public ?int $id_state = null;

    public ?string $zipcode_from = null;

    public ?string $zipcode_to = null;

    public function mount(?Tax $tax = null): void
    {
        $languageModel = config('lmt.language_model');
        $this->languageModel = $languageModel;
        
        if ($tax?->exists) {
            $this->isEditing = true;
            $this->tax = $tax;
            $this->rate = $tax->rate;
            $this->active = $tax->active;
            $this->id_country = $tax->id_country;
            $this->id_state = $tax->id_state;
            $this->zipcode_from = $tax->zipcode_from;
            $this->zipcode_to = $tax->zipcode_to;
            $this->loadTranslatableData();
        } else {
            $this->tax = new Tax;
            $this->initializeTranslatableData();
        }
        
        $this->selected_language = $this->languageModel::getDefaultLanguage();
    }

    protected function initializeTranslatableData(): void
    {
        $languages = $this->languageModel::getLanguagesForMultilangForm();
        foreach ($languages as $language) {
            $this->name[$language['id']] = '';
        }
    }

    protected function loadTranslatableData(): void
    {
        $languages = $this->languageModel::getLanguagesForMultilangForm();
        foreach ($languages as $language) {
            $langData = $this->tax->languages()->where('language_id', $language['id'])->first();
            $this->name[$language['id']] = $langData?->name ?? '';
        }
    }

    public function save(): void
    {
        $languages = $this->languageModel::getLanguagesForMultilangForm();
        
        $this->validate([
            'name.*' => ['required', 'string', 'max:32'],
            'rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'active' => ['required', 'boolean'],
            'id_country' => ['nullable', 'integer'],
            'id_state' => ['nullable', 'integer'],
            'zipcode_from' => ['nullable', 'string', 'max:12'],
            'zipcode_to' => ['nullable', 'string', 'max:12'],
        ]);

        $fields = [
            'rate' => $this->rate,
            'active' => $this->active,
            'id_country' => $this->id_country,
            'id_state' => $this->id_state,
            'zipcode_from' => $this->zipcode_from,
            'zipcode_to' => $this->zipcode_to,
        ];

        if (!$this->tax->exists) {
            $this->tax = Tax::create($fields);
        } else {
            $this->tax->update($fields);
        }

        foreach ($languages as $language) {
            TaxLanguage::updateOrCreate(
                [
                    'tax_id' => $this->tax->id,
                    'language_id' => $language['id'],
                ],
                [
                    'name' => $this->name[$language['id']] ?? '',
                ]
            );
        }

        if ($this->isEditing) {
            session()->flash('status', __('ecommerce::taxes.tax_updated'));
        } else {
            session()->flash('status', __('ecommerce::taxes.tax_created'));
        }

        $this->redirect(route(config('ud-ecommerce.admin_route_prefix', 'admin').'.taxes.index'), navigate: true);
    }

    public function render()
    {
        return view('ecommerce::livewire.admin.administration.tax.tax-create-edit', [
            'isEditing' => $this->isEditing,
        ]);
    }
}

