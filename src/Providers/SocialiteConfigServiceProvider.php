<?php

namespace Unusualdope\LaravelEcommerce\Providers;

use Illuminate\Support\ServiceProvider;
use Unusualdope\LaravelEcommerce\Models\SocialAuthProvider;

class SocialiteConfigServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * Dynamically configures Socialite drivers using credentials
     * stored in the database (social_auth_providers table).
     */
    public function boot(): void
    {
        // Only configure if the table exists (prevents errors during migration)
        try {
            $providers = SocialAuthProvider::getActiveProviders();
        } catch (\Exception $e) {
            return;
        }

        foreach ($providers as $provider) {
            if (! $provider->isConfigured()) {
                continue;
            }

            $redirectUrl = $provider->redirect_url ?: url('/auth/'.$provider->provider.'/callback');

            // Handle Twitter OAuth 2.0 mapping
            $configKey = $provider->provider === 'twitter' ? 'twitter-oauth-2' : $provider->provider;

            config([
                "services.{$configKey}" => [
                    'client_id' => $provider->client_id,
                    'client_secret' => $provider->client_secret,
                    'redirect' => $redirectUrl,
                ],
            ]);
        }
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        //
    }
}
