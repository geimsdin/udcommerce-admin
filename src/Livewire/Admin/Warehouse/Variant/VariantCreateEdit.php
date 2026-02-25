<?php

namespace Unusualdope\LaravelEcommerce\Livewire\Admin\Warehouse\Variant;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Unusualdope\LaravelEcommerce\Models\Stock\Variant;
use Unusualdope\LaravelEcommerce\Models\Stock\VariantGroup;
use Unusualdope\LaravelEcommerce\Models\Stock\VariantLanguage;

class VariantCreateEdit extends Component
{
    public ?Variant $variant = null;

    public string $color = '';

    public int $variant_group_id = 0;

    public array $name = [];

    public bool $is_color = false;

    public $languageModel;

    public $selected_language;

    public function mount(?Variant $variant = null): void
    {
        $languageModel = config('lmt.language_model', 'App\Models\Configuration\Language');
        $this->languageModel = $languageModel;
        if ($variant?->exists) {
            $this->variant = $variant;
            $this->color = $variant->color ?? '';
            $this->variant_group_id = $variant->variant_group_id;
            $this->is_color = $variant->variantGroup->type == 'color';
            $this->loadTranslatableData();
        } else {
            $this->variant = new Variant;
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
            $langData = $this->variant->getSpecificLanguage($language['id']);
            $this->name[$language['id']] = $langData?->name ?? '';
        }
    }

    public function updatedVariantGroupId($value)
    {
        $variantGroup = VariantGroup::find($value);
        if ($variantGroup) {
            $this->is_color = $variantGroup->type == 'color';
            if (! $this->is_color) {
                $this->color = '';
            }
        }
    }

    public function save(): void
    {
        $languages = $this->languageModel::getLanguagesForMultilangForm();
        $this->validate([
            'name' => 'required',
            'variant_group_id' => 'required',
            'color' => 'required_if:is_color,true',
        ]);

        DB::transaction(function () use ($languages) {
            if (! $this->variant->exists) {
                $this->variant = Variant::create([
                    'color' => ($this->color == '') ? null : $this->color,
                    'variant_group_id' => $this->variant_group_id,
                ]);
            } else {
                $this->variant->update([
                    'color' => ($this->color == '') ? null : $this->color,
                    'variant_group_id' => $this->variant_group_id,
                ]);
            }

            // Save translations for each language
            foreach ($languages as $language) {
                VariantLanguage::updateOrCreate(
                    [
                        'variant_id' => $this->variant->id,
                        'language_id' => $language['id'],
                    ],
                    [
                        'name' => $this->name[$language['id']] ?? '',
                    ]
                );
            }
        });

        $isEditing = $this->variant->wasRecentlyCreated === false && $this->variant->exists;

        if ($isEditing) {
            session()->flash('status', __('ecommerce::variants.variants_updated'));
        } else {
            session()->flash('status', __('ecommerce::variants.variants_created'));
        }
        $this->redirect(route(config('ud-ecommerce.admin_route_prefix', 'admin').'.variants.index'), navigate: true);
    }

    public function render()
    {
        return view('ecommerce::livewire.admin.warehouse.variant.variant-create-edit', [
            'variantgroup' => VariantGroup::orderBy('position')->get(),
            'isEditing' => $this->variant?->exists ?? false,
        ]);
    }
}
