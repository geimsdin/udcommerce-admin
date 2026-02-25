<div class="space-y-6">
    {{-- Header --}}
    <div>
        <flux:heading size="xl">
            {{ $isEditing ? __('ecommerce::order_statuses.edit_order_status') : __('ecommerce::order_statuses.add_order_status') }}
        </flux:heading>
        <flux:subheading>
            {{ $isEditing ? __('ecommerce::order_statuses.messages.edit_subtitle') : __('ecommerce::order_statuses.messages.create_subtitle') }}
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
                    label="{{ __('ecommerce::order_statuses.form.name') }}"
                    placeholder="{{ __('ecommerce::order_statuses.form.name_placeholder') }}"
                    wire:model="name"
                    :required="true"
                />
                @error('name')
                    <flux:error>{{ $message }}</flux:error>
                @enderror
            </flux:card>

            {{-- Configuration --}}
            <flux:separator />
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <flux:input 
                        wire:model="color" 
                        type="color"
                        :label="__('ecommerce::order_statuses.form.color')" 
                        placeholder="{{ __('ecommerce::order_statuses.form.color_placeholder') }}"
                    />
                    @error('color')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror
                </div>

                <div>
                    <flux:input 
                        wire:model="icon" 
                        :label="__('ecommerce::order_statuses.form.icon')" 
                        placeholder="{{ __('ecommerce::order_statuses.form.icon_placeholder') }}"
                    />
                    <flux:subheading class="mt-1">
                        {{ __('ecommerce::order_statuses.form.icon_help') }}
                    </flux:subheading>
                    @error('icon')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror
                </div>
            </div>

            {{-- Email Settings --}}
            <flux:separator />
            
            <flux:heading size="lg">{{ __('ecommerce::order_statuses.form.email_settings') }}</flux:heading>

            <flux:switch 
                wire:model="sends_email" 
                :label="__('ecommerce::order_statuses.form.sends_email')" 
            />

            <div x-show="$wire.sends_email" x-transition>
                <flux:input 
                    wire:model="email_template" 
                    :label="__('ecommerce::order_statuses.form.email_template')" 
                    placeholder="{{ __('ecommerce::order_statuses.form.email_template_placeholder') }}"
                />
                @error('email_template')
                    <flux:error>{{ $message }}</flux:error>
                @enderror
            </div>

            <flux:separator />

            {{-- Buttons --}}
            <div class="flex items-center gap-4">
                <flux:button variant="primary" type="submit">
                    {{ $isEditing ? __('ecommerce::order_statuses.update_order_status') : __('ecommerce::order_statuses.create_order_status') }}
                </flux:button>
                <flux:button variant="ghost" :href="route(config('ud-ecommerce.admin_route_prefix', 'admin').'.order-statuses.index')" wire:navigate>
                    {{ __('common.cancel') }}
                </flux:button>
            </div>
        </form>
    </flux:card>
</div>

