<?php

namespace Unusualdope\LaravelEcommerce\Models;

use Illuminate\Database\Eloquent\Model;

class SocialAuthProvider extends Model
{
    protected $table = 'social_auth_providers';

    protected $fillable = [
        'provider',
        'client_id',
        'client_secret',
        'redirect_url',
        'is_active',
        'icon_path',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'client_secret' => 'encrypted',
        ];
    }

    /**
     * Supported social auth providers.
     *
     * @var array<string, array{name: string, icon: string}>
     */
    public const PROVIDERS = [
        'facebook' => ['name' => 'Facebook', 'icon' => 'facebook'],
        'google' => ['name' => 'Google', 'icon' => 'google'],
        'twitter' => ['name' => 'Twitter (X)', 'icon' => 'twitter'],
        'instagram' => ['name' => 'Instagram', 'icon' => 'instagram'],
    ];

    /**
     * Get all active providers.
     */
    public static function getActiveProviders(): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('is_active', true)->get();
    }

    /**
     * Get a provider by name.
     */
    public static function getByProvider(string $provider): ?self
    {
        return static::where('provider', $provider)->first();
    }

    /**
     * Check if a provider is active and properly configured.
     */
    public function isConfigured(): bool
    {
        return $this->is_active
            && ! empty($this->client_id)
            && ! empty($this->client_secret);
    }
}
