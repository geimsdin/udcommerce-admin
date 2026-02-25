<div class="space-y-6">
    <div>
        <flux:heading size="xl">
            {{ __('config.title') }}
        </flux:heading>
        <flux:subheading>
            {{ __('config.subtitle') }}
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
    <flux:card>
        <form wire:submit="save">
            <div class="grid grid-cols-4 gap-4">
                <flux:switch wire:model="is_returned_product_affect_stock" :label="__('config.form.is_returned_product_affect_stock')" :description="__('config.form.is_returned_product_affect_stock_tooltip')" />
            </div>
            <div class="flex items-center gap-4 justify-end mt-6">
                <flux:button type="button" wire:click="resetConfig">
                    {{ __('common.reset') }}
                </flux:button>
                <flux:button variant="primary" type="submit">
                    {{ __('common.save') }}
                </flux:button>
            </div>
        </form>
    </flux:card>
</div>
