<?php

namespace Unusualdope\LaravelEcommerce\Livewire\Admin\Product\FeatureGroup;

use Livewire\Component;
use Unusualdope\LaravelEcommerce\Models\Product\FeatureGroup;
use Unusualdope\LaravelEcommerce\Models\Product\FeatureGroupLanguage;

class FeatureGroupCreateEdit extends Component
{
    public ?FeatureGroup $featureGroup = null;

    public $isEditing = false;

    public $languageModel;

    public $selected_language;

    public array $name = [];

    public array $tooltip = [];

    public function mount(?FeatureGroup $featureGroup = null): void
    {
        $languageModel = config('lmt.language_model');
        $this->languageModel = $languageModel;
        if ($featureGroup?->exists) {
            $this->isEditing = true;
            $this->featureGroup = $featureGroup;
            $this->loadTranslatableData();
        } else {
            $this->featureGroup = new FeatureGroup;
            $this->initializeTranslatableData();
        }
        $this->selected_language = $this->languageModel::getDefaultLanguage();
    }

    protected function initializeTranslatableData(): void
    {
        $languages = $this->languageModel::getLanguagesForMultilangForm();
        foreach ($languages as $language) {
            $this->name[$language['id']] = '';
            $this->tooltip[$language['id']] = '';
        }
    }

    protected function loadTranslatableData(): void
    {
        $languages = $this->languageModel::getLanguagesForMultilangForm();
        foreach ($languages as $language) {
            $langData = $this->featureGroup->getSpecificLanguage($language['id']);
            $this->name[$language['id']] = $langData?->name ?? '';
            $this->tooltip[$language['id']] = $langData?->tooltip ?? '';
        }
    }

    public function save(): void
    {
        $languages = $this->languageModel::getLanguagesForMultilangForm();
        if (! $this->featureGroup->exists) {
            $this->featureGroup = FeatureGroup::create([
                'position' => 0,
            ]);
        }
        foreach ($languages as $language) {
            FeatureGroupLanguage::updateOrCreate(
                [
                    'feature_group_id' => $this->featureGroup->id,
                    'language_id' => $language['id'],
                ],
                [
                    'name' => $this->name[$language['id']] ?? '',
                    'tooltip' => $this->tooltip[$language['id']] ?? '',
                ]
            );
        }
        if ($this->isEditing) {
            session()->flash('status', __('ecommerce::feature-groups.feature_group_updated'));
        } else {
            session()->flash('status', __('ecommerce::feature-groups.feature_group_created'));
        }
        $this->redirect(route(config('ud-ecommerce.admin_route_prefix', 'admin').'.feature-groups.index'), navigate: true);
    }

    public function render()
    {
        return view('ecommerce::livewire.admin.product.feature-group.feature-group-create-edit', [
            'isEditing' => $this->isEditing,
        ]);
    }
}
