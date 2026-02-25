<div class="space-y-6">
    {{-- Header --}}
    <div>
        <flux:heading size="xl">
            {{ $isEditing ? __('carriers.edit_carrier') : __('carriers.add_carrier') }}
        </flux:heading>
        <flux:subheading>
            {{ $isEditing ? __('carriers.messages.edit_subtitle') : __('carriers.messages.create_subtitle') }}
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
                label="{{ __('carriers.form.name') }}"
                placeholder="{{ __('carriers.form.name_placeholder') }}"
                wire:model="name"
                :required="true"
                />
                <livewire:lmt-Textarea
                label="{{ __('carriers.form.description') }}"
                placeholder="{{ __('carriers.form.description_placeholder') }}"
                wire:model="description"
                :required="true"
                />
            </flux:card>
            <div class="grid grid-cols-2 gap-4">
                <flux:input wire:model="price" :label="__('carriers.form.price')" placeholder="{{ __('carriers.form.price_placeholder') }}" />
                <flux:input wire:model="icon" :label="__('carriers.form.icon')" placeholder="{{ __('carriers.form.icon_placeholder') }}" />
                <flux:switch wire:model="active" :label="__('carriers.form.active')" />
            </div>
            <flux:separator />

            {{-- Buttons --}}
            <div class="flex items-center gap-4">
                <flux:button variant="primary" type="submit">
                    {{ $isEditing ? __('carriers.update_carrier') : __('carriers.create_carrier') }}
                </flux:button>
                <flux:button variant="ghost" :href="route(config('ud-ecommerce.admin_route_prefix', 'admin').'.carriers.index')" wire:navigate>
                    {{ __('common.cancel') }}
                </flux:button>
            </div>
        </form>
    </flux:card>
</div>
