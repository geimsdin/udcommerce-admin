<div class="space-y-6 max-w-2xl">
    <div>
        <flux:heading size="xl">
            {{ $field ? __('ecommerce::country_address_custom_fields.title') : __('ecommerce::country_address_custom_fields.add_field') }}
        </flux:heading>
        <flux:subheading>{{ __('ecommerce::country_address_custom_fields.subtitle') }}</flux:subheading>
    </div>

    <flux:card>
        <form wire:submit="save" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <flux:input wire:model="country"
                    label="{{ __('ecommerce::country_address_custom_fields.form.country') }}" required
                    placeholder="IT" />

                <flux:input wire:model="name" label="{{ __('ecommerce::country_address_custom_fields.form.name') }}"
                    required placeholder="fiscal_code" />
            </div>

            <flux:input wire:model="label" label="{{ __('ecommerce::country_address_custom_fields.form.label') }}"
                required placeholder="Codice Fiscale" />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <flux:select wire:model="type" label="{{ __('ecommerce::country_address_custom_fields.form.type') }}">
                    <flux:select.option value="text">Text</flux:select.option>
                    <flux:select.option value="textarea">Textarea</flux:select.option>
                    <flux:select.option value="number">Number</flux:select.option>
                </flux:select>

                <div class="flex flex-col gap-4 justify-end pb-2">
                    <flux:checkbox wire:model="is_required"
                        label="{{ __('ecommerce::country_address_custom_fields.form.is_required') }}" />
                    <flux:checkbox wire:model="is_active"
                        label="{{ __('ecommerce::country_address_custom_fields.form.is_active') }}" />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <flux:input type="number" wire:model="min_length"
                    label="{{ __('ecommerce::country_address_custom_fields.form.min_length') }}" />

                <flux:input type="number" wire:model="max_length"
                    label="{{ __('ecommerce::country_address_custom_fields.form.max_length') }}" />
            </div>

            <div class="flex items-center gap-2 pt-4">
                <flux:button variant="primary" type="submit">
                    {{ __('ecommerce::country_address_custom_fields.form.save') }}
                </flux:button>

                <flux:button
                    :href="route(config('ud-ecommerce.admin_route_prefix', 'ecommerce') . '.configs.country_address_custom_fields.index')"
                    wire:navigate>
                    {{ __('ecommerce::country_address_custom_fields.form.cancel') }}
                </flux:button>
            </div>
        </form>
    </flux:card>
</div>