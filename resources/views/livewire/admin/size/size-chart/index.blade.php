<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('size-charts.title') }}</flux:heading>
            <flux:subheading>{{ __('size-charts.subtitle') }}</flux:subheading>
        </div>
        <flux:button variant="primary" icon="plus" :href="route(config('ud-ecommerce.admin_route_prefix', 'admin').'.size-charts.create')" wire:navigate>
            {{ __('size-charts.add_size_chart') }}
        </flux:button>
    </div>

    {{-- Status Message --}}
    @if (session('status'))
        <flux:callout variant="success" icon="check-circle">
            {{ session('status') }}
        </flux:callout>
    @endif

    {{-- Livewire Flash Message --}}
    <div x-data="{ show: false }"
         x-on:size-chart-deleted.window="show = true; setTimeout(() => show = false, 3000)"
         x-show="show"
         x-transition
         x-cloak>
        <flux:callout variant="success" icon="check-circle">
            {{ __('size-charts.size_chart_deleted') }}
        </flux:callout>
    </div>

    {{-- Card --}}
    <flux:card class="space-y-4">
        {{-- Search --}}
        <div class="max-w-sm">
            <flux:input
                wire:model.live.debounce.300ms="search"
                placeholder="{{ __('size-charts.search_placeholder') }}"
                icon="magnifying-glass"
            />
        </div>

        {{-- Table --}}
        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('size-charts.table.id') }}</flux:table.column>
                <flux:table.column>{{ __('size-charts.table.name') }}</flux:table.column>
                <flux:table.column>{{ __('size-charts.table.brand') }}</flux:table.column>
                <flux:table.column>{{ __('size-charts.table.category') }}</flux:table.column>
                <flux:table.column class="w-32">{{ __('size-charts.table.actions') }}</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($size_charts as $size_chart)
                    <flux:table.row wire:key="size_charts-{{ $size_chart->id }}">
                        <flux:table.column>{{ $size_chart->id }}</flux:table.column>
                        <flux:table.column>{{ $size_chart->name }}</flux:table.column>
                        <flux:table.column>{{ $size_chart->brand->name }}</flux:table.column>
                        <flux:table.column>{{ $size_chart->category->name }}</flux:table.column>
                        <flux:table.column>
                            <div class="flex items-center gap-1">
                                <flux:button
                                    size="sm"
                                    variant="ghost"
                                    icon="pencil"
                                    :href="route(config('ud-ecommerce.admin_route_prefix', 'admin').'.size-charts.edit', $size_chart)"
                                    wire:navigate
                                />
                                <flux:button
                                    size="sm"
                                    variant="ghost"
                                    icon="trash"
                                    wire:click="delete({{ $size_chart->id }})"
                                    wire:confirm="{{ __('size-charts.delete_confirm') }}"
                                    class="text-red-500 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-900/20"
                                />
                            </div>
                        </flux:table.column>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="4" class="text-center py-8">
                            <div class="flex flex-col items-center gap-2">
                                <flux:icon.users class="size-12 text-zinc-300 dark:text-zinc-600" />
                                <flux:text class="text-zinc-500">{{ __('size-charts.no_found') }}</flux:text>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        {{-- Pagination --}}
        @if ($size_charts->hasPages())
            <div class="pt-4">
                {{ $size_charts->links() }}
            </div>
        @endif
    </flux:card>
</div>

