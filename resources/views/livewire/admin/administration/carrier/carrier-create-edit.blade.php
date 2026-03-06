<div class="space-y-6">
    {{-- Header --}}
    <div>
        <flux:heading size="xl">
            {{ $isEditing ? __('ecommerce::carriers.edit_carrier') : __('ecommerce::carriers.add_carrier') }}
        </flux:heading>
        <flux:subheading>
            {{ $isEditing ? __('ecommerce::carriers.messages.edit_subtitle') : __('ecommerce::carriers.messages.create_subtitle') }}
        </flux:subheading>
    </div>
    
    {{-- Card --}}
    <flux:card>
        <form wire:submit="save" class="space-y-6">
            {{-- Name (Multi-language) --}}
            <flux:card>
                <div class="mb-5">
                    <livewire:lmt-LangSelector wire:model.live="selected_language" />
                </div>
                <livewire:lmt-TextInput
                label="{{ __('ecommerce::carriers.form.name') }}"
                placeholder="{{ __('ecommerce::carriers.form.name_placeholder') }}"
                wire:model="name"
                :required="true"
                />
                <livewire:lmt-Textarea
                label="{{ __('ecommerce::carriers.form.description') }}"
                placeholder="{{ __('ecommerce::carriers.form.description_placeholder') }}"
                wire:model="description"
                :required="true"
                />
            </flux:card>
            <div class="grid grid-cols-2 gap-4">
                <flux:input wire:model="price" :label="__('ecommerce::carriers.form.price')" placeholder="{{ __('ecommerce::carriers.form.price_placeholder') }}" />
                <flux:input wire:model="icon" :label="__('ecommerce::carriers.form.icon')" placeholder="{{ __('ecommerce::carriers.form.icon_placeholder') }}" />
                <flux:switch wire:model="active" :label="__('ecommerce::carriers.form.active')" />
            </div>
            <flux:separator />

            {{-- Buttons --}}
            <div class="flex items-center gap-4">
                <flux:button variant="primary" type="submit">
                    {{ $isEditing ? __('ecommerce::carriers.update_carrier') : __('ecommerce::carriers.create_carrier') }}
                </flux:button>
                <flux:button variant="ghost" :href="route(config('ud-ecommerce.admin_route_prefix', 'admin').'.carriers.index')" wire:navigate>
                    {{ __('common.cancel') }}
                </flux:button>
            </div>
        </form>
    </flux:card>
</div>
