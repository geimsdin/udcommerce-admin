<?php

namespace Unusualdope\LaravelEcommerce\Livewire\Admin\Administration\PaymentGateway;

use Livewire\Component;
use Livewire\WithPagination;
use Unusualdope\LaravelEcommerce\Models\Payment\PaymentGateway;

class PaymentGatewayIndex extends Component
{
    use WithPagination;

    public $search = '';
    public bool $editing = false;
    public ?int $editingId = null;
    public array $editingConfig = [];
    public string $editingName = '';

    public function toggleActive(int $id): void
    {
        $gateway = PaymentGateway::findOrFail($id);
        $gateway->update(['active' => !$gateway->active]);

        $status = $gateway->active ? 'activated' : 'deactivated';
        session()->flash('status', "Payment Gateway {$status} successfully.");
    }

    public function edit(int $id): void
    {
        $gateway = PaymentGateway::findOrFail($id);
        $this->editingId = $id;
        $this->editingName = collect(explode('\\', $gateway->name))->last();
        $this->editingConfig = $gateway->config ?? [];
        $this->editing = true;
    }

    public function save(): void
    {
        $this->validate([
            'editingConfig.*' => 'nullable|string',
        ]);

        $gateway = PaymentGateway::findOrFail($this->editingId);
        $gateway->update(['config' => $this->editingConfig]);

        $this->editing = false;
        $this->editingId = null;
        $this->editingConfig = [];

        session()->flash('status', 'Payment Gateway configuration updated successfully.');
    }

    public function cancel(): void
    {
        $this->editing = false;
        $this->editingId = null;
        $this->editingConfig = [];
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('ecommerce::livewire.admin.administration.payment-gateway.payment-gateway-index', [
            'gateways' => PaymentGateway::query()
                ->when($this->search, function ($query) {
                    $query->where('name', 'like', "%{$this->search}%")
                        ->orWhere('driver', 'like', "%{$this->search}%");
                })
                ->paginate(15),
        ]);
    }
}
