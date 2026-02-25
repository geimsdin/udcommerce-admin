<?php

namespace Unusualdope\LaravelEcommerce\Livewire\Admin\Order\OrderStatus;

use Livewire\Component;
use Livewire\WithPagination;
use Unusualdope\LaravelEcommerce\Models\Order\OrderStatus;

class OrderStatusIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public int $order_status_id = 0;

    public bool $show_delete_modal = false;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function requestDelete(int $id): void
    {
        $this->order_status_id = $id;
        $this->show_delete_modal = true;
    }

    public function delete(): void
    {
        $orderStatus = OrderStatus::findOrFail($this->order_status_id);

        // Check if it's a native status (prevent deletion)
        if (isset($orderStatus->is_native) && $orderStatus->is_native) {
            session()->flash('error', __('ecommerce::order_statuses.cannot_delete_native'));
            $this->show_delete_modal = false;
            $this->order_status_id = 0;

            return;
        }

        $orderStatus->delete();
        $this->show_delete_modal = false;
        $this->order_status_id = 0;
        session()->flash('status', __('ecommerce::order_statuses.order_status_deleted'));
    }

    public function render()
    {
        return view('ecommerce::livewire.admin.order.order-status.order-status-index', [
            'orderStatuses' => OrderStatus::query()
                ->with('languages')
                ->when($this->search, function ($query) {
                    $query->whereHas('languages', function ($q) {
                        $q->where('name', 'like', "%{$this->search}%");
                    });
                })
                ->orderBy('id')
                ->paginate(10),
        ]);
    }
}
