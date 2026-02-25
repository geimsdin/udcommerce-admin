<?php

namespace Unusualdope\LaravelEcommerce\Livewire\Admin\Customer\ClientGroup;

use Livewire\Component;
use Livewire\WithPagination;
use Unusualdope\LaravelEcommerce\Models\Customer\ClientGroup;

class ClientGroupIndex extends Component
{
    use WithPagination;

    public int $client_group_id = 0;

    public bool $show_delete_modal = false;

    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function requestDelete(int $id): void
    {
        $this->client_group_id = $id;
        $this->show_delete_modal = true;
    }

    public function delete(): void
    {
        ClientGroup::findOrFail($this->client_group_id)->delete();
        $this->show_delete_modal = false;
        $this->client_group_id = 0;
        session()->flash('status', __('ecommerce::client_groups.client_group_deleted'));
    }

    public function render()
    {
        return view('ecommerce::livewire.admin.customer.client-group.client-group-index', [
            'clientGroups' => ClientGroup::query()
                ->when($this->search, function ($query) {
                    $query->where('name', 'like', "%{$this->search}%");
                })
                ->orderBy('id')
                ->paginate(15),
        ]);
    }
}
