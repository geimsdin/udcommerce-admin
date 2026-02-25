<div class="max-w-2xl space-y-6">
    {{-- Header --}}
    <div>
        <flux:heading size="xl">
            {{ $isEditing ? __('ecommerce::taxes.edit_tax') : __('ecommerce::taxes.add_tax') }}
        </flux:heading>
        <flux:subheading>
            {{ $isEditing ? __('ecommerce::taxes.messages.edit_subtitle') : __('ecommerce::taxes.messages.create_subtitle') }}
        </flux:subheading>
    </div>

    {{-- Card --}}
    <flux:card>
        <form wire:submit="save" class="space-y-6">
            {{-- Name (Multi-language) --}}
            <flux:card>
                <div class="mb-5">
                    <livewire:lmt-LangSelector wire:model.live="selected_language"/>
                </div>
                <livewire:lmt-TextInput
                    label="{{ __('ecommerce::taxes.form.name') }}"
                    placeholder="{{ __('ecommerce::taxes.form.name_placeholder') }}"
                    wire:model="name"
                    :required="true"
                />
            </flux:card>

            {{-- Rate & Activation --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:input
                    type="number"
                    step="0.01"
                    wire:model="rate"
                    :label="__('ecommerce::taxes.form.rate')"
                    placeholder="{{ __('ecommerce::taxes.form.rate_placeholder') }}"
                    :badge="__('common.required')"
                    required
                />

                <div class="flex items-center gap-2">
                    <flux:switch wire:model="active" :label="__('ecommerce::taxes.form.active')" />
                </div>
            </div>

            {{-- Geographic conditions --}}
            <flux:card class="mt-4">
                <div class="mb-3">
                    <flux:heading size="md">{{ __('ecommerce::taxes.geographic_conditions') }}</flux:heading>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:input
                        type="number"
                        wire:model="id_country"
                        :label="__('ecommerce::taxes.form.country_id')"
                        placeholder="{{ __('ecommerce::taxes.form.country_id_placeholder') }}"
                    />
                    <flux:input
                        type="number"
                        wire:model="id_state"
                        :label="__('ecommerce::taxes.form.state_id')"
                        placeholder="{{ __('ecommerce::taxes.form.state_id_placeholder') }}"
                    />
                    <flux:input
                        type="text"
                        wire:model="zipcode_from"
                        :label="__('ecommerce::taxes.form.zipcode_from')"
                        placeholder="{{ __('ecommerce::taxes.form.zipcode_from_placeholder') }}"
                    />
                    <flux:input
                        type="text"
                        wire:model="zipcode_to"
                        :label="__('ecommerce::taxes.form.zipcode_to')"
                        placeholder="{{ __('ecommerce::taxes.form.zipcode_to_placeholder') }}"
                    />
                </div>
            </flux:card>

            <flux:separator />

            {{-- Buttons --}}
            <div class="flex items-center gap-4">
                <flux:button variant="primary" type="submit">
                    {{ $isEditing ? __('ecommerce::taxes.update_tax') : __('ecommerce::taxes.create_tax') }}
                </flux:button>
                <flux:button variant="ghost" :href="route(config('ud-ecommerce.admin_route_prefix', 'admin').'.taxes.index')" wire:navigate>
                    {{ __('common.cancel') }}
                </flux:button>
            </div>
        </form>
    </flux:card>
</div>

