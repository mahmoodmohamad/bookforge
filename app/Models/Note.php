<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',  
        'symptoms',
        'notes',
    ];

    protected static function booted()
{
    static::addGlobalScope('tenant', function ($query) {
        if (app()->has('tenant')) {
            $query->where('tenant_id', app('tenant')->id);
        }
    });
}
// Remove the problematic method, keep only:
public function getClient()
{
    return $this->booking->client;
}

public function getProvider()
{
    return $this->booking->provider;
}

// Or simply use accessors:
public function getClientAttribute()
{
    return $this->booking->client;
}

public function getProviderAttribute()
{
    return $this->booking->provider;
}

}