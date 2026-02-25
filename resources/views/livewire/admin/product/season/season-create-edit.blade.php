<div class="max-w-2xl space-y-6">
    {{-- Header --}}
    <div>
        <flux:heading size="xl">
            {{ $isEditing ? __('ecommerce::seasons.edit_season') : __('ecommerce::seasons.create_season') }}
        </flux:heading>
        <flux:subheading>
            {{ $isEditing ? __('ecommerce::seasons.messages.edit_subtitle') : __('ecommerce::seasons.messages.create_subtitle') }}
        </flux:subheading>
    </div>

    {{-- Language Selector --}}
    
    {{-- Card --}}
    <flux:card>
        <form wire:submit="save" class="space-y-6">
            <flux:card>
                <div class="mb-5">
                    <livewire:lmt-LangSelector wire:model.live="selected_language"/>
                </div>
                <livewire:lmt-TextInput
                    label="{{ __('ecommerce::seasons.form.name') }}"
                    placeholder="{{ __('ecommerce::seasons.form.name_placeholder') }}"
                    wire:model="name"
                    :required="true"
                />
            </flux:card>
            @if (!$ready_stock)
                <div class="grid grid-cols-2 gap-4">
                    <flux:date-picker
                        wire:model="date_start"
                        :label="__('ecommerce::seasons.form.date_start')"
                    />
                    <flux:date-picker
                        wire:model="date_end"
                        :label="__('ecommerce::seasons.form.date_end')"
                    />
                </div>
            @endif
            <flux:switch
                wire:model.live="ready_stock"
                :label="__('ecommerce::seasons.form.ready_stock')"
            />
            <flux:separator />

            {{-- Buttons --}}
            <div class="flex items-center gap-4">
                <flux:button variant="primary" type="submit">
                    {{ $isEditing ? __('ecommerce::seasons.update_season') : __('ecommerce::seasons.create_season') }}
                </flux:button>
                <flux:button variant="ghost" :href="route(config('ud-ecommerce.admin_route_prefix', 'admin').'.seasons.index')" wire:navigate>
                    {{ __('ecommerce::seasons.form.cancel') }}
                </flux:button>
            </div>
        </form>
    </flux:card>
</div>
