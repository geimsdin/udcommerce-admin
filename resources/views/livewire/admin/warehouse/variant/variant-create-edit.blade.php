<div class="max-w-2xl space-y-6">
    {{-- Header --}}
    <div>
        <flux:heading size="xl">
            {{ $isEditing ? __('ecommerce::variants.edit_variant') : __('ecommerce::variants.create_variant') }}
        </flux:heading>
        <flux:subheading>
            {{ $isEditing ? __('ecommerce::variants.messages.edit_subtitle') : __('ecommerce::variants.messages.create_subtitle') }}
        </flux:subheading>
    </div>

    {{-- Language Selector --}}
    
    {{-- Card --}}
    <form wire:submit="save" class="space-y-6">
        <flux:card>
            <div class="space-y-6">
                <flux:card>
                    <div class="mb-5">
                        <livewire:lmt-LangSelector wire:model.live="selected_language"/>
                    </div>
                    <livewire:lmt-TextInput
                        label="{{ __('ecommerce::variants.form.name') }}"
                        placeholder="{{ __('ecommerce::variants.form.name_placeholder') }}"
                        wire:model="name"
                        :required="true"
                    />
                </flux:card>

                    <div class="grid grid-cols-2 gap-4">
                        <flux:select
                            variant="listbox"
                            wire:model.live="variant_group_id"
                            :label="__('ecommerce::variants.form.variant_group_id')"
                            placeholder="{{ __('ecommerce::variants.form.variant_group_id_placeholder') }}"
                            searchable
                        >
                            @foreach($variantgroup as $vg)
                                <flux:select.option 
                                    value="{{ $vg->id }}"
                                >
                                    {{ $vg->name }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                        @if($is_color)
                            <flux:input type="color" wire:model="color" :label="__('ecommerce::variants.form.color')" placeholder="{{ __('ecommerce::variants.form.color_placeholder') }}" />
                        @endif
                    </div>

                    <flux:separator />
                    {{-- Buttons --}}
                    <div class="flex items-center gap-4 justify-end">
                        <flux:button variant="ghost" :href="route(config('ud-ecommerce.admin_route_prefix', 'admin').'.variants.index')" wire:navigate>
                            {{ __('common.cancel') }}
                        </flux:button>
                        <flux:button variant="primary" type="submit">
                            {{ $isEditing ? __('ecommerce::variants.update_variant') : __('ecommerce::variants.create_variant') }}
                        </flux:button>
                    </div>
            </div>
        </flux:card>
    </form>
</div>
