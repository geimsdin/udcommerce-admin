<div class="max-w-2xl space-y-6">
    {{-- Header --}}
    <div>
        <flux:heading size="xl">
            {{ $isEditing ? __('size-charts.edit_size_chart') : __('size-charts.create_size_chart') }}
        </flux:heading>
        <flux:subheading>
            {{ $isEditing ? __('size-charts.messages.edit_subtitle') : __('size-charts.messages.create_subtitle') }}
        </flux:subheading>
    </div>

    {{-- Card --}}
    <flux:card>
        <form wire:submit="save" class="space-y-6">
            {{-- Title --}}
            <flux:input
                wire:model="name"
                :label="__('size-charts.form.name')"
                placeholder="{{ __('size-charts.form.name_placeholder') }}"
                required
            />

            <flux:select variant="listbox" :required="true" :placeholder="__('size-charts.form.brand_placeholder')" :label="__('size-charts.form.brand')" wire:model="brand_id" searchable clearable>
                @foreach($this->getBrand() as $brand)
                    <flux:select.option :value="$brand->id">{{ $brand->name }}</flux:select.option>
                @endforeach
            </flux:select>
            <flux:select variant="listbox" :required="true" :placeholder="__('size-charts.form.category_placeholder')" :label="__('size-charts.form.category')" wire:model="category_id" searchable clearable>
                @foreach($this->getCategory() as $category)
                    <flux:select.option :value="$category->id">{{ $category->name }}</flux:select.option>
                @endforeach
            </flux:select>

            {{-- Buttons --}}
            <div class="flex items-center gap-4">
                <flux:button variant="primary" type="submit">
                    {{ $isEditing ? __('size-charts.update_size_chart') : __('size-charts.create_size_chart') }}
                </flux:button>
                <flux:button variant="ghost" :href="route(config('ud-ecommerce.admin_route_prefix', 'admin').'.size-charts.index')" wire:navigate>
                    {{ __('size-charts.form.cancel') }}
                </flux:button>
            </div>
        </form>
    </flux:card>
</div>

