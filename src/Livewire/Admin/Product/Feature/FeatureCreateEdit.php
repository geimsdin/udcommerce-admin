<?php

namespace Unusualdope\LaravelEcommerce\Livewire\Admin\Product\Feature;

use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Unusualdope\LaravelEcommerce\Models\Product\Feature;
use Unusualdope\LaravelEcommerce\Models\Product\FeatureGroup;
use Unusualdope\LaravelEcommerce\Models\Product\FeatureLanguage;

class FeatureCreateEdit extends Component
{
    public ?Feature $feature = null;

    public $isEditing = false;

    public $languageModel;

    public $selected_language;

    #[Validate('required')]
    public $feature_group_id;

    public array $name = [];

    public function mount(?Feature $feature = null): void
    {
        $languageModel = config('lmt.language_model');
        $this->languageModel = $languageModel;
        if ($feature?->exists) {
            $this->isEditing = true;
            $this->feature = $feature;
            $this->feature_group_id = $feature->feature_group_id;
            $this->loadTranslatableData();
        } else {
            $this->feature = new Feature;
            $this->initializeTranslatableData();
        }
        $this->selected_language = $this->languageModel::getDefaultLanguage();
    }

    #[Computed]
    public function featureGroups()
    {
        return FeatureGroup::orderBy('position')->get();
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
            $langData = $this->feature->getSpecificLanguage($language['id']);
            $this->name[$language['id']] = $langData?->name ?? '';
        }
    }

    public function save(): void
    {
        $languages = $this->languageModel::getLanguagesForMultilangForm();
        $this->validate();
        if (! $this->feature->exists) {
            $this->feature = Feature::create([
                'feature_group_id' => $this->feature_group_id,
            ]);
        } else {
            $this->feature->update([
                'feature_group_id' => $this->feature_group_id,
            ]);
        }
        foreach ($languages as $language) {
            FeatureLanguage::updateOrCreate(
                [
                    'feature_id' => $this->feature->id,
                    'language_id' => $language['id'],
                ],
                [
                    'name' => $this->name[$language['id']] ?? '',
                ]
            );
        }
        if ($this->isEditing) {
            session()->flash('status', __('ecommerce::features.feature_updated'));
        } else {
            session()->flash('status', __('ecommerce::features.feature_created'));
        }
        $this->redirect(route(config('ud-ecommerce.admin_route_prefix', 'admin').'.features.index'), navigate: true);
    }

    public function render()
    {
        return view('ecommerce::livewire.admin.product.feature.feature-create-edit');
    }
}
