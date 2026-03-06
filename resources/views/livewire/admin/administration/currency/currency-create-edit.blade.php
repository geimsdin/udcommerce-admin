<div class="max-w-2xl space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-4">
        <div>
            <flux:heading size="xl">{{ $isEditing ? __('ecommerce::currencies.edit_currency') : __('ecommerce::currencies.create_currency') }}</flux:heading>
            <flux:subheading>{{ $isEditing ? __('ecommerce::currencies.messages.edit_subtitle') : __('ecommerce::currencies.messages.create_subtitle') }}</flux:subheading>
        </div>
    </div>

    {{-- Card --}}
    <flux:card>
        <form wire:submit="save" class="space-y-6">
            <div class="grid grid-cols-2 gap-4">
                <flux:input wire:model="name" :label="__('ecommerce::currencies.form.name')" placeholder="{{ __('ecommerce::currencies.form.name_placeholder') }}" :badge="__('common.required')"/>
                <flux:input wire:model="iso_code" :label="__('ecommerce::currencies.form.code')" placeholder="{{ __('ecommerce::currencies.form.code_placeholder') }}" :badge="__('common.required')"/>
                <flux:input wire:model="exchange_rate" :label="__('ecommerce::currencies.form.exchange_rate')" placeholder="{{ __('ecommerce::currencies.form.exchange_rate_placeholder') }}" :disabled="$default"/>
                <div class="flex items-center gap-2">
                    <flux:switch wire:model.live="default" :label="__('ecommerce::currencies.form.default')" />
                </div>
            </div>
            <flux:separator />
            <div class="flex items-center gap-4 justify-end">
                <flux:button variant="ghost" :href="route(config('ud-ecommerce.admin_route_prefix', 'admin').'.currencies.index')" wire:navigate>
                    {{ __('common.cancel') }}
                </flux:button>
                <flux:button variant="primary" type="submit">
                    {{ $isEditing ? __('ecommerce::currencies.update_currency') : __('ecommerce::currencies.create_currency') }}
                </flux:button>
            </div>
        </form>
    </flux:card>
</div>
