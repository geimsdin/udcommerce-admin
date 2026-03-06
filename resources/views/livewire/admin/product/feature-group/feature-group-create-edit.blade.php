<div class="max-w-2xl space-y-6">
    {{-- Header --}}
    <div>
        <flux:heading size="xl">
            {{ $isEditing ? __('ecommerce::feature-groups.edit_feature_group') : __('ecommerce::feature-groups.add_feature_group') }}
        </flux:heading>
        <flux:subheading>
            {{ $isEditing ? __('ecommerce::feature-groups.messages.edit_subtitle') : __('ecommerce::feature-groups.messages.create_subtitle') }}
        </flux:subheading>
    </div>

    {{-- Card --}}
    <flux:card>
        <form wire:submit="save" class="space-y-6">
            {{-- Name (Multi-language) --}}
            <flux:card>
                <div class="mb-4">
                    <livewire:lmt-LangSelector wire:model.live="selected_language"/>
                </div>
                <livewire:lmt-TextInput
                label="{{ __('ecommerce::feature-groups.form.name') }}"
                placeholder="{{ __('ecommerce::feature-groups.form.name_placeholder') }}"
                wire:model="name"
                :required="true"
                />
                <livewire:lmt-Textarea
                    label="{{ __('ecommerce::feature-groups.form.tooltip') }}"
                    placeholder="{{ __('ecommerce::feature-groups.form.tooltip_placeholder') }}"
                    wire:model="tooltip"
                    :required="true"
                />
            </flux:card>

            <flux:separator />

            {{-- Buttons --}}
            <div class="flex items-center gap-4">
                <flux:button variant="primary" type="submit">
                    {{ $isEditing ? __('ecommerce::feature-groups.update_feature_group') : __('ecommerce::feature-groups.create_feature_group') }}
                </flux:button>
                <flux:button variant="ghost" :href="route(config('ud-ecommerce.admin_route_prefix', 'admin').'.feature-groups.index')" wire:navigate>
                    {{ __('common.cancel') }}
                </flux:button>
            </div>
        </form>
    </flux:card>
</div>
