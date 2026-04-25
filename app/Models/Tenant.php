<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 'slug', 'business_type', 'config', 'is_active'
    ];

    protected $casts = [
        'config' => 'array',
        'is_active' => 'boolean',
    ];

    // Get a config value with fallback
    public function getConfig(string $key, $default = null)
    {
        return data_get($this->config, $key, $default);
    }

    // Relationships
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function providers()
    {
        return $this->hasMany(Provider::class);
    }

    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
