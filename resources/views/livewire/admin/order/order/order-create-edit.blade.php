<div class="space-y-6">
    <div>
        <flux:heading size="xl">
            {{ $isEditing ? __('ecommerce::orders.edit_order') : __('ecommerce::orders.add_order') }}
        </flux:heading>
        <flux:subheading>
            {{ $isEditing ? __('ecommerce::orders.messages.edit_subtitle') : __('ecommerce::orders.messages.create_subtitle') }}
        </flux:subheading>
    </div>
    @if (session('status'))
        <div x-data="{ show: true }" 
            x-init="setTimeout(() => show = false, 3000)" 
            x-show="show"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">
            <flux:callout variant="success" icon="check-circle">
                {{ session('status') }}
            </flux:callout>
        </div>
    @endif
    @if (session('error'))
        <div x-data="{ show: true }" 
            x-init="setTimeout(() => show = false, 3000)" 
            x-show="show"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">
            <flux:callout variant="danger" icon="x-circle">
                {{ session('error') }}
            </flux:callout>
        </div>
    @endif

    <form wire:submit="save">
        <flux:tab.group>
            <flux:tabs wire:model="tab">
                <flux:tab name="general">{{__('ecommerce::orders.form.general')}}</flux:tab>
                <flux:tab name="products">{{__('ecommerce::orders.form.products')}}</flux:tab>
            </flux:tabs>

            <flux:tab.panel name="general">
                <div class="flex gap-6">
                    <flux:card class="w-2/3 space-y-6">
                        <flux:select wire:model.live="client_id" :label="__('ecommerce::orders.form.client')" :badge="__('common.required')"
                            placeholder="{{ __('ecommerce::orders.form.client_placeholder') }}" variant="listbox" clearable searchable>
                            @foreach ($clients as $client)
                                <flux:select.option value="{{ $client->id }}">{{ $client->user->name }}
                                    ({{ $client->user->email }})</flux:select.option>
                            @endforeach
                        </flux:select>

                        <flux:input wire:model="reference" :label="__('ecommerce::orders.form.reference')"
                            placeholder="{{ __('ecommerce::orders.form.reference_placeholder') }}" :badge="__('common.required')" />
                        <flux:accordion transition>
                            <flux:card class="mb-6">
                                <flux:accordion.item>
                                    <flux:accordion.heading>{{ __('ecommerce::orders.form.shipping_information') }}</flux:accordion.heading>
                                    <flux:accordion.content class="mt-2 space-y-6">
                                        <flux:separator class="mb-4" />
                                        <flux:select wire:model="carrier_id" :label="__('ecommerce::orders.form.carrier')"
                                            placeholder="{{ __('ecommerce::orders.form.carrier_placeholder') }}" variant="listbox" clearable>
                                            @foreach ($carriers as $carrier)
                                                <flux:select.option value="{{ $carrier->id }}">{{ $carrier->name }}
                                                </flux:select.option>
                                            @endforeach
                                        </flux:select>
                                        @if ($client_id)
                                            <div>
                                                <div class="flex items-end gap-2">
                                                    <div class="flex-1">
                                                        <flux:select wire:model="address_id" :label="__('ecommerce::orders.form.address')"
                                                            placeholder="{{ __('ecommerce::orders.form.address_placeholder') }}"
                                                            variant="listbox" clearable>
                                                            @foreach ($addresses as $address)
                                                                <flux:select.option value="{{ $address->id }}">
                                                                    {{ $address->address }}, {{ $address->city }},
                                                                    {{ $address->post_code }}
                                                                </flux:select.option>
                                                            @endforeach
                                                        </flux:select>
                                                    </div>
                                                    <flux:button type="button" variant="ghost" icon="plus"
                                                        wire:click="openAddressModal" class="mb-0">
                                                        {{ __('ecommerce::orders.add_address') }}
                                                    </flux:button>
                                                </div>
                                            </div>
                                        @else
                                            <div>
                                                <flux:label>{{ __('ecommerce::orders.form.address') }}</flux:label>
                                                <flux:subheading>{{ __('ecommerce::orders.form.select_client_first') }}</flux:subheading>
                                            </div>
                                        @endif
                                    </flux:accordion.content>
                                </flux:accordion.item>
                            </flux:card>
                            <flux:card class="mb-6">
                                <flux:accordion.item>
                                    <flux:accordion.heading>{{ __('ecommerce::orders.form.payment_information') }}</flux:accordion.heading>
                                    <flux:accordion.content class="mt-2 space-y-6">
                                        <flux:separator class="mb-4" />
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <flux:select wire:model="discount_type" :label="__('ecommerce::orders.form.discount_type')"
                                                variant="listbox" clearable>
                                                <flux:select.option value="percent">{{ __('ecommerce::orders.form.percent') }}
                                                </flux:select.option>
                                                <flux:select.option value="amount">{{ __('ecommerce::orders.form.amount') }}
                                                </flux:select.option>
                                            </flux:select>

                                            <flux:input wire:model="discount" type="number" step="0.01"
                                                :label="__('ecommerce::orders.form.discount_value')"
                                                placeholder="{{ __('ecommerce::orders.form.discount_value_placeholder') }}" />
                                        </div>
                                        <flux:input wire:model="payment_method" :label="__('ecommerce::orders.form.payment_method')"
                                            placeholder="{{ __('ecommerce::orders.form.payment_method_placeholder') }}" />

                                        <flux:textarea wire:model="payment_info" :label="__('ecommerce::orders.form.payment_info')"
                                            placeholder="{{ __('ecommerce::orders.form.payment_info_placeholder') }}" rows="3" />
                                    </flux:accordion.content>
                                </flux:accordion.item>
                            </flux:card>
                            <flux:card class="mb-6">
                                <flux:accordion.item>
                                    <flux:accordion.heading>{{ __('ecommerce::orders.form.return_information') }}</flux:accordion.heading>
                                    <flux:accordion.content class="mt-2 space-y-6">
                                        <flux:separator class="mb-4" />
                                        <flux:switch wire:model.live="returned" :label="__('ecommerce::orders.form.returned')" />
                                        @if ($returned)
                                            <flux:textarea wire:model="return_note" :label="__('ecommerce::orders.form.return_note')"
                                                placeholder="{{ __('ecommerce::orders.form.return_note_placeholder') }}" rows="3" />
                                            <flux:input wire:model="return_amount" type="number" :label="__('ecommerce::orders.form.return_amount')"
                                                placeholder="{{ __('ecommerce::orders.form.return_amount_placeholder') }}" />
                                        @endif
                                    </flux:accordion.content>
                                </flux:accordion.item>
                            </flux:card>
                        </flux:accordion>

                        <flux:separator />
                        <flux:select wire:model.live="coupon_id" :label="__('ecommerce::orders.form.coupon')"
                            placeholder="{{ __('ecommerce::orders.form.coupon_placeholder') }}" variant="listbox" clearable searchable>
                            @foreach ($coupons as $coupon)
                                <flux:select.option value="{{ $coupon->id }}">{{ $coupon->code ? $coupon->code : '#' . $coupon->id }}</flux:select.option>
                            @endforeach
                        </flux:select>

                        <flux:textarea wire:model="note" :label="__('ecommerce::orders.form.note')"
                            placeholder="{{ __('ecommerce::orders.form.note_placeholder') }}" rows="4" />
                    </flux:card>
                    <flux:card class="w-1/3 space-y-6">
                        <flux:heading size="lg">{{ __('ecommerce::orders.form.information') }}</flux:heading>
                        <flux:separator />
                        <flux:select wire:model="last_status_id" :label="__('ecommerce::orders.form.status')" :badge="__('common.required')"
                            placeholder="{{ __('ecommerce::orders.form.status_placeholder') }}" variant="listbox" clearable>
                            @foreach ($orderStatuses as $status)
                                <flux:select.option value="{{ $status->id }}">{{ $status->name }}</flux:select.option>
                            @endforeach
                        </flux:select>

                        <flux:select wire:model="currency_id" :label="__('ecommerce::orders.form.currency')" :badge="__('common.required')"
                            placeholder="{{ __('ecommerce::orders.form.currency_placeholder') }}" variant="listbox" clearable>
                            @foreach ($currencies as $currency)
                                <flux:select.option value="{{ $currency->id }}">{{ $currency->name }} (
                                    {{ config('currencies.' . $currency->iso_code) }} )</flux:select.option>
                            @endforeach
                        </flux:select>


                        <flux:select wire:model="season_id" :label="__('ecommerce::orders.form.season')"
                            placeholder="{{ __('ecommerce::orders.form.season_placeholder') }}" variant="listbox" clearable>
                            @foreach ($seasons as $season)
                                <flux:select.option value="{{ $season->id }}">{{ $season->name }}</flux:select.option>
                            @endforeach
                        </flux:select>

                        @if ($isEditing && $orderStatusHistory->count() > 0)
                            <div>
                                <div class="mb-2">
                                    <flux:label>{{ __('ecommerce::orders.form.status_history') }}</flux:label>
                                </div>
                                <flux:card class="space-y-2 max-h-48 overflow-y-auto p-3">
                                    @foreach ($orderStatusHistory as $statusHistory)
                                        <div class="flex items-center justify-between py-2 px-3 rounded-md">
                                            <div class="flex items-center gap-2">
                                                @if ($statusHistory->orderStatus?->color)
                                                    <div class="w-3 h-3 rounded-full flex-shrink-0" style="background-color: {{ $statusHistory->orderStatus->color }}"></div>
                                                @endif
                                                <span class="text-sm font-medium">{{ $statusHistory->orderStatus?->name ?? __('ecommerce::orders.unknown_status') }}</span>
                                            </div>
                                            <span class="text-xs text-zinc-500 dark:text-zinc-400">
                                                {{ $statusHistory->created_at->format('Y-m-d H:i') }}
                                            </span>
                                        </div>
                                    @endforeach
                                </flux:card>
                            </div>
                        @endif
                    </flux:card>
                </div>
            </flux:tab.panel>
            
            <flux:tab.panel name="products">
                <flux:card class="space-y-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <flux:heading size="lg">{{ __('ecommerce::orders.form.order_items') }}</flux:heading>
                            <flux:subheading>{{ __('ecommerce::orders.form.order_items_subtitle') }}</flux:subheading>
                        </div>
                        <flux:button type="button" variant="primary" icon="plus" wire:click="openDetailModal">
                            {{ __('ecommerce::orders.form.add_item') }}
                        </flux:button>
                    </div>

                    @if (count($orderDetails) > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-zinc-200 dark:border-zinc-700">
                                        <th class="text-left py-3 px-4 text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ __('ecommerce::orders.form.product') }}</th>
                                        <th class="text-left py-3 px-4 text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ __('ecommerce::orders.form.variation') }}</th>
                                        <th class="text-center py-3 px-4 text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ __('ecommerce::orders.form.quantity') }}</th>
                                        <th class="text-right py-3 px-4 text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ __('ecommerce::orders.form.price') }}</th>
                                        <th class="text-right py-3 px-4 text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ __('ecommerce::orders.form.discount') }}</th>
                                        <th class="text-right py-3 px-4 text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ __('ecommerce::orders.form.subtotal') }}</th>
                                        <th class="text-center py-3 px-4 text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ __('common.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($orderDetails as $index => $detail)
                                        <tr class="border-b border-zinc-100 dark:border-gray-600">
                                            <td class="py-3 px-4">
                                                <div class="text-sm font-medium">{{ $detail['product_name'] }}</div>
                                                @if ($detail['returned'])
                                                    <flux:badge color="red" size="sm">{{ __('ecommerce::orders.returned') }}</flux:badge>
                                                @endif
                                            </td>
                                            <td class="py-3 px-4 text-left text-sm">{{ $detail['variation'] }}</td>
                                            <td class="py-3 px-4 text-center text-sm">{{ $detail['quantity'] }}</td>
                                            <td class="py-3 px-4 text-right text-sm">{{ number_format($detail['price'], 2) }}</td>
                                            <td class="py-3 px-4 text-right text-sm">
                                                @if ($detail['discount'] > 0)
                                                    {{ $detail['discount_type'] === 'percent' ? $detail['discount'] . '%' : number_format($detail['discount'], 2) }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="py-3 px-4 text-right text-sm font-medium">
                                                {{ number_format($this->calculateDetailTotal($detail), 2) }}
                                            </td>
                                            <td class="py-3 px-4">
                                                <div class="flex items-center justify-center gap-2">
                                                    <flux:button type="button" size="sm" variant="ghost" icon="pencil" 
                                                        wire:click="editDetail({{ $index }})" />
                                                    <flux:button type="button" size="sm" variant="ghost" icon="trash" 
                                                        wire:click="requestDeleteDetail({{ $index }})"/>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="border-t border-zinc-200 dark:border-zinc-700">
                                        <td colspan="5" class="py-3 px-4 text-right text-sm font-medium">{{ __('ecommerce::orders.form.subtotal') }}:</td>
                                        <td class="py-3 px-4 text-right text-sm font-semibold">
                                            {{ number_format($this->calculateSubtotal(), 2) }}
                                        </td>
                                        <td></td>
                                    </tr>
                                    @if ($this->calculateSubtotal() != $this->calculateTotalAfterItemDiscounts())
                                        <tr class="border-t border-zinc-200 dark:border-zinc-700">
                                            <td colspan="5" class="py-3 px-4 text-right text-sm font-medium">{{ __('ecommerce::orders.form.item_discounts') }}:</td>
                                            <td class="py-3 px-4 text-right text-sm font-semibold text-green-600">
                                                -{{ number_format($this->calculateSubtotal() - $this->calculateTotalAfterItemDiscounts(), 2) }}
                                            </td>
                                            <td></td>
                                        </tr>
                                    @endif
                                    @if ($this->calculateOrderDiscount() > 0)
                                        <tr class="border-t border-zinc-200 dark:border-zinc-700">
                                            <td colspan="5" class="py-3 px-4 text-right text-sm font-medium">
                                                {{ __('ecommerce::orders.form.order_discount') }} 
                                                ({{ $discount_type === 'percent' ? $discount . '%' : number_format($discount, 2) }}):
                                            </td>
                                            <td class="py-3 px-4 text-right text-sm font-semibold text-green-600">
                                                -{{ number_format($this->calculateOrderDiscount(), 2) }}
                                            </td>
                                            <td></td>
                                        </tr>
                                    @endif
                                    @if ($this->calculateCouponDiscount() > 0)
                                        <tr class="border-t border-zinc-200 dark:border-zinc-700">
                                            <td colspan="5" class="py-3 px-4 text-right text-sm font-medium">
                                                <div class="flex items-center justify-end gap-2">
                                                    {{ __('ecommerce::orders.form.coupon_discount') }}:
                                                </div>
                                            </td>
                                            <td class="py-3 px-4 text-right text-sm font-semibold text-blue-600">
                                                -{{ number_format($this->calculateCouponDiscount(), 2) }}
                                            </td>
                                            <td></td>
                                        </tr>
                                    @endif
                                    <tr class="border-t-2 border-zinc-300 dark:border-zinc-600">
                                        <td colspan="5" class="py-4 px-4 text-right font-semibold text-base">{{ __('ecommerce::orders.form.total') }}:</td>
                                        <td class="py-4 px-4 text-right font-bold text-lg">
                                            {{ number_format($this->calculateOrderTotal(), 2) }}
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <flux:icon.shopping-cart class="mx-auto h-12 w-12 text-zinc-400" />
                            <flux:heading size="lg" class="mt-4">{{ __('ecommerce::orders.form.no_items') }}</flux:heading>
                        </div>
                    @endif
                </flux:card>
            </flux:tab.panel>
        </flux:tab.group>
        <flux:card class="mt-4">
            <div class="flex items-center gap-4 justify-end">
                <flux:button variant="ghost" :href="route(config('ud-ecommerce.admin_route_prefix', 'admin').'.orders.index')" wire:navigate>
                    {{ __('common.cancel') }}
                </flux:button>
                <flux:button variant="primary" type="submit">
                    {{ $isEditing ? __('ecommerce::orders.update_order') : __('ecommerce::orders.create_order') }}
                </flux:button>
            </div>
        </flux:card>
    </form>

    {{-- Add Address Modal --}}
    <flux:modal wire:model="show_address_modal" class="max-w-2xl">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('ecommerce::orders.add_address') }}</flux:heading>
                <flux:subheading>{{ __('ecommerce::orders.add_address_subtitle') }}</flux:subheading>
            </div>

            <form wire:submit="saveAddress" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:input wire:model="address_name" :label="__('ecommerce::orders.form.address_name')"
                        placeholder="{{ __('ecommerce::orders.form.address_name_placeholder') }}" />

                    <flux:input wire:model="address_destination_name" :label="__('ecommerce::orders.form.destination_name')"
                        placeholder="{{ __('ecommerce::orders.form.destination_name_placeholder') }}" />
                </div>

                <flux:textarea wire:model="address_address" :label="__('ecommerce::orders.form.address')"
                    :badge="__('common.required')" placeholder="{{ __('ecommerce::orders.form.address_placeholder') }}"
                    rows="2" />

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <flux:input wire:model="address_post_code" :label="__('ecommerce::orders.form.post_code')"
                        placeholder="{{ __('ecommerce::orders.form.post_code_placeholder') }}" />

                    <flux:input wire:model="address_city" :label="__('ecommerce::orders.form.city')"
                        placeholder="{{ __('ecommerce::orders.form.city_placeholder') }}" />

                    <flux:input wire:model="address_state" :label="__('ecommerce::orders.form.state')"
                        placeholder="{{ __('ecommerce::orders.form.state_placeholder') }}" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:input wire:model="address_country" :label="__('ecommerce::orders.form.country')"
                        placeholder="{{ __('ecommerce::orders.form.country_placeholder') }}" />

                    <flux:input wire:model="address_telephone" :label="__('ecommerce::orders.form.telephone')"
                        placeholder="{{ __('ecommerce::orders.form.telephone_placeholder') }}" />
                </div>

                <flux:switch wire:model="address_default" :label="__('ecommerce::orders.form.set_as_default')" />

                <div class="flex items-center gap-4 justify-end pt-4 border-t">
                    <flux:button type="button" variant="ghost" wire:click="closeAddressModal">
                        {{ __('common.cancel') }}
                    </flux:button>
                    <flux:button type="submit" variant="primary">
                        {{ __('ecommerce::orders.create_address') }}
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    {{-- Add/Edit Order Detail Modal --}}
    <flux:modal wire:model="show_detail_modal" class="w-3xl max-w-full">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">
                    {{ $editing_detail_index !== null ? __('ecommerce::orders.form.update_item') : __('ecommerce::orders.form.add_item') }}
                </flux:heading>
            </div>

            <form wire:submit="saveDetail" class="space-y-6">
                <flux:select wire:model.live="detail_product_id" :label="__('ecommerce::orders.form.product')" 
                    :badge="__('common.required')" placeholder="{{ __('ecommerce::orders.form.product_placeholder') }}" 
                    variant="listbox" searchable clearable>
                    @foreach ($products as $product)
                        <flux:select.option value="{{ $product->id }}">
                            {{ $product->currentLanguage->name ?? $product->id }}
                        </flux:select.option>
                    @endforeach
                </flux:select>

                @if ($is_detail_product_variant)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:select wire:model.live="variation_id" :label="__('ecommerce::orders.form.variation')"
                            placeholder="{{ __('ecommerce::orders.form.variation_placeholder') }}" variant="listbox" clearable>
                            @foreach ($variations as $variation)
                                <flux:select.option value="{{ $variation->id }}">
                                    {{ $variation->combination_name ?? $variation->id }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>
                @endif
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:input wire:model="detail_quantity" type="number" min="1" :label="__('ecommerce::orders.form.quantity')"
                        :badge="__('common.required')" placeholder="{{ __('ecommerce::orders.form.quantity_placeholder') }}" />

                    <flux:input wire:model="detail_price" type="number" step="0.01" :label="__('ecommerce::orders.form.price')"
                        :badge="__('common.required')" placeholder="{{ __('ecommerce::orders.form.price_placeholder') }}" />
                </div>

                <flux:separator />
                
                <flux:heading size="sm">{{ __('ecommerce::orders.form.discount_settings') }}</flux:heading>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:select wire:model="detail_discount_type" :label="__('ecommerce::orders.form.discount_type')"
                        variant="listbox">
                        <flux:select.option value="percent">{{ __('ecommerce::orders.form.percent') }}</flux:select.option>
                        <flux:select.option value="amount">{{ __('ecommerce::orders.form.amount') }}</flux:select.option>
                    </flux:select>

                    <flux:input wire:model="detail_discount" type="number" step="0.01" min="0"
                        :label="__('ecommerce::orders.form.discount_value')"
                        placeholder="{{ __('ecommerce::orders.form.discount_value_placeholder') }}" />
                </div>

                <flux:separator />
                
                <flux:switch wire:model.live="detail_returned" :label="__('ecommerce::orders.form.item_returned')" />

                @if ($detail_returned)
                    <flux:textarea wire:model="detail_return_note" :label="__('ecommerce::orders.form.return_note')"
                        placeholder="{{ __('ecommerce::orders.form.return_note_placeholder') }}" rows="3" />

                    <flux:input wire:model="detail_return_amount" type="number" step="0.01"
                        :label="__('ecommerce::orders.form.return_amount')"
                        placeholder="{{ __('ecommerce::orders.form.return_amount_placeholder') }}" />
                @endif

                <flux:separator/>

                <div class="flex items-center gap-4 justify-end">
                    <flux:button type="button" variant="ghost" wire:click="closeDetailModal">
                        {{ __('common.cancel') }}
                    </flux:button>
                    <flux:button type="submit" variant="primary">
                        {{ $editing_detail_index !== null ? __('ecommerce::orders.form.update_item') : __('ecommerce::orders.form.add_item') }}
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    {{-- Delete Order Detail Modal --}}
    <flux:modal wire:model="show_delete_detail_modal" class="max-w-md">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                <flux:icon.exclamation-triangle class="h-6 w-6 text-red-600" />
            </div>
            
            <flux:heading size="lg" class="mb-2">
                {{ __('ecommerce::orders.delete_confirmation_title') }}
            </flux:heading>
            
            <flux:subheading class="mb-6 text-gray-600">
                {{ __('ecommerce::orders.delete_confirmation_text') }}
            </flux:subheading>
        </div>

        <div class="flex gap-3 justify-center mt-6">
            <flux:button type="button" wire:click="closeDeleteDetailModal">
                {{ __('common.cancel') }}
            </flux:button>

            <flux:button type="button" variant="danger" wire:click="deleteDetail">
                {{ __('common.delete') }}
            </flux:button>
        </div>
    </flux:modal>
</div>