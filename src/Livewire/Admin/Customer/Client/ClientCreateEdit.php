<?php

namespace Unusualdope\LaravelEcommerce\Livewire\Admin\Customer\Client;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Unusualdope\LaravelEcommerce\Models\Customer\Client;
use Unusualdope\LaravelEcommerce\Models\Customer\ClientGroup;

class ClientCreateEdit extends Component
{
    public Client $client;

    public bool $isEditing = false;

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public string $company_name = '';

    public string $vat_code = '';

    public string $fiscal_code = '';

    public string $pec = '';

    public string $postcode = '';

    public string $city = '';

    public string $state = '';

    public string $country = '';

    public string $first_name = '';

    public string $last_name = '';

    public string $phone = '';

    public array $client_groups = [];

    public array $addresses = [];

    public function mount(Client $client): void
    {
        $this->client = $client;
        if ($client?->exists) {
            $this->isEditing = true;
            $this->email = $client->user->email;
            $this->company_name = $client->company_name ?? '';
            $this->vat_code = $client->vat_code ?? '';
            $this->fiscal_code = $client->fiscal_code ?? '';
            $this->pec = $client->pec ?? '';
            $this->postcode = $client->postcode ?? '';
            $this->city = $client->city ?? '';
            $this->state = $client->state ?? '';
            $this->country = $client->country ?? '';
            $this->first_name = $client->first_name;
            $this->last_name = $client->last_name;
            $this->phone = $client->phone ?? '';
            $this->client_groups = $client->groups->pluck('id')->toArray();
            $this->addresses = $client->addresses->map(function ($address) {
                return [
                    'id' => $address->id,
                    'name' => $address->name ?? '',
                    'destination_name' => $address->destination_name ?? '',
                    'address' => $address->address ?? '',
                    'post_code' => $address->post_code ?? '',
                    'city' => $address->city ?? '',
                    'state' => $address->state ?? '',
                    'country' => $address->country ?? 'Italy',
                    'telephone' => $address->telephone ?? '',
                    'default' => $address->default ?? false,
                ];
            })->toArray();
        } else {
            $this->client = new Client;
        }
    }

    #[Computed]
    public function clientGroups()
    {
        return ClientGroup::all();
    }

    public function addAddress(): void
    {
        $this->addresses[] = [
            'id' => null,
            'name' => '',
            'destination_name' => '',
            'address' => '',
            'post_code' => '',
            'city' => '',
            'state' => '',
            'country' => 'Italy',
            'telephone' => '',
            'default' => false,
        ];
    }

    public function removeAddress(int $index): void
    {
        unset($this->addresses[$index]);
        $this->addresses = array_values($this->addresses);
    }

    public function setDefaultAddress(int $index): void
    {
        foreach ($this->addresses as $key => $address) {
            $this->addresses[$key]['default'] = ($key === $index);
        }
    }

    public function save(): void
    {
        if (! $this->isEditing) {
            $validation_rules = [
                'email' => ['required', 'email', 'max:255', 'unique:users,email'],
                'password' => ['required', 'string', 'min:8'],
                'password_confirmation' => ['required', 'same:password'],
                'first_name' => ['required', 'string', 'max:255'],
            ];
        } else {
            $validation_rules = [
                'first_name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$this->client->user_id],
            ];
            if ($this->password != '') {
                $validation_rules['password'] = ['required', 'string', 'min:8'];
                $validation_rules['password_confirmation'] = ['required', 'same:password'];
            }
        }

        $this->validate($validation_rules);

        $user_fields = [
            'name' => $this->first_name.' '.$this->last_name,
            'email' => $this->email,
        ];

        if (! $this->isEditing) {
            $user_fields['password'] = Hash::make($this->password);
            $user = User::create($user_fields);
        } else {
            $user = $this->client->user;
            if ($this->password != '') {
                $user_fields['password'] = Hash::make($this->password);
            }
            $user->update($user_fields);
        }
        $user->syncRoles('client');

        $client_fields = [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'user_id' => $user->id,
            'company_name' => $this->company_name,
            'vat_code' => $this->vat_code,
            'fiscal_code' => $this->fiscal_code,
            'pec' => $this->pec,
            'postcode' => $this->postcode,
            'city' => $this->city,
            'state' => $this->state,
            'country' => $this->country,
            'phone' => $this->phone,
            'reference_code' => Str::random(10),
        ];

        if (! $this->isEditing) {
            $this->client = Client::create($client_fields);
        } else {
            $this->client->update($client_fields);
        }

        $this->client->groups()->sync($this->client_groups);

        // Save addresses
        $existingAddressIds = [];
        foreach ($this->addresses as $addressData) {
            if (isset($addressData['id']) && $addressData['id']) {
                // Update existing address
                $address = $this->client->addresses()->find($addressData['id']);
                if ($address) {
                    $address->update($addressData);
                    $existingAddressIds[] = $addressData['id'];
                }
            } else {
                // Create new address
                $newAddress = $this->client->addresses()->create($addressData);
                $existingAddressIds[] = $newAddress->id;
            }
        }

        // Delete removed addresses
        $this->client->addresses()->whereNotIn('id', $existingAddressIds)->delete();

        if (! $this->isEditing) {
            session()->flash('status', __('ecommerce::clients.client_created'));
        } else {
            session()->flash('status', __('ecommerce::clients.client_updated'));
        }
        $this->redirect(route(config('ud-ecommerce.admin_route_prefix', 'admin').'.clients.index'));
    }

    public function render()
    {
        return view('ecommerce::livewire.admin.customer.client.client-create-edit', [
            'isEditing' => $this->isEditing,
        ]);
    }
}
