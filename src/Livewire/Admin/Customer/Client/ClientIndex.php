<?php

namespace Unusualdope\LaravelEcommerce\Livewire\Admin\Customer\Client;

use Livewire\Component;
use Livewire\WithPagination;
use Unusualdope\LaravelEcommerce\Models\Customer\Client;

class ClientIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public int $client_id = 0;

    public bool $show_delete_modal = false;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function requestDelete(int $id): void
    {
        $this->client_id = $id;
        $this->show_delete_modal = true;
    }

    public function delete(): void
    {
        Client::findOrFail($this->client_id)->delete();
        $this->show_delete_modal = false;
        $this->client_id = 0;
        session()->flash('status', __('ecommerce::clients.client_deleted'));
    }

    public function render()
    {
        return view('ecommerce::livewire.admin.customer.client.client-index', [
            'clients' => Client::query()
                ->with(['user'])
                ->when($this->search, fn ($q) => $q->whereHas('user', function ($q) {
                    $q->where('name', 'like', "%{$this->search}%");
                })
                )->paginate(15),
        ]);
    }
}
