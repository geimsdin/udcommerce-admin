<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('ecommerce::orders.title') }}</flux:heading>
            <flux:subheading>{{ __('ecommerce::orders.subtitle') }}</flux:subheading>
        </div>
        <flux:button variant="primary" icon="plus" :href="route(config('ud-ecommerce.admin_route_prefix', 'admin').'.orders.create')" wire:navigate>
            {{ __('ecommerce::orders.add_order') }}
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
        {{-- Search and Filter Toggle --}}
        <div class="flex items-center gap-4">
            <div class="flex-1 max-w-sm">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('common.search_placeholder') }}"
                    icon="magnifying-glass" />
            </div>
            <flux:button 
                variant="ghost" 
                icon="{{ $show_filters ? 'x-mark' : 'funnel' }}"
                wire:click="toggleFilters"
            >
                {{ $show_filters ? __('ecommerce::orders.filter.hide_filters') : __('ecommerce::orders.filter.show_filters') }}
            </flux:button>
        </div>

        {{-- Filters Panel --}}
        @if ($show_filters)
            <flux:card class="pt-4 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    {{-- Client Filter --}}
                    <flux:select 
                        wire:model="client_id" 
                        :label="__('ecommerce::orders.filter.client')" 
                        placeholder="{{ __('ecommerce::orders.filter.client_placeholder') }}"
                        variant="listbox" 
                        clearable
                        searchable
                    >
                        @foreach ($clients as $client)
                            <flux:select.option value="{{ $client->id }}">
                                {{ $client->user->name }} ({{ $client->user->email }})
                            </flux:select.option>
                        @endforeach
                    </flux:select>

                    {{-- Status Filter --}}
                    <flux:select 
                        wire:model="status_id" 
                        :label="__('ecommerce::orders.filter.status')" 
                        placeholder="{{ __('ecommerce::orders.filter.status_placeholder') }}"
                        variant="listbox" 
                        clearable
                    >
                        @foreach ($orderStatuses as $status)
                            <flux:select.option value="{{ $status->id }}">
                                <div class="flex items-center gap-2">
                                    @if ($status->color)
                                        <div class="w-3 h-3 rounded-full" style="background-color: {{ $status->color }}"></div>
                                    @endif
                                    <span>{{ $status->name }}</span>
                                </div>
                            </flux:select.option>
                        @endforeach
                    </flux:select>

                    {{-- Date Range --}}
                    <div class="grid grid-cols-2 gap-2">
                        <flux:date-picker 
                            wire:model="date_start" 
                            type="date"
                            :label="__('ecommerce::orders.filter.date_start')" 
                            placeholder="{{ __('ecommerce::orders.filter.date_start_placeholder') }}"
                        />
                        <flux:date-picker 
                            wire:model="date_end" 
                            type="date"
                            :label="__('ecommerce::orders.filter.date_end')" 
                            placeholder="{{ __('ecommerce::orders.filter.date_end_placeholder') }}"
                        />
                    </div>
                </div>

                {{-- Filter Actions --}}
                <div class="flex items-center gap-2 justify-end">
                    @if ($applied_client_id || $applied_status_id || $applied_date_start || $applied_date_end)
                        <flux:button variant="ghost" wire:click="resetFilters" icon="x-mark">
                            {{ __('ecommerce::orders.filter.reset_filters') }}
                        </flux:button>
                    @endif
                    <flux:button variant="primary" wire:click="applyFilters" icon="funnel">
                        {{ __('ecommerce::orders.filter.apply_filters') }}
                    </flux:button>
                </div>

                {{-- Active Filters Display --}}
                @if ($applied_client_id || $applied_status_id || $applied_date_start || $applied_date_end)
                    <div class="flex flex-wrap items-center gap-2 pt-2 border-t border-zinc-200 dark:border-zinc-700">
                        <span class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('ecommerce::orders.filter.active_filters') }}:</span>
                        @if ($applied_client_id)
                            @php
                                $client = $clients->firstWhere('id', $applied_client_id);
                            @endphp
                            <flux:badge color="blue" size="sm" class="flex items-center gap-1">
                                <span>{{ __('ecommerce::orders.filter.client') }}: {{ $client?->user->name ?? '-' }}</span>
                                <button type="button" wire:click="$set('client_id', null); $set('applied_client_id', null); resetPage()" class="ml-1 hover:text-red-500">×</button>
                            </flux:badge>
                        @endif
                        @if ($applied_status_id)
                            @php
                                $status = $orderStatuses->firstWhere('id', $applied_status_id);
                            @endphp
                            <flux:badge color="blue" size="sm" class="flex items-center gap-1">
                                <span>{{ __('ecommerce::orders.filter.status') }}: {{ $status?->name ?? '-' }}</span>
                                <button type="button" wire:click="$set('status_id', null); $set('applied_status_id', null); resetPage()" class="ml-1 hover:text-red-500">×</button>
                            </flux:badge>
                        @endif
                        @if ($applied_date_start)
                            <flux:badge color="blue" size="sm" class="flex items-center gap-1">
                                <span>{{ __('ecommerce::orders.filter.date_start') }}: {{ $applied_date_start }}</span>
                                <button type="button" wire:click="$set('date_start', null); $set('applied_date_start', null); resetPage()" class="ml-1 hover:text-red-500">×</button>
                            </flux:badge>
                        @endif
                        @if ($applied_date_end)
                            <flux:badge color="blue" size="sm" class="flex items-center gap-1">
                                <span>{{ __('ecommerce::orders.filter.date_end') }}: {{ $applied_date_end }}</span>
                                <button type="button" wire:click="$set('date_end', null); $set('applied_date_end', null); resetPage()" class="ml-1 hover:text-red-500">×</button>
                            </flux:badge>
                        @endif
                    </div>
                @endif
            </flux:card>
        @endif
        
        <flux:table :paginate="$orders">
            <flux:table.columns>
                <flux:table.column>{{ __('ecommerce::orders.table.reference') }}</flux:table.column>
                <flux:table.column>{{ __('ecommerce::orders.table.client') }}</flux:table.column>
                <flux:table.column>{{ __('ecommerce::orders.table.status') }}</flux:table.column>
                <flux:table.column>{{ __('ecommerce::orders.table.currency') }}</flux:table.column>
                <flux:table.column>{{ __('ecommerce::orders.table.payment_method') }}</flux:table.column>
                <flux:table.column>{{ __('ecommerce::orders.table.date') }}</flux:table.column>
                <flux:table.column class="w-32">{{ __('common.actions') }}</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse ($orders as $order)
                    <flux:table.row>
                        <flux:table.cell>
                            <div class="font-medium">{{ $order->reference }}</div>
                        </flux:table.cell>
                        <flux:table.cell>
                            <div>
                                <div class="font-medium">{{ $order->client->user->name ?? '-' }}</div>
                                <div class="text-sm text-zinc-500">{{ $order->client->user->email ?? '-' }}</div>
                            </div>
                        </flux:table.cell>
                        <flux:table.cell>
                            @if ($order->lastStatus)
                                <div class="flex items-center gap-2">
                                    @if ($order->lastStatus->color)
                                        <div class="w-3 h-3 rounded-full" style="background-color: {{ $order->lastStatus->color }}"></div>
                                    @endif
                                    <span>{{ $order->last_status_name ?? $order->lastStatus->name ?? '-' }}</span>
                                </div>
                            @else
                                <span class="text-zinc-400">-</span>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <span>{{ $order->currency->name ?? '-' }}</span>
                        </flux:table.cell>
                        <flux:table.cell>
                            <span class="text-sm">{{ $order->payment_method ?? '-' }}</span>
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="text-sm">
                                <div>{{ $order->created_at->format('Y-m-d') }}</div>
                                <div class="text-zinc-500">{{ $order->created_at->format('H:i') }}</div>
                            </div>
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex items-center gap-1">
                                <flux:button icon="pencil" :href="route(config('ud-ecommerce.admin_route_prefix', 'admin').'.orders.edit', $order)" wire:navigate>
                                    {{ __('common.edit') }}
                                </flux:button>
                                <flux:button variant="danger" icon="trash" wire:click="requestDelete({{ $order->id }})">
                                    {{ __('common.delete') }}
                                </flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="7" class="text-center py-8">
                            <div class="flex flex-col items-center gap-2">
                                <flux:text class="text-zinc-500">{{ __('ecommerce::orders.no_orders_found') }}</flux:text>
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
                {{ __('ecommerce::orders.delete_confirmation_title') }}
            </flux:heading>
            
            <flux:subheading class="mb-6 text-gray-600">
                {{ __('ecommerce::orders.delete_confirmation_text') }}
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

