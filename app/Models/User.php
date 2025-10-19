<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable 
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', 
        'is_active',
         'two_factor_enabled',
        'two_factor_code',
        'two_factor_expires_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_code',
    ];

    public function favorites()
    {
        return $this->belongsToMany(PostDechet::class, 'favorites', 'user_id', 'post_dechet_id')->withTimestamps();
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'two_factor_enabled' => 'boolean',
            'two_factor_expires_at' => 'datetime',
        ];
    }

    // 🔔 Méthodes pour la gestion de la 2F
      /**
     * Generate and send 2FA code
     */
    public function generateAndSendTwoFactorCode()
    {
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        $this->update([
            'two_factor_code' => $code,
            'two_factor_expires_at' => now()->addMinutes(10),
        ]);

        \Mail::to($this->email)->send(new \App\Mail\TwoFactorCodeMail($this, $code));
    }

    /**
     * Verify 2FA code
     */
    public function verifyTwoFactorCode($code)
    {
        if ($this->two_factor_code !== $code) {
            return false;
        }

        if ($this->two_factor_expires_at < now()) {
            return false;
        }

        $this->update([
            'two_factor_code' => null,
            'two_factor_expires_at' => null,
        ]);

        return true;
    }

    /**
     * Enable 2FA
     */
    public function enableTwoFactor()
    {
        $this->update(['two_factor_enabled' => true]);
        $this->generateAndSendTwoFactorCode();
    }

    /**
     * Disable 2FA
     */
    public function disableTwoFactor()
    {
        $this->update([
            'two_factor_enabled' => false,
            'two_factor_code' => null,
            'two_factor_expires_at' => null,
        ]);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isClient(): bool
    {
        return $this->role === 'client';
    }

    // 🔔 Relation avec les notifications Laravel
    public function receivesBroadcastNotificationsOn(): string
    {
        return 'users.' . $this->id;
    }

    // 🔔 Raccourcis utiles
    public function unreadNotificationsCount()
    {
        return $this->unreadNotifications()->count();
    }

    public function latestNotifications($limit = 10)
    {
        return $this->notifications()->latest()->take($limit)->get();
    }
}