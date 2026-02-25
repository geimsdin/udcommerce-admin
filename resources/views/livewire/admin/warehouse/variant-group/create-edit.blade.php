<div class="max-w-2xl space-y-6">
    {{-- Header --}}
    <div>
        <flux:heading size="xl">
            {{ $isEditing ? __('variantgroups.edit_variantgroup') : __('variantgroups.create_variantgroup') }}
        </flux:heading>
        <flux:subheading>
            {{ $isEditing ? __('variantgroups.messages.edit_subtitle') : __('variantgroups.messages.create_subtitle') }}
        </flux:subheading>
    </div>


    {{-- Card --}}
    <form wire:submit="save" class="space-y-6">
        <flux:card>
            <div class="space-y-6">
                <flux:card>
                    <div class="mb-5">
                        <livewire:lmt-LangSelector wire:model.live="selected_language"/>
                    </div>

                    <livewire:lmt-TextInput
                        label="{{ __('variantgroups.form.name') }}"
                        placeholder="{{ __('variantgroups.form.name_placeholder') }}"
                        wire:model="name"
                        :required="true"
                    />
                    <livewire:lmt-TextInput
                        label="{{ __('variantgroups.form.tooltip') }}"
                        placeholder="{{ __('variantgroups.form.tooltip_placeholder') }}"
                        wire:model="tooltip"
                        :required="true"
                        rows="3"
                    />
                </flux:card>

                <flux:select
                    wire:model="type"
                    :label="__('variantgroups.form.type')"
                    placeholder="{{ __('variantgroups.form.type_placeholder') }}"
                    :required="true"
                    variant="listbox"
                >
                    @foreach($types as $key)
                        <flux:select.option 
                            value="{{ $key }}">
                            {{ $key }}
                        </flux:select.option>
                    @endforeach
                </flux:select>
                <flux:separator />

                    {{-- Buttons --}}
                <div class="flex items-center gap-4 justify-end">
                    <flux:button variant="ghost" :href="route(config('ud-ecommerce.admin_route_prefix', 'admin').'.variantgroups.index')" wire:navigate>
                        {{ __('common.cancel') }}
                    </flux:button>
                    <flux:button variant="primary" type="submit">
                        {{ $isEditing ? __('variantgroups.update_variantgroup') : __('variantgroups.create_variantgroup') }}
                    </flux:button>
                </div>
            </div>
        </flux:card>
    </form>
</div>
