<?php

namespace Unusualdope\LaravelEcommerce\Livewire\Admin\Coupon;

use Livewire\Component;
use Livewire\WithPagination;
use Unusualdope\LaravelEcommerce\Models\Coupon;

class CouponIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public int $coupon_id = 0;

    public bool $show_delete_modal = false;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function requestDelete(int $id): void
    {
        $this->coupon_id = $id;
        $this->show_delete_modal = true;
    }

    public function delete(): void
    {
        Coupon::findOrFail($this->coupon_id)->delete();
        $this->show_delete_modal = false;
        $this->coupon_id = 0;
        session()->flash('status', __('ecommerce::coupons.coupon_deleted'));
    }

    public function render()
    {
        return view('ecommerce::livewire.admin.coupon.index', [
            'coupons' => Coupon::query()
                ->when($this->search, function ($query) {
                    $query->where('code', 'like', "%{$this->search}%");
                })
                ->orderBy('created_at', 'desc')
                ->paginate(15),
        ]);
    }
}
