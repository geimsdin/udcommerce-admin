<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('feature-groups.title') }}</flux:heading>
            <flux:subheading>{{ __('feature-groups.subtitle') }}</flux:subheading>
        </div>
        <flux:button variant="primary" icon="plus" :href="route(config('ud-ecommerce.admin_route_prefix', 'admin').'.feature-groups.create')" wire:navigate>
            {{ __('feature-groups.add_feature_group') }}
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
        <flux:table :paginate="$featureGroups">
            <flux:table.columns>
                <flux:table.column>{{ __('feature-groups.table.name') }}</flux:table.column>
                <flux:table.column>{{ __('feature-groups.table.position') }}</flux:table.column>
                <flux:table.column class="w-32">{{ __('common.actions') }}</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse ($featureGroups as $featureGroup)
                    <flux:table.row>
                        <flux:table.cell>{{ $featureGroup->name }}</flux:table.cell>
                        <flux:table.cell>{{ $featureGroup->position }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex items-center gap-1">
                                <flux:button icon="pencil" :href="route(config('ud-ecommerce.admin_route_prefix', 'admin').'.feature-groups.edit', $featureGroup)" wire:navigate>
                                    {{ __('common.edit') }}
                                </flux:button>
                                <flux:button variant="danger" icon="trash" wire:click="requestDelete({{ $featureGroup->id }})">
                                    {{ __('common.delete') }}
                                </flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="9" class="text-center py-8">
                            <div class="flex flex-col items-center gap-2">
                                <flux:text class="text-zinc-500">{{ __('feature-groups.no_feature_groups_found') }}</flux:text>
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
                {{ __('feature-groups.delete_confirmation_title') }}
            </flux:heading>
            
            <flux:subheading class="mb-6 text-gray-600">
                {{ __('feature-groups.delete_confirmation_text') }}
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
