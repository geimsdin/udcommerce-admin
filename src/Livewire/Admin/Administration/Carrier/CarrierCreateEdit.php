<?php

namespace Unusualdope\LaravelEcommerce\Livewire\Admin\Administration\Carrier;

use Livewire\Component;
use Unusualdope\LaravelEcommerce\Models\Administration\Carrier;
use Unusualdope\LaravelEcommerce\Models\Administration\CarrierLanguage;

class CarrierCreateEdit extends Component
{
    public ?Carrier $carrier = null;

    public $isEditing = false;

    public $languageModel;

    public $selected_language;

    public array $name = [];

    public array $description = [];

    public bool $active = false;

    public float $price = 0;

    public string $icon = '';

    public function mount(?Carrier $carrier = null): void
    {
        $languageModel = config('lmt.language_model');
        $this->languageModel = $languageModel;
        $this->carrier = $carrier;
        if ($carrier?->exists) {
            $this->isEditing = true;
            $this->active = $carrier->active;
            $this->price = $carrier->price;
            $this->icon = $carrier->icon;
            $this->loadTranslatableData();
        } else {
            $this->carrier = new Carrier;
            $this->initializeTranslatableData();
        }
        $this->selected_language = $this->languageModel::getDefaultLanguage();
    }

    protected function initializeTranslatableData(): void
    {
        $languages = $this->languageModel::getLanguagesForMultilangForm();
        foreach ($languages as $language) {
            $this->name[$language['id']] = '';
            $this->description[$language['id']] = '';
        }
    }

    protected function loadTranslatableData()
    {
        $languages = $this->languageModel::getLanguagesForMultilangForm();
        foreach ($languages as $language) {
            $langData = $this->carrier->getSpecificLanguage($language['id']);
            $this->name[$language['id']] = $langData?->name ?? '';
            $this->description[$language['id']] = $langData?->description ?? '';
        }
    }

    public function save(): void
    {
        $languages = $this->languageModel::getLanguagesForMultilangForm();
        if (! $this->carrier->exists) {
            $this->carrier = Carrier::create([
                'active' => $this->active,
                'price' => $this->price,
                'icon' => $this->icon,
            ]);
        } else {
            $this->carrier->update([
                'active' => $this->active,
                'price' => $this->price,
                'icon' => $this->icon,
            ]);
        }
        foreach ($languages as $language) {
            CarrierLanguage::updateOrCreate(
                [
                    'carrier_id' => $this->carrier->id,
                    'language_id' => $language['id'],
                ],
                [
                    'name' => $this->name[$language['id']] ?? '',
                    'description' => $this->description[$language['id']] ?? '',
                ]
            );
        }
        if ($this->isEditing) {
            session()->flash('status', __('ecommerce::carriers.carrier_updated'));
        } else {
            session()->flash('status', __('ecommerce::carriers.carrier_created'));
        }
        $this->redirect(route(config('ud-ecommerce.admin_route_prefix', 'admin').'.carriers.index'), navigate: true);
    }

    public function render()
    {
        return view('ecommerce::livewire.admin.administration.carrier.carrier-create-edit');
    }
}
