<?php

namespace Unusualdope\LaravelEcommerce\Livewire\Admin\Order\OrderStatus;

use Livewire\Component;
use Unusualdope\LaravelEcommerce\Models\Order\OrderStatus;
use Unusualdope\LaravelEcommerce\Models\Order\OrderStatusLanguage;

class OrderStatusCreateEdit extends Component
{
    public ?OrderStatus $orderStatus = null;

    public $isEditing = false;

    public $languageModel;

    public $selected_language;

    public array $name = [];

    public bool $sends_email = false;

    public ?string $email_template = null;

    public string $color = '#000000';

    public ?string $icon = null;

    public function mount(?OrderStatus $orderStatus = null): void
    {
        $languageModel = config('lmt.language_model', 'App\Models\Configuration\Language');
        $this->languageModel = $languageModel;
        $this->orderStatus = $orderStatus;

        if ($orderStatus?->exists) {
            $this->isEditing = true;
            $this->sends_email = $orderStatus->sends_email ?? false;
            $this->email_template = $orderStatus->email_template;
            $this->color = $orderStatus->color ?? '#000000';
            $this->icon = $orderStatus->icon;
            $this->loadTranslatableData();
        } else {
            $this->orderStatus = new OrderStatus;
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
            $langData = $this->orderStatus->getSpecificLanguage($language['id']);
            $this->name[$language['id']] = $langData?->name ?? '';
        }
    }

    public function save(): void
    {
        $languages = $this->languageModel::getLanguagesForMultilangForm();

        // Validate at least one name is provided
        $hasName = false;
        foreach ($languages as $language) {
            if (! empty($this->name[$language['id']])) {
                $hasName = true;
                break;
            }
        }

        if (! $hasName) {
            $this->addError('name', __('ecommerce::order_statuses.name_required'));

            return;
        }

        if (! $this->orderStatus->exists) {
            $this->orderStatus = OrderStatus::create([
                'sends_email' => $this->sends_email,
                'email_template' => $this->email_template,
                'color' => $this->color,
                'icon' => $this->icon,
            ]);
        } else {
            $this->orderStatus->update([
                'sends_email' => $this->sends_email,
                'email_template' => $this->email_template,
                'color' => $this->color,
                'icon' => $this->icon,
            ]);
        }

        foreach ($languages as $language) {
            OrderStatusLanguage::updateOrCreate(
                [
                    'order_status_id' => $this->orderStatus->id,
                    'language_id' => $language['id'],
                ],
                [
                    'name' => $this->name[$language['id']] ?? '',
                ]
            );
        }

        if ($this->isEditing) {
            session()->flash('status', __('ecommerce::order_statuses.order_status_updated'));
        } else {
            session()->flash('status', __('ecommerce::order_statuses.order_status_created'));
        }

        $this->redirect(route(config('ud-ecommerce.admin_route_prefix', 'admin').'.order-statuses.index'), navigate: true);
    }

    public function render()
    {
        return view('ecommerce::livewire.admin.order.order-status.order-status-create-edit', [
            'isEditing' => $this->isEditing,
        ]);
    }
}
