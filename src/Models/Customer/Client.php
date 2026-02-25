<?php

namespace Unusualdope\LaravelEcommerce\Models\Customer;

use App\Models\User;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Unusualdope\LaravelEcommerce\Models\Cart\Cart;
use Unusualdope\LaravelEcommerce\Models\Order\Order;

class Client extends Model
{
    use Cachable;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($client) {
            $user = $client->user;
            if (! $user) {
                throw new \Exception('Client cannot be created without a user.');
            }
            $user->syncRoles('client');
        });

        static::updating(function ($client) {
            $user = $client->user;
            if (! $user) {
                throw new \Exception('Client cannot be updated without a user.');
            }
            $user->syncRoles('client');
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function cart(): HasMany
    {
        return $this->hasMany(Cart::class, 'client_id', 'id');
    }

    public function groups()
    {
        return $this->belongsToMany(ClientGroup::class, 'clients_client_groups', 'client_id', 'client_group_id');
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(\Unusualdope\LaravelEcommerce\Models\Address::class, 'client_id', 'id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function defaultAddress(): \Unusualdope\LaravelEcommerce\Models\Address
    {
        return \Unusualdope\LaravelEcommerce\Models\Address::where('client_id', $this->id)
            ->where('default', 1)
            ->first();
    }

    public static function getStates($remove_nulls = false)
    {
        // Retrieve distinct states from the clients table
        $states = self::select('state')
            ->when($remove_nulls, function ($query) {
                $query->whereNotNull('state');
            })
            ->distinct()
            ->orderBy('state', 'asc')  // Optional: Sort by state name
            ->pluck('state');         // Pluck as key-value pairs with state as both

        return $states;
    }

    public static function export()
    {
        $clients = [];
        foreach (self::with(['user', 'agent'])->all() as $client) {
            $clients[] = [
                'EMAIL' => $client->user->email,
                'FIRST NAME' => ! empty($client->first_name) ? $client->first_name : $client->user->name,
                'LAST NAME' => ! empty($client->last_name) ? $client->last_name : '',
                'CODICE AGENTE' => $client->agent->reference_code ?? '',
                'CODICE' => $client->reference_code,
                'RAGIONE SOCIALE' => $client->company_name,
                'INDIRIZZO' => $client->address,
                'CAP' => $client->postcode,
                'LOCALITA' => $client->city,
                'PROVINCIA' => $client->state,
                'PARTITA IVA' => $client->vat_code,
                'CODICE FISCALE' => $client->fiscal_code,
                'TELEFONO' => $client->phone,
            ];
        }

        return $clients;
    }
}
