<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'activation', 'tenant_id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'activation' => 'boolean',
    ];
protected static function booted()
{
    static::addGlobalScope('tenant', function ($query) {
        if (app()->has('tenant')) {
            $query->where('tenant_id', app('tenant')->id);
        }
    });
}

// Add relationship
public function tenant()
{
    return $this->belongsTo(Tenant::class);
}
    // Relationships
    public function admin(): HasOne
    {
        return $this->hasOne(Admin::class);
    }

    public function provider(): HasOne  // was: provider()
{
    return $this->hasOne(Provider::class);
}

public function client(): HasOne  // was: client()
{
    return $this->hasOne(Client::class);
}

    public function staff(): HasOne
    {
        return $this->hasOne(Staff::class);
    }

   
    // Role checking with caching
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isProvider(): bool
    {
        return $this->hasRole('provider');
    }

    public function isStaff(): bool
    {
        return $this->hasRole('staff');
    }

    public function isClient(): bool
    {
        return $this->hasRole('client');
    }

    protected function hasRole(string $role): bool
    {
        static $cache = [];
        
        if (!isset($cache[$this->id][$role])) {
            $cache[$this->id][$role] = $this->$role()->exists();
        }
        
        return $cache[$this->id][$role];
    }

    // Helper methods
    public function getRoleName(): string
    {
        if ($this->isAdmin()) return 'Admin';
        if ($this->isProvider()) return 'Provider';
        if ($this->isStaff()) return 'Staff';
        if ($this->isClient()) return 'Client';
        
        return 'Unknown';
    }

    public function getRoleAttribute()
    {
        return $this->admin 
            ?? $this->provider 
            ?? $this->staff 
            ?? $this->client;
    }

    public function isActive(): bool
    {
        return (bool) $this->activation;
    }

    // Query scopes
    public function scopeSearch($query, string $search)
    {
        $like = "%{$search}%";
        
        return $query->where(function($q) use ($like) {
            $q->where('email', 'LIKE', $like)
              ->orWhere('name', 'LIKE', $like);
        });
    }

    public function scopeActive($query)
    {
        return $query->where('activation', true);
    }

    public function scopeProviders($query)
    {
        return $query->whereHas('provider');
    }

    public function scopeSecretaries($query)
    {
        return $query->whereHas('staff');
    }

    public function scopeClients($query)
    {
        return $query->whereHas('client');
    }

    public function scopeAdmins($query)
    {
        return $query->whereHas('admin');
    }
}