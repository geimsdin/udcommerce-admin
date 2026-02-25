<?php

namespace Unusualdope\LaravelEcommerce\Livewire\Admin\Warehouse\VariantGroup;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Unusualdope\LaravelEcommerce\Models\Stock\VariantGroup;
use Unusualdope\LaravelEcommerce\Models\Stock\VariantGroupLanguage;

class VariantGroupCreateEdit extends Component
{
    public ?VariantGroup $variantgroup = null;

    public string $type = '';

    public array $name = [];

    public array $tooltip = [];

    public $languageModel;

    public $selected_language;

    public array $types = [
        'radio',
        'select',
        'color',
    ];

    public function mount(?VariantGroup $variantgroup = null): void
    {
        $languageModel = config('lmt.language_model', 'App\Models\Configuration\Language');
        $this->languageModel = $languageModel;
        if ($variantgroup?->exists) {
            $this->variantgroup = $variantgroup;
            $this->type = $variantgroup->type ?? '';
            $this->loadTranslatableData();
        } else {
            $this->variantgroup = new VariantGroup;
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
            $langData = $this->variantgroup->getSpecificLanguage($language['id']);
            $this->name[$language['id']] = $langData?->name ?? '';
            $this->tooltip[$language['id']] = $langData?->tooltip ?? '';
        }
    }

    public function save(): void
    {
        $languages = $this->languageModel::getLanguagesForMultilangForm();
        $this->validate([
            'type' => 'required',
        ]);

        DB::transaction(function () use ($languages) {
            // Save the main model if it doesn't exist
            if (! $this->variantgroup->exists) {
                $this->variantgroup = VariantGroup::create([
                    'type' => ($this->type == '') ? null : $this->type,
                ]);
            } else {
                $this->variantgroup->update([
                    'type' => ($this->type == '') ? null : $this->type,
                ]);
            }

            // Save translations for each language
            foreach ($languages as $language) {
                VariantGroupLanguage::updateOrCreate(
                    [
                        'variant_group_id' => $this->variantgroup->id,
                        'language_id' => $language['id'],
                    ],
                    [
                        'name' => $this->name[$language['id']] ?? '',
                        'tooltip' => $this->tooltip[$language['id']] ?? '',
                    ]
                );
            }
        });

        $isEditing = $this->variantgroup->wasRecentlyCreated === false && $this->variantgroup->exists;

        if ($isEditing) {
            session()->flash('status', __('ecommerce::variantgroups.variantgroups_updated'));
        } else {
            session()->flash('status', __('ecommerce::variantgroups.variantgroups_created'));
        }
        $this->redirect(route(config('ud-ecommerce.admin_route_prefix', 'admin').'.variantgroups.index'), navigate: true);
    }

    public function render()
    {
        return view('ecommerce::livewire.admin.warehouse.variant-group.create-edit', [
            'isEditing' => $this->variantgroup?->exists ?? false,
        ]);
    }
}
