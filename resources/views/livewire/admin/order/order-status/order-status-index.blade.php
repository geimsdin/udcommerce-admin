<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('ecommerce::order_statuses.title') }}</flux:heading>
            <flux:subheading>{{ __('ecommerce::order_statuses.subtitle') }}</flux:subheading>
        </div>
        <flux:button variant="primary" icon="plus" :href="route(config('ud-ecommerce.admin_route_prefix', 'admin').'.order-statuses.create')" wire:navigate>
            {{ __('ecommerce::order_statuses.add_order_status') }}
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
        
        <flux:table :paginate="$orderStatuses">
            <flux:table.columns>
                <flux:table.column>{{ __('ecommerce::order_statuses.table.name') }}</flux:table.column>
                <flux:table.column>{{ __('ecommerce::order_statuses.table.color') }}</flux:table.column>
                <flux:table.column class="w-32">{{ __('common.actions') }}</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse ($orderStatuses as $orderStatus)
                    <flux:table.row>
                        <flux:table.cell>
                            <div class="font-medium">
                                {{ $orderStatus->name ?? __('ecommerce::order_statuses.no_name') }}
                            </div>
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded" style="background-color: {{ $orderStatus->color }}"></div>
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ $orderStatus->color }}</span>
                            </div>
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex items-center gap-1">
                                <flux:button icon="pencil" :href="route(config('ud-ecommerce.admin_route_prefix', 'admin').'.order-statuses.edit', $orderStatus)" wire:navigate>
                                    {{ __('common.edit') }}
                                </flux:button>
                                @if (!isset($orderStatus->is_native) || !$orderStatus->is_native)
                                    <flux:button variant="danger" icon="trash" wire:click="requestDelete({{ $orderStatus->id }})">
                                        {{ __('common.delete') }}
                                    </flux:button>
                                @endif
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="6" class="text-center py-8">
                            <div class="flex flex-col items-center gap-2">
                                <flux:text class="text-zinc-500">{{ __('ecommerce::order_statuses.no_order_statuses_found') }}</flux:text>
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
                {{ __('ecommerce::order_statuses.delete_confirmation_title') }}
            </flux:heading>
            
            <flux:subheading class="mb-6 text-gray-600">
                {{ __('ecommerce::order_statuses.delete_confirmation_text') }}
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

