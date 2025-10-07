<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;

class User extends Authenticatable
{
    use Notifiable;

    protected $primaryKey = 'user_id';
    public $incrementing = true;

    protected $fillable = [
        'username',
        'password',
        'role',
    ];

    protected $appends = ['is_online'];

    /**
     * Get all sessions for the user.
     */
    public function sessions()
    {
        return $this->hasMany(UserSession::class, 'user_id', 'user_id');
    }

    public function getIsOnlineAttribute()
    {
        return $this->sessions()
            ->where('last_activity', '>=', now()->subMinutes(config('auth.guards.web.lifetime', 120)))
            ->exists();
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get all boarding houses owned by this user.
     */
    public function boardingHouses()
    {
        return $this->hasMany(BoardingHouse::class, 'user_id', 'user_id');
    }

    // Only hash the password if it's being set and not already hashed
    public function setPasswordAttribute($value)
    {
        if (!empty($value) && !preg_match('/^\$2[ayb]\$.+$/', $value)) {
            $this->attributes['password'] = Hash::make($value);
        } else {
            $this->attributes['password'] = $value;
        }
    }
    
    // Add this to properly handle the username as the authentication identifier
    public function getAuthIdentifierName()
    {
        return 'username';
    }
    
    // Add this to properly handle the password field name
    public function getAuthPassword()
    {
        return $this->password;
    }
}