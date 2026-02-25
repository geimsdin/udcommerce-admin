<?php

namespace Unusualdope\LaravelEcommerce\Livewire\Admin\SocialAuth;

use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Unusualdope\LaravelEcommerce\Models\SocialAuthProvider;

class SocialAuthSettings extends Component
{
    use WithFileUploads;

    /** @var array<string, array{client_id: string, client_secret: string, redirect_url: string, is_active: bool}> */
    public array $providers = [];

    public $iconUploads = []; // Temporary storage for uploads keyed by provider

    public function mount(): void
    {
        $this->loadProviders();
    }

    /**
     * Load all provider configurations from the database, initializing missing ones.
     */
    protected function loadProviders(): void
    {
        $existingProviders = SocialAuthProvider::all()->keyBy('provider');

        foreach (SocialAuthProvider::PROVIDERS as $key => $meta) {
            $existing = $existingProviders->get($key);

            $this->providers[$key] = [
                'client_id' => $existing?->client_id ?? '',
                'client_secret' => $existing?->client_secret ?? '',
                'redirect_url' => $existing?->redirect_url ?? '',
                'is_active' => $existing?->is_active ?? false,
                'icon_path' => $existing?->icon_path, // Load existing icon path
            ];
            $this->iconUploads[$key] = null; // Initialize upload field
        }
    }

    /**
     * Validation rules for provider settings.
     *
     * @return array<string, string>
     */
    protected function rules(): array
    {
        $rules = [];

        foreach (array_keys(SocialAuthProvider::PROVIDERS) as $provider) {
            $rules["providers.{$provider}.client_id"] = 'nullable|string|max:255';
            $rules["providers.{$provider}.client_secret"] = 'nullable|string|max:1000';
            $rules["providers.{$provider}.redirect_url"] = 'nullable|url|max:500';
            $rules["providers.{$provider}.is_active"] = 'boolean';
            $rules["iconUploads.{$provider}"] = 'nullable|image|max:1024'; // Max 1MB image
        }

        return $rules;
    }

    /**
     * Save all provider settings.
     */
    public function save(): void
    {
        $this->validate();

        foreach ($this->providers as $providerKey => $data) {
            // Only allow saving known providers
            if (! array_key_exists($providerKey, SocialAuthProvider::PROVIDERS)) {
                continue;
            }

            // Handle Icon Upload
            $iconPath = $data['icon_path'] ?? null;
            if (isset($this->iconUploads[$providerKey]) && $this->iconUploads[$providerKey]) {
                $iconPath = $this->iconUploads[$providerKey]->store('social-icons', 'public');
            }

            SocialAuthProvider::updateOrCreate(
                ['provider' => $providerKey],
                [
                    'client_id' => $data['client_id'] ?: null,
                    'client_secret' => $data['client_secret'] ?: null,
                    'redirect_url' => $data['redirect_url'] ?: null,
                    'is_active' => $data['is_active'],
                    'icon_path' => $iconPath,
                ]
            );
        }

        // Clear uploads after save
        $this->iconUploads = array_fill_keys(array_keys(SocialAuthProvider::PROVIDERS), null);

        // Reload providers to get new paths
        $this->loadProviders();

        session()->flash('status', __('ecommerce::social-auth.settings_saved'));
    }

    /**
     * Delete the custom icon for a provider.
     */
    public function deleteIcon(string $provider): void
    {
        if (! array_key_exists($provider, SocialAuthProvider::PROVIDERS)) {
            return;
        }

        $providerModel = SocialAuthProvider::where('provider', $provider)->first();

        if ($providerModel && $providerModel->icon_path) {
            Storage::disk('public')->delete($providerModel->icon_path);
            $providerModel->update(['icon_path' => null]);

            // Update local state
            $this->providers[$provider]['icon_path'] = null;
            $this->iconUploads[$provider] = null;

            session()->flash('status', __('ecommerce::social-auth.icon_removed'));
        }
    }

    /**
     * Toggle a specific provider's active status.
     */
    public function toggleProvider(string $provider): void
    {
        if (array_key_exists($provider, $this->providers)) {
            $this->providers[$provider]['is_active'] = ! $this->providers[$provider]['is_active'];
        }
    }

    public function render(): \Illuminate\View\View
    {
        return view('ecommerce::livewire.admin.social-auth.settings', [
            'providerMeta' => SocialAuthProvider::PROVIDERS,
        ]);
    }
}
