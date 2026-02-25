<div class="space-y-6">
    {{-- Header --}}
    <div>
        <flux:heading size="xl">
            {{ $isEditing ? __('ecommerce::clients.edit_client') : __('ecommerce::clients.add_client') }}
        </flux:heading>
        <flux:subheading>
            {{ $isEditing ? __('ecommerce::clients.messages.edit_subtitle') : __('ecommerce::clients.messages.create_subtitle') }}
        </flux:subheading>
    </div>

    {{-- Card --}}
    <flux:card>
        <form wire:submit="save" class="space-y-6">
            <flux:accordion transition>

                <flux:card class="mb-6">
                    <flux:accordion.item>
                        <flux:accordion.heading>{{ __('ecommerce::clients.form.login_details') }}</flux:accordion.heading>
                        <flux:accordion.content class="mt-2">
                            <flux:separator class="mb-4"/>
                            <flux:input wire:model="email" :label="__('ecommerce::clients.form.email')" placeholder="{{ __('ecommerce::clients.form.email_placeholder') }}" type="email" :badge="__('common.required')"/>
                            <div class="grid grid-cols-2 gap-4 mt-6">
                                <flux:input wire:model="password" :label="__('ecommerce::clients.form.password')" placeholder="{{ __('ecommerce::clients.form.password_placeholder') }}" viewable type="password" :badge="__('common.required')" />
                                <flux:input wire:model="password_confirmation" :label="__('ecommerce::clients.form.password_confirmation')" placeholder="{{ __('ecommerce::clients.form.password_confirmation_placeholder') }}" viewable type="password" />
                            </div>
                        </flux:accordion.content>
                    </flux:accordion.item>
                </flux:card>

                <flux:card class="mb-6">
                    <flux:accordion.item>
                        <flux:accordion.heading>{{ __('ecommerce::clients.form.personal_details') }}</flux:accordion.heading>
                        <flux:accordion.content class="mt-2">
                            <flux:separator class="mb-4"/>
                            <div class="grid grid-cols-2 gap-4">
                                <flux:input wire:model="first_name" :label="__('ecommerce::clients.form.first_name')" placeholder="{{ __('ecommerce::clients.form.first_name_placeholder') }}" 
                                :badge="__('common.required')" />
                                <flux:input wire:model="last_name" :label="__('ecommerce::clients.form.last_name')" placeholder="{{ __('ecommerce::clients.form.last_name_placeholder') }}" />
                                <flux:input wire:model="phone" :label="__('ecommerce::clients.form.phone')" placeholder="{{ __('ecommerce::clients.form.phone_placeholder') }}" />
                                <flux:input wire:model="company_name" :label="__('ecommerce::clients.form.company_name')" placeholder="{{ __('ecommerce::clients.form.company_name_placeholder') }}" />
                                <flux:input wire:model="vat_code" :label="__('ecommerce::clients.form.vat_code')" placeholder="{{ __('ecommerce::clients.form.vat_code_placeholder') }}" />
                                <flux:input wire:model="fiscal_code" :label="__('ecommerce::clients.form.fiscal_code')" placeholder="{{ __('ecommerce::clients.form.fiscal_code_placeholder') }}" />
                                <flux:input wire:model="pec" :label="__('ecommerce::clients.form.pec')" placeholder="{{ __('ecommerce::clients.form.pec_placeholder') }}" />
                                <flux:input wire:model="postcode" :label="__('ecommerce::clients.form.postcode')" placeholder="{{ __('ecommerce::clients.form.postcode_placeholder') }}" />
                                <flux:input wire:model="city" :label="__('ecommerce::clients.form.city')" placeholder="{{ __('ecommerce::clients.form.city_placeholder') }}" />
                                <flux:input wire:model="state" :label="__('ecommerce::clients.form.state')" placeholder="{{ __('ecommerce::clients.form.state_placeholder') }}" />
                                <flux:input wire:model="country" :label="__('ecommerce::clients.form.country')" placeholder="{{ __('ecommerce::clients.form.country_placeholder') }}" />
                            </div>
                        </flux:accordion.content>
                    </flux:accordion.item>
                </flux:card>

                <flux:card class="mb-6">
                    <flux:accordion.item>
                        <flux:accordion.heading>{{ __('ecommerce::clients.form.addresses') }}</flux:accordion.heading>
                        <flux:accordion.content class="mt-2">
                            <flux:separator class="mb-4"/>
                            
                            @if(count($addresses) > 0)
                                <div class="space-y-6">
                                    @foreach($addresses as $index => $address)
                                        <flux:card>
                                            <div class="flex justify-between items-center mb-4">
                                                <h4 class="font-semibold text-sm">
                                                    {{ __('ecommerce::clients.form.address') }} #{{ $index + 1 }}
                                                    @if($address['default'])
                                                        <flux:badge color="green" size="sm" class="ml-2">{{ __('ecommerce::clients.form.default') }}</flux:badge>
                                                    @endif
                                                </h4>
                                                <div class="flex gap-2">
                                                    @if(!$address['default'])
                                                        <flux:button variant="ghost" size="sm" wire:click="setDefaultAddress({{ $index }})" type="button">
                                                            {{ __('ecommerce::clients.form.set_default') }}
                                                        </flux:button>
                                                    @endif
                                                    <flux:button variant="danger" size="sm" wire:click="removeAddress({{ $index }})" type="button" icon="trash">
                                                        {{ __('common.remove') }}
                                                    </flux:button>
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-2 gap-4">
                                                <flux:input 
                                                    wire:model="addresses.{{ $index }}.name" 
                                                    :label="__('ecommerce::clients.form.address_name')" 
                                                    placeholder="{{ __('ecommerce::clients.form.address_name_placeholder') }}" />
                                                
                                                <flux:input 
                                                    wire:model="addresses.{{ $index }}.destination_name" 
                                                    :label="__('ecommerce::clients.form.destination_name')" 
                                                    placeholder="{{ __('ecommerce::clients.form.destination_name_placeholder') }}" />
                                                
                                                <div class="col-span-2">
                                                    <flux:textarea 
                                                        wire:model="addresses.{{ $index }}.address" 
                                                        :label="__('ecommerce::clients.form.address')" 
                                                        placeholder="{{ __('ecommerce::clients.form.address_placeholder') }}" />
                                                </div>
                                                
                                                <flux:input 
                                                    wire:model="addresses.{{ $index }}.post_code" 
                                                    :label="__('ecommerce::clients.form.post_code')" 
                                                    placeholder="{{ __('ecommerce::clients.form.post_code_placeholder') }}" />
                                                
                                                <flux:input 
                                                    wire:model="addresses.{{ $index }}.city" 
                                                    :label="__('ecommerce::clients.form.city')" 
                                                    placeholder="{{ __('ecommerce::clients.form.city_placeholder') }}" />
                                                
                                                <flux:input 
                                                    wire:model="addresses.{{ $index }}.state" 
                                                    :label="__('ecommerce::clients.form.state')" 
                                                    placeholder="{{ __('ecommerce::clients.form.state_placeholder') }}" />
                                                
                                                <flux:input 
                                                    wire:model="addresses.{{ $index }}.country" 
                                                    :label="__('ecommerce::clients.form.country')" 
                                                    placeholder="{{ __('ecommerce::clients.form.country_placeholder') }}" />
                                                
                                                <flux:input 
                                                    wire:model="addresses.{{ $index }}.telephone" 
                                                    :label="__('ecommerce::clients.form.telephone')" 
                                                    placeholder="{{ __('ecommerce::clients.form.telephone_placeholder') }}" />
                                            </div>
                                        </flux:card>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                                    {{ __('ecommerce::clients.form.no_addresses') }}
                                </p>
                            @endif

                            <flux:button variant="primary" wire:click="addAddress" type="button" icon="plus" class="mt-4">
                                {{ __('ecommerce::clients.form.add_address') }}
                            </flux:button>
                        </flux:accordion.content>
                    </flux:accordion.item>
                </flux:card>
            </flux:accordion>

            <flux:select variant="listbox" wire:model="client_groups" :label="__('ecommerce::clients.form.client_groups')" placeholder="{{ __('ecommerce::clients.form.client_groups_placeholder') }}" searchable clearable multiple>
                @foreach ($this->clientGroups as $clientGroup)
                    <flux:select.option :value="$clientGroup->id">{{ $clientGroup->name }}</flux:select.option>
                @endforeach
            </flux:select>

            {{-- Buttons --}}
            <div class="flex items-center gap-4">
                <flux:button variant="primary" type="submit">
                    {{ $isEditing ? __('ecommerce::clients.update_client') : __('ecommerce::clients.create_client') }}
                </flux:button>
                <flux:button variant="ghost" :href="route(config('ud-ecommerce.admin_route_prefix', 'admin').'.clients.index')" wire:navigate>
                    {{ __('common.cancel') }}
                </flux:button>
            </div>
        </form>
    </flux:card>
</div>