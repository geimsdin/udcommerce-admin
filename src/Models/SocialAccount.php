<?php

namespace Unusualdope\LaravelEcommerce\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialAccount extends Model
{
    protected $table = 'social_accounts';

    protected $fillable = [
        'user_id',
        'provider',
        'provider_id',
        'name',
        'email',
        'avatar',
        'token',
        'refresh_token',
        'token_expires_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'token' => 'encrypted',
            'refresh_token' => 'encrypted',
            'token_expires_at' => 'datetime',
        ];
    }

    /**
     * Get the user that owns this social account.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Find a social account by provider and provider ID.
     */
    public static function findByProvider(string $provider, string $providerId): ?self
    {
        return static::where('provider', $provider)
            ->where('provider_id', $providerId)
            ->first();
    }
}
