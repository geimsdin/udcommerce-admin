<div class="max-w-2xl space-y-6">
    {{-- Header --}}
    <div>
        <flux:heading size="xl">
            {{ $isEditing ? __('ecommerce::target-units.edit_target_unit') : __('ecommerce::target-units.create_target_unit') }}
        </flux:heading>
        <flux:subheading>
            {{ $isEditing ? __('ecommerce::target-units.messages.edit_subtitle') : __('ecommerce::target-units.messages.create_subtitle') }}
        </flux:subheading>
    </div>

    {{-- Card --}}
    <flux:card>
        <form wire:submit="save" class="space-y-6">
            {{-- Title --}}
            <flux:input
                wire:model="name"
                :label="__('ecommerce::target-units.form.name')"
                placeholder="{{ __('ecommerce::target-units.form.name_placeholder') }}"
                required
            />

            {{-- Buttons --}}
            <div class="flex items-center gap-4">
                <flux:button variant="primary" type="submit">
                    {{ $isEditing ? __('ecommerce::target-units.update_target_unit') : __('ecommerce::target-units.create_target_unit') }}
                </flux:button>
                <flux:button variant="ghost" :href="route(config('ud-ecommerce.admin_route_prefix', 'admin').'.target-units.index')" wire:navigate>
                    {{ __('ecommerce::target-units.form.cancel') }}
                </flux:button>
            </div>
        </form>
    </flux:card>
</div>

