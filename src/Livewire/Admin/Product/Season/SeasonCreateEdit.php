<?php

namespace Unusualdope\LaravelEcommerce\Livewire\Admin\Product\Season;

use Livewire\Attributes\Validate;
use Livewire\Component;
use Unusualdope\LaravelEcommerce\Models\Product\Season;
use Unusualdope\LaravelEcommerce\Models\Product\SeasonLanguage;

class SeasonCreateEdit extends Component
{
    public ?Season $season = null;

    #[Validate('array')]
    public array $name = [];

    #[Validate('date')]
    public string $date_start = '';

    #[Validate('date')]
    public string $date_end = '';

    #[Validate('boolean')]
    public bool $ready_stock = false;

    public $languageModel;

    public $selected_language;

    public function mount(?Season $season = null): void
    {
        $languageModel = config('lmt.language_model', 'App\Models\Configuration\Language');
        $this->languageModel = $languageModel;
        if ($season?->exists) {
            $this->season = $season;
            $this->date_start = $season->date_start ?? '';
            $this->date_end = $season->date_end ?? '';
            $this->ready_stock = $season->ready_stock;
            $this->loadTranslatableData();
        } else {
            $this->season = new Season;
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
            $langData = $this->season->getSpecificLanguage($language['id']);
            $this->name[$language['id']] = $langData?->name ?? '';
        }
    }

    public function save(): void
    {
        $languages = $this->languageModel::getLanguagesForMultilangForm();
        $this->validate();
        // Save the main model if it doesn't exist
        if (! $this->season->exists) {
            $this->season = Season::create([
                'date_start' => ($this->date_start == '') ? null : $this->date_start,
                'date_end' => ($this->date_end == '') ? null : $this->date_end,
                'ready_stock' => $this->ready_stock,
            ]);
        } else {
            $this->season->update([
                'date_start' => ($this->date_start == '') ? null : $this->date_start,
                'date_end' => ($this->date_end == '') ? null : $this->date_end,
                'ready_stock' => $this->ready_stock,
            ]);
        }

        // Save translations for each language
        foreach ($languages as $language) {
            SeasonLanguage::updateOrCreate(
                [
                    'season_id' => $this->season->id,
                    'language_id' => $language['id'],
                ],
                [
                    'name' => $this->name[$language['id']] ?? '',
                ]
            );
        }

        if ($this->season->exists) {
            session()->flash('status', __('ecommerce::seasons.season_updated'));
        } else {
            session()->flash('status', __('ecommerce::seasons.season_created'));
        }
        $this->redirect(route(config('ud-ecommerce.admin_route_prefix', 'admin').'.seasons.index'), navigate: true);
    }

    public function render()
    {
        return view('ecommerce::livewire.admin.product.season.season-create-edit', [
            'isEditing' => $this->season?->exists ?? false,
        ]);
    }
}
