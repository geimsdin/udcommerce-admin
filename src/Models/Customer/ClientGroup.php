<?php

namespace Unusualdope\LaravelEcommerce\Models\Customer;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;

class ClientGroup extends Model
{
    use Cachable;

    public function clients()
    {
        return $this->belongsToMany(Client::class, 'clients_client_groups', 'client_group_id', 'client_id');
    }
}
