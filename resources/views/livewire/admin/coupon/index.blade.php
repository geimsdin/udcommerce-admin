<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('ecommerce::coupons.title') }}</flux:heading>
            <flux:subheading>{{ __('ecommerce::coupons.subtitle') }}</flux:subheading>
        </div>
        <flux:button variant="primary" icon="plus" :href="route(config('ud-ecommerce.admin_route_prefix', 'admin').'.coupons.create')" wire:navigate>
            {{ __('ecommerce::coupons.add_coupon') }}
        </flux:button>
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
    <flux:card class="space-y-4">
        {{-- Search --}}
        <div class="max-w-sm">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('common.search_placeholder') }}"
                icon="magnifying-glass" />
        </div>
        <flux:table :paginate="$coupons">
            <flux:table.columns>
                <flux:table.column>{{ __('ecommerce::coupons.table.code') }}</flux:table.column>
                <flux:table.column>{{ __('ecommerce::coupons.table.start_date') }}</flux:table.column>
                <flux:table.column>{{ __('ecommerce::coupons.table.end_date') }}</flux:table.column>
                <flux:table.column>{{ __('ecommerce::coupons.table.minimum_spend') }}</flux:table.column>
                <flux:table.column>{{ __('ecommerce::coupons.table.maximum_spend') }}</flux:table.column>
                <flux:table.column>{{ __('ecommerce::coupons.table.active') }}</flux:table.column>
                <flux:table.column class="w-32">{{ __('common.actions') }}</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse ($coupons as $coupon)
                    <flux:table.row>
                        <flux:table.cell>
                            <span class="font-mono font-semibold">{{ $coupon->code }}</span>
                        </flux:table.cell>
                        <flux:table.cell>{{ $coupon->start_date ? $coupon->start_date->format('Y-m-d H:i') : '-' }}</flux:table.cell>
                        <flux:table.cell>{{ $coupon->end_date ? $coupon->end_date->format('Y-m-d H:i') : '-' }}</flux:table.cell>
                        <flux:table.cell>
                            @if($coupon->minimum_spend)
                                {{ number_format($coupon->minimum_spend, 2) }}
                            @else
                                <span class="text-zinc-400">-</span>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            @if($coupon->maximum_spend)
                                {{ number_format($coupon->maximum_spend, 2) }}
                            @else
                                <span class="text-zinc-400">-</span>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge color="{{ $coupon->active ? 'green' : 'red' }}" icon="{{ $coupon->active ? 'check-circle' : 'x-circle' }}">
                                {{ $coupon->active ? __('common.active') : __('common.inactive') }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex items-center gap-1">
                                <flux:button icon="pencil" :href="route(config('ud-ecommerce.admin_route_prefix', 'admin').'.coupons.edit', $coupon)" wire:navigate>
                                    {{ __('common.edit') }}
                                </flux:button>
                                <flux:button variant="danger" icon="trash" wire:click="requestDelete({{ $coupon->id }})">
                                    {{ __('common.delete') }}
                                </flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="7" class="text-center py-8">
                            <div class="flex flex-col items-center gap-2">
                                <flux:text class="text-zinc-500">{{ __('ecommerce::coupons.no_coupons_found') }}</flux:text>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </flux:card>
    <flux:modal wire:model="show_delete_modal" class="max-w-md">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                <flux:icon.exclamation-triangle class="h-6 w-6 text-red-600" />
            </div>
            
            <flux:heading size="lg" class="mb-2">
                {{ __('ecommerce::coupons.delete_confirmation_title') }}
            </flux:heading>
            
            <flux:subheading class="mb-6 text-gray-600">
                {{ __('ecommerce::coupons.delete_confirmation_text') }}
            </flux:subheading>
        </div>

        <div class="flex gap-3 justify-center mt-6">
            <flux:button type="button" wire:click="$set('show_delete_modal', false)">
                {{ __('common.cancel') }}
            </flux:button>

            <flux:button type="button" variant="danger" wire:click="delete">
                {{ __('common.delete') }}
            </flux:button>
        </div>
    </flux:modal>

</div>

