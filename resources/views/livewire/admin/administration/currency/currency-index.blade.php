<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('currencies.title') }}</flux:heading>
            <flux:subheading>{{ __('currencies.subtitle') }}</flux:subheading>
        </div>
        <flux:button variant="primary" icon="plus" :href="route(config('ud-ecommerce.admin_route_prefix', 'admin').'.currencies.create')" wire:navigate>
            {{ __('currencies.add_currency') }}
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
        <flux:table :paginate="$currencies">
            <flux:table.columns>
                <flux:table.column>{{ __('currencies.table.name') }}</flux:table.column>
                <flux:table.column>{{ __('currencies.table.code') }}</flux:table.column>
                <flux:table.column>{{ __('currencies.table.symbol') }}</flux:table.column>
                <flux:table.column>{{ __('currencies.table.exchange_rate') }}</flux:table.column>
                <flux:table.column>{{ __('currencies.table.default') }}</flux:table.column>
                <flux:table.column class="w-32">{{ __('common.actions') }}</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse ($currencies as $currency)
                    <flux:table.row>
                        <flux:table.cell>{{ $currency->name }}</flux:table.cell>
                        <flux:table.cell>{{ $currency->iso_code }}</flux:table.cell>
                        <flux:table.cell>{{ config('currencies.' . $currency->iso_code) }}</flux:table.cell>
                        <flux:table.cell>{{ $currency->exchange_rate }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge color="{{ $currency->default ? 'green' : 'red' }}" icon="{{ $currency->default ? 'check-circle' : 'x-circle' }}">
                                {{ $currency->default ? __('common.yes') : __('common.no') }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex items-center gap-1">
                                <flux:button icon="pencil" :href="route(config('ud-ecommerce.admin_route_prefix', 'admin').'.currencies.edit', $currency)" wire:navigate>
                                    {{ __('common.edit') }}
                                </flux:button>
                                <flux:button variant="danger" icon="trash" wire:click="requestDelete({{ $currency->id }})">
                                    {{ __('common.delete') }}
                                </flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="6" class="text-center py-8">
                            <div class="flex flex-col items-center gap-2">
                                <flux:text class="text-zinc-500">{{ __('currencies.no_currencies_found') }}</flux:text>
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
                {{ __('currencies.delete_confirmation_title') }}
            </flux:heading>
            
            <flux:subheading class="mb-6 text-gray-600">
                {{ __('currencies.delete_confirmation_text') }}
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
