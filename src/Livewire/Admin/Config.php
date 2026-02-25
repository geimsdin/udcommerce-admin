<?php

namespace Unusualdope\LaravelEcommerce\Livewire\Admin;

use Livewire\Component;
use Unusualdope\LaravelEcommerce\Models\Config as EcommerceConfig;

class Config extends Component
{
    public $is_returned_product_affect_stock = false;

    public function mount()
    {
        $configs = EcommerceConfig::getValues([
            'is_returned_product_affect_stock',
        ]);
        $this->is_returned_product_affect_stock = $configs['is_returned_product_affect_stock'] ? true : false;
    }

    public function save()
    {
        EcommerceConfig::setValues([
            'is_returned_product_affect_stock' => $this->is_returned_product_affect_stock,
        ]);
        session()->flash('status', __('config.config_saved'));
    }

    public function resetConfig()
    {
        EcommerceConfig::resetValues([
            'is_returned_product_affect_stock',
        ]);
        $this->is_returned_product_affect_stock = false;
        session()->flash('status', __('config.config_reset'));
    }

    public function render()
    {
        return view('ecommerce::livewire.admin.config');
    }
}
