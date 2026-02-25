<div>
    {{-- Header --}}
    <div class="mb-5">
        <flux:heading size="xl">
            {{ $isEditing ? __('product-categories.edit_category') : __('product-categories.add_category') }}
        </flux:heading>
        <flux:subheading>
            {{ $isEditing ? __('product-categories.messages.edit_subtitle') : __('product-categories.messages.create_subtitle') }}
        </flux:subheading>
    </div>
    <form wire:submit="save">
        <div class="flex justify-between gap-6">
            <div class="w-2/3">
                <flux:card class="space-y-6">
                    <flux:heading size="lg">
                        {{ __('product-categories.form.category_details') }}
                    </flux:heading>
                    <flux:separator />
                    {{-- Name (Multi-language) --}}
                    <flux:card>
                        <div class="mb-5">
                            <livewire:lmt-LangSelector wire:model.live="selected_language" />
                        </div>
                        <livewire:lmt-TextInput label="{{ __('product-categories.form.name') }}"
                            placeholder="{{ __('product-categories.form.name_placeholder') }}" wire:model="name"
                            :required="true" />
                        <div class="mt-2">
                            <livewire:lmt-Textarea label="{{ __('product-categories.form.description') }}"
                                placeholder="{{ __('product-categories.form.description_placeholder') }}"
                                wire:model="description" :required="false" />
                        </div>
                    </flux:card>
                    <flux:separator />

                    {{-- Buttons --}}
                    <div class="flex items-center gap-4">
                        <flux:button variant="primary" type="submit">
                            {{ $isEditing ? __('product-categories.update_category') : __('product-categories.create_category') }}
                        </flux:button>
                        <flux:button variant="ghost" :href="route(config('ud-ecommerce.admin_route_prefix', 'admin').'.product-categories.index')" wire:navigate>
                            {{ __('common.cancel') }}
                        </flux:button>
                    </div>
                </flux:card>
            </div>
            <div class="w-1/3">
                <flux:card class="space-y-6">
                    <flux:heading size="lg">
                        {{ __('product-categories.form.settings') }}
                    </flux:heading>
                    <flux:separator />
                    <flux:switch wire:model="status" :label="__('product-categories.form.status')" />
                    <flux:select variant="listbox" searchable :label="__('product-categories.form.parent_id')"
                        placeholder="{{ __('product-categories.form.parent_id_placeholder') }}" wire:model="parent_id"
                        clearable>
                        @foreach ($this->productCategories as $productCategory)
                            <flux:select.option :value="$productCategory->id">{{ $productCategory->name }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                </flux:card>
            </div>
        </div>
    </form>
</div>
