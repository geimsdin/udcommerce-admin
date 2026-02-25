<?php

namespace Unusualdope\LaravelEcommerce\Livewire\Admin\Size\TargetUnit;

use Flux\Flux;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Unusualdope\LaravelEcommerce\Models\Size\TargetUnit;

class TargetUnitCreateEdit extends Component
{
    public ?TargetUnit $target_unit = null;

    #[Validate('string')]
    public string $name = '';

    public function mount(?TargetUnit $target_unit = null): void
    {
        if ($target_unit?->exists) {
            $this->target_unit = $target_unit;
            $this->name = $target_unit->name;
        }
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        DB::transaction(function () {
            if ($this->target_unit?->exists) {
                $this->target_unit->update([
                    'name' => $this->name,
                ]);
            } else {
                $this->target_unit = TargetUnit::create([
                    'name' => $this->name,
                ]);
            }
        });

        $isEditing = $this->target_unit->wasRecentlyCreated === false && $this->target_unit->exists;

        Flux::toast(
            variant: 'success',
            heading: $isEditing ? __('general.updated') : __('general.created'),
            text: $isEditing ? __('ecommerce::target-units.target_unit_updated') : __('ecommerce::target-units.target_unit_created'),
        );

        $this->redirect(route(config('ud-ecommerce.admin_route_prefix', 'admin').'.target-units.index'));
    }

    public function render()
    {
        return view('ecommerce::livewire.admin.size.target-unit.create-edit', [
            'isEditing' => $this->target_unit?->exists ?? false,
        ]);
    }
}
