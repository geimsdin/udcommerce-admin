<div class="max-w-2xl space-y-6">
    {{-- Header --}}
    <div>
        <flux:heading size="xl">
            {{ $isEditing ? __('size-chart-entries.edit_size_chart_entry') : __('size-chart-entries.create_size_chart_entry') }}
        </flux:heading>
        <flux:subheading>
            {{ $isEditing ? __('size-chart-entries.messages.edit_subtitle') : __('size-chart-entries.messages.create_subtitle') }}
        </flux:subheading>
    </div>

    {{-- Card --}}
    <flux:card>
        <form wire:submit="save" class="space-y-6">
            {{-- Title --}}
            <flux:input
                wire:model="converted_value"
                :label="__('size-chart-entries.form.converted_value')"
                placeholder="{{ __('size-chart-entries.form.converted_value_placeholder') }}"
                required
            />

            <flux:select variant="listbox" :required="true" :placeholder="__('size-chart-entries.form.size_chart_placeholder')" :label="__('size-chart-entries.form.size_chart')" wire:model="size_chart_id" searchable clearable>
                @foreach($this->getSizeChart() as $size_chart)
                    <flux:select.option :value="$size_chart->id">{{ $size_chart->name }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:select variant="listbox" :required="true" :placeholder="__('size-chart-entries.form.variant_placeholder')" :label="__('size-chart-entries.form.variant')" wire:model="variant_id" searchable clearable>
                @foreach($this->getVariant() as $variant)
                    <flux:select.option :value="$variant->id">{{ $variant->getNameCurrentLanguage($selected_language) }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:select variant="listbox" :required="true" :placeholder="__('size-chart-entries.form.target_unit_placeholder')" :label="__('size-chart-entries.form.target_unit')" wire:model="target_unit_id" searchable clearable>
                @foreach($this->getTargetUnit() as $target_unit)
                    <flux:select.option :value="$target_unit->id">{{ $target_unit->name }}</flux:select.option>
                @endforeach
            </flux:select>

            {{-- Buttons --}}
            <div class="flex items-center gap-4">
                <flux:button variant="primary" type="submit">
                    {{ $isEditing ? __('size-chart-entries.update_size_chart_entry') : __('size-chart-entries.create_size_chart_entry') }}
                </flux:button>
                <flux:button variant="ghost" :href="route(config('ud-ecommerce.admin_route_prefix', 'admin').'.size-chart-entries.index')" wire:navigate>
                    {{ __('size-chart-entries.form.cancel') }}
                </flux:button>
            </div>
        </form>
    </flux:card>
</div>

