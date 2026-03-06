<div class="max-w-2xl space-y-6">
    {{-- Header --}}
    <div>
        <flux:heading size="xl">
            {{ $isEditing ? __('ecommerce::features.edit_feature') : __('ecommerce::features.add_feature') }}
        </flux:heading>
        <flux:subheading>
            {{ $isEditing ? __('ecommerce::features.messages.edit_subtitle') : __('ecommerce::features.messages.create_subtitle') }}
        </flux:subheading>
    </div>

    {{-- Language Selector --}}
    
    {{-- Card --}}
    <flux:card>
        <form wire:submit="save" class="space-y-6">
            {{-- Name (Multi-language) --}}
            <flux:card>
                <div class="mb-5">
                    <livewire:lmt-LangSelector wire:model.live="selected_language"/>
                </div>
                <livewire:lmt-TextInput
                label="{{ __('ecommerce::features.form.name') }}"
                placeholder="{{ __('ecommerce::features.form.name_placeholder') }}"
                wire:model="name"
                :required="true"
                />
            </flux:card>
            <flux:select variant="listbox" searchable :label="__('ecommerce::features.form.feature_group')" placeholder="{{ __('ecommerce::features.form.feature_group_placeholder') }}" wire:model="feature_group_id" clearable>
                @foreach($this->featureGroups as $feature_group)
                    <flux:select.option :value="$feature_group->id">{{ $feature_group->name }}</flux:select.option>
                @endforeach
            </flux:select>
            <flux:separator />

            {{-- Buttons --}}
            <div class="flex items-center gap-4">
                <flux:button variant="primary" type="submit">
                    {{ $isEditing ? __('ecommerce::features.update_feature') : __('ecommerce::features.create_feature') }}
                </flux:button>
                <flux:button variant="ghost" :href="route(config('ud-ecommerce.admin_route_prefix', 'admin').'.features.index')" wire:navigate>
                    {{ __('common.cancel') }}
                </flux:button>
            </div>
        </form>
    </flux:card>
</div>
