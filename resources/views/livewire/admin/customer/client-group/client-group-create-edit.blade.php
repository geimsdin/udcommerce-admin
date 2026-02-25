<div class="space-y-6">
    {{-- Header --}}
    <div>
        <flux:heading size="xl">
            {{ $isEditing ? __('client_groups.edit_client_group') : __('client_groups.add_client_group') }}
        </flux:heading>
        <flux:subheading>
            {{ $isEditing ? __('client_groups.messages.edit_subtitle') : __('client_groups.messages.create_subtitle') }}
        </flux:subheading>
    </div>

    {{-- Card --}}
    <flux:card>
        <form wire:submit="save" class="space-y-6">
            <div class="grid grid-cols-2 gap-6">
                <flux:input wire:model="name" :label="__('client_groups.form.name')" placeholder="{{ __('client_groups.form.name_placeholder') }}" :badge="__('common.required')"/>
                <flux:input type="color" wire:model="color" :label="__('client_groups.form.color')" placeholder="{{ __('client_groups.form.color_placeholder') }}" />
                <flux:switch wire:model="default" :label="__('client_groups.form.default')" />
            </div>
            <flux:separator />

            {{-- Buttons --}}
            <div class="flex items-center gap-4">
                <flux:button variant="primary" type="submit">
                    {{ $isEditing ? __('client_groups.update_client_group') : __('client_groups.create_client_group') }}
                </flux:button>
                <flux:button variant="ghost" :href="route(config('ud-ecommerce.admin_route_prefix', 'admin').'.client-groups.index')" wire:navigate>
                    {{ __('common.cancel') }}
                </flux:button>
            </div>
        </form>
    </flux:card>
</div>
