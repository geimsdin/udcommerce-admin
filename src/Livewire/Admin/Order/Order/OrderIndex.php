<?php

namespace Unusualdope\LaravelEcommerce\Livewire\Admin\Order\Order;

use Livewire\Component;
use Livewire\WithPagination;
use Unusualdope\LaravelEcommerce\Models\Customer\Client;
use Unusualdope\LaravelEcommerce\Models\Order\Order;
use Unusualdope\LaravelEcommerce\Models\Order\OrderStatus;

class OrderIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public ?int $client_id = null;

    public ?int $status_id = null;

    public ?string $date_start = null;

    public ?string $date_end = null;

    // Applied filters (separate from form inputs)
    public ?int $applied_client_id = null;

    public ?int $applied_status_id = null;

    public ?string $applied_date_start = null;

    public ?string $applied_date_end = null;

    public bool $show_filters = false;

    public int $order_id = 0;

    public bool $show_delete_modal = false;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function toggleFilters(): void
    {
        $this->show_filters = ! $this->show_filters;
    }

    public function applyFilters(): void
    {
        $this->applied_client_id = $this->client_id;
        $this->applied_status_id = $this->status_id;
        $this->applied_date_start = $this->date_start;
        $this->applied_date_end = $this->date_end;
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->client_id = null;
        $this->status_id = null;
        $this->date_start = null;
        $this->date_end = null;
        $this->applied_client_id = null;
        $this->applied_status_id = null;
        $this->applied_date_start = null;
        $this->applied_date_end = null;
        $this->search = '';
        $this->resetPage();
    }

    public function requestDelete(int $id): void
    {
        $this->order_id = $id;
        $this->show_delete_modal = true;
    }

    public function delete(): void
    {
        Order::findOrFail($this->order_id)->delete();
        $this->show_delete_modal = false;
        $this->order_id = 0;
        session()->flash('status', __('ecommerce::orders.order_deleted'));
    }

    public function render()
    {
        return view('ecommerce::livewire.admin.order.order.order-index', [
            'orders' => Order::query()
                ->with(['client.user', 'lastStatus', 'currency', 'carrier'])
                ->when($this->search, function ($query) {
                    $query->where(function ($q) {
                        $q->where('reference', 'like', "%{$this->search}%")
                            ->orWhereHas('client.user', function ($q) {
                                $q->where('name', 'like', "%{$this->search}%")
                                    ->orWhere('email', 'like', "%{$this->search}%");
                            });
                    });
                })
                ->when($this->applied_client_id, function ($query) {
                    $query->where('client_id', $this->applied_client_id);
                })
                ->when($this->applied_status_id, function ($query) {
                    $query->where('last_status_id', $this->applied_status_id);
                })
                ->when($this->applied_date_start, function ($query) {
                    $query->whereDate('created_at', '>=', $this->applied_date_start);
                })
                ->when($this->applied_date_end, function ($query) {
                    $query->whereDate('created_at', '<=', $this->applied_date_end);
                })
                ->orderBy('created_at', 'desc')
                ->paginate(15),
            'clients' => Client::with('user')->orderBy('id')->get(),
            'orderStatuses' => OrderStatus::orderBy('id')->get(),
        ]);
    }
}
