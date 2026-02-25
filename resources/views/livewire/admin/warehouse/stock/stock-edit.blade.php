<div>
    {{-- Header --}}
    <div class="flex items-center justify-between mb-4">
        <div>
            <flux:heading size="xl">{{ $isEditing ? __('ecommerce::stocks.edit_stock') : __('ecommerce::stocks.create_stock') }}</flux:heading>
            <flux:subheading>{{ $isEditing ? __('ecommerce::stocks.messages.edit_subtitle') : __('ecommerce::stocks.messages.create_subtitle') }}</flux:subheading>
        </div>
    </div>

    {{-- Card --}}
    <flux:card>
        <form wire:submit="save">
            <flux:select
                variant="listbox"
                wire:model.live="product_id"
                :label="__('ecommerce::stocks.form.product_id')"
                placeholder="{{ __('ecommerce::stocks.form.product_id_placeholder') }}"
                class="mb-6"
                searchable
            >
                <flux:select.option value="">{{ __('ecommerce::stocks.form.no_product_id_assigned') }}</flux:select.option>
                @foreach($this->getProducts() as $p)
                    <flux:select.option 
                        value="{{ $p->id }}"
                    >
                        {{ $p->getNameCurrentLanguage($selected_language) }}
                    </flux:select.option>
                @endforeach
            </flux:select>
            @if($is_variant)
                <flux:card class="space-y-6 mb-6">
                    <flux:heading size="lg">{{ __('ecommerce::stocks.form.variants') }}</flux:heading>
                    <div class="space-y-6 grid grid-cols-3 gap-4 ">
                        @foreach($this->getCombinationVariantList() as $group_id => $group)
                            <flux:card>
                                <flux:heading size="lg">{{ $group['tooltip'] }}</flux:heading>
                                <flux:separator class="my-4"/>
                                @if ($group['type'] == 'radio')
                                    <flux:radio.group wire:model="variant_id[{{ $group_id }}]" class="grid grid-cols-2 gap-4 space-y-6">
                                        @foreach($group['options'] as $variant_id => $variant)
                                            <flux:radio value="{{ $variant_id }}" label="{{ $variant['name'] }}"/>
                                        @endforeach
                                    </flux:radio.group>
                                @elseif ($group['type'] == 'select')
                                    <flux:select wire:model="variant_id[{{ $group_id }}]" variant="listbox">
                                        @foreach($group['options'] as $variant_id => $variant)
                                            <flux:select.option value="{{ $variant_id }}">{{ $variant['name'] }}</flux:select.option>
                                        @endforeach
                                    </flux:select>
                                @elseif ($group['type'] == 'color')
                                    <div class="flex flex-wrap gap-3">
                                        @foreach($group['options'] as $variant_id => $variant)
                                            <label class="relative cursor-pointer">
                                                <input 
                                                    type="radio" 
                                                    wire:model="variant_id.{{ $group_id }}" 
                                                    value="{{ $variant_id }}"
                                                    class="peer sr-only"
                                                />
                                                <div 
                                                    class="w-10 h-6 border-2 border-gray-300 peer-checked:border-blue-500 peer-checked:ring-2 peer-checked:ring-blue-500 peer-checked:ring-offset-2 transition-all"
                                                    style="background-color: {{ $variant['color'] }}"
                                                    title="{{ $variant['name'] }}"
                                                ></div>
                                            </label>
                                        @endforeach
                                    </div>
                                @endif
                            </flux:card>
                        @endforeach
                    </div>
                </flux:card>
            @endif
            <flux:accordion transition>
                <flux:card>
                    <flux:accordion.item>
                        <flux:accordion.heading>{{ __('ecommerce::stocks.form.details') }}</flux:accordion.heading>
                        <flux:accordion.content class="mt-2">
                            <flux:separator class="mb-4"/>
                            <div class="grid grid-cols-2 gap-4 space-y-6"> 
                                <flux:input wire:model="quantity" :label="__('ecommerce::stocks.form.quantity')" placeholder="{{ __('ecommerce::stocks.form.quantity_placeholder') }}"/>
                                <flux:input wire:model="price" :label="__('ecommerce::stocks.form.price')" placeholder="{{ __('ecommerce::stocks.form.price_placeholder') }}"/>
                                <flux:input wire:model="sku" :label="__('ecommerce::stocks.form.sku')" placeholder="{{ __('ecommerce::stocks.form.sku_placeholder') }}"/>
                                <flux:input wire:model="ean" :label="__('ecommerce::stocks.form.ean')" placeholder="{{ __('ecommerce::stocks.form.ean_placeholder') }}"/>
                                <flux:input wire:model="mpn" :label="__('ecommerce::stocks.form.mpn')" placeholder="{{ __('ecommerce::stocks.form.mpn_placeholder') }}"/>
                                <flux:input wire:model="upc" :label="__('ecommerce::stocks.form.upc')" placeholder="{{ __('ecommerce::stocks.form.upc_placeholder') }}"/>
                                <flux:input wire:model="isbn" :label="__('ecommerce::stocks.form.isbn')" placeholder="{{ __('ecommerce::stocks.form.isbn_placeholder') }}"/>
                                <flux:input wire:model="minimal_quantity" :label="__('ecommerce::stocks.form.minimal_quantity')" placeholder="{{ __('ecommerce::stocks.form.minimal_quantity_placeholder') }}"/>
                                <flux:input wire:model="low_stock_alert" :label="__('ecommerce::stocks.form.low_stock_alert')" placeholder="{{ __('ecommerce::stocks.form.low_stock_alert_placeholder') }}"/>
                                <flux:date-picker wire:model="available_from" :label="__('ecommerce::stocks.form.available_from')" placeholder="{{ __('ecommerce::stocks.form.available_from_placeholder') }}"/>
                                <flux:date-picker wire:model="available_to" :label="__('ecommerce::stocks.form.available_to')" placeholder="{{ __('ecommerce::stocks.form.available_to_placeholder') }}"/>
                            </div>
                        </flux:accordion.content>
                    </flux:accordion.item>
                </flux:card>
            </flux:accordion>
            <br>
            <div class="space-y-6">
                <flux:separator />
                <div class="flex items-center gap-4 justify-end">
                    <flux:button variant="ghost" :href="route(config('ud-ecommerce.admin_route_prefix', 'admin').'.stocks.index')" wire:navigate>
                        {{ __('common.cancel') }}
                    </flux:button>
                    <flux:button variant="primary" type="submit">
                        {{ $isEditing ? __('ecommerce::stocks.update_stock') : __('ecommerce::stocks.create_stock') }}
                    </flux:button>
                </div>
            </div>
        </form>
    </flux:card>
</div>
