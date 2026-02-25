<?php

namespace Unusualdope\LaravelEcommerce\Livewire\Admin\Customer\ClientGroup;

use Livewire\Component;
use Unusualdope\LaravelEcommerce\Models\Customer\ClientGroup;

class ClientGroupCreateEdit extends Component
{
    public ClientGroup $clientGroup;

    public bool $isEditing = false;

    public string $name = '';

    public string $color = '#000000';

    public bool $default = false;

    public function mount(ClientGroup $clientGroup): void
    {
        $this->clientGroup = $clientGroup;
        if ($clientGroup?->exists) {
            $this->isEditing = true;
            $this->clientGroup = $clientGroup;
            $this->name = $clientGroup->name;
            $this->color = $clientGroup->color;
            $this->default = $clientGroup->default;
        }
    }

    public function save(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'color' => ['required', 'string', 'max:10'],
            'default' => ['required', 'boolean'],
        ]);
        $fields = [
            'name' => $this->name,
            'color' => $this->color,
            'default' => $this->default,
        ];
        if ($this->default) {
            ClientGroup::where('default', 1)->update(['default' => 0]);
        }
        if ($this->clientGroup?->exists) {
            $this->clientGroup->update($fields);
            session()->flash('status', __('ecommerce::client_groups.client_group_updated'));
        } else {
            if (ClientGroup::count() == 0) {
                $fields['default'] = 1;
            }
            $this->clientGroup = ClientGroup::create($fields);
            session()->flash('status', __('ecommerce::client_groups.client_group_created'));
        }
        $this->redirect(route(config('ud-ecommerce.admin_route_prefix', 'admin').'.client-groups.index'));
    }

    public function render()
    {
        return view('ecommerce::livewire.admin.customer.client-group.client-group-create-edit', [
            'isEditing' => $this->isEditing,
        ]);
    }
}
