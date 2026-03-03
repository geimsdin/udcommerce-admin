<div>
    {{-- Header --}}
    <div class="flex items-center justify-between mb-4">
        <div>
            <flux:heading size="xl">
                {{ $isEditing ? __('ecommerce::image-settings.edit_image_type') : __('ecommerce::image-settings.add_image_type') }}
            </flux:heading>
            <flux:subheading>
                {{ $isEditing ? __('ecommerce::image-settings.messages.edit_subtitle') : __('ecommerce::image-settings.messages.create_subtitle') }}
            </flux:subheading>
        </div>
    </div>

    {{-- Card --}}
    <flux:card>
        <form wire:submit="save" class="space-y-6">
            {{-- Name --}}
            <flux:input wire:model="name" :label="__('ecommerce::image-settings.form.name')"
                placeholder="{{ __('ecommerce::image-settings.form.name_placeholder') }}" :badge="__('common.required')"
                :description="__('ecommerce::image-settings.form.name_description')" />

            {{-- Dimensions --}}
            <flux:heading size="md">{{ __('ecommerce::image-settings.form.dimensions') }}</flux:heading>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:input wire:model="width" type="number" min="10" max="4000"
                    :label="__('ecommerce::image-settings.form.width')" placeholder="300" suffix="px"
                    :badge="__('common.required')" />
                <flux:input wire:model="height" type="number" min="10" max="4000"
                    :label="__('ecommerce::image-settings.form.height')" placeholder="300" suffix="px"
                    :badge="__('common.required')" />
            </div>

            {{-- Entity toggles --}}
            <flux:separator />
            <flux:heading size="md">{{ __('ecommerce::image-settings.form.apply_to') }}</flux:heading>
            <flux:subheading>{{ __('ecommerce::image-settings.form.apply_to_description') }}</flux:subheading>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
                <flux:switch wire:model="products" :label="__('ecommerce::image-settings.form.products')"
                    :description="__('ecommerce::image-settings.form.products_description')" />
                <flux:switch wire:model="categories" :label="__('ecommerce::image-settings.form.categories')"
                    :description="__('ecommerce::image-settings.form.categories_description')" />
                <flux:switch wire:model="brands" :label="__('ecommerce::image-settings.form.brands')"
                    :description="__('ecommerce::image-settings.form.brands_description')" />
            </div>

            {{-- Actions --}}
            <flux:separator />

            <div class="flex items-center gap-4 justify-end">
                <flux:button variant="ghost"
                    :href="route(config('ud-ecommerce.admin_route_prefix', 'admin').'.image-settings.index')"
                    wire:navigate>
                    {{ __('common.cancel') }}
                </flux:button>
                <flux:button variant="primary" type="submit">
                    {{ $isEditing ? __('ecommerce::image-settings.update_image_type') : __('ecommerce::image-settings.create_image_type') }}
                </flux:button>
            </div>
        </form>
    </flux:card>
</div>