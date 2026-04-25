<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'specialization',
        'phone',
        'city_id',
    ];

   protected static function booted()
{
    static::addGlobalScope('tenant', function ($query) {
        if (app()->has('tenant')) {
            $query->where('tenant_id', app('tenant')->id);
        }
    });
}
public function tenant()
{
    return $this->belongsTo(Tenant::class);
}
    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function bookings()  
    {
        return $this->hasMany(Booking::class);
    }
    
    public function notes()  
    {
        return $this->hasMany(Note::class);
    }

    public function todayBookings()
    {
        return $this->bookings()
            ->whereDate('booking_date', today())
            ->orderBy('booking_time')
            ->get();
    }

    public function upcomingBookings()
    {
        return $this->bookings()
            ->where('booking_date', '>=', now())
            ->where('status', 'scheduled')
            ->orderBy('booking_date')
            ->orderBy('booking_time')
            ->get();
    }

    // Search scope
    public function scopeSearch($query, string $search)
    {
        $like = "%{$search}%";
        
        return $query->whereHas('user', function($q) use ($search) {
            $q->search($search);
        })->orWhere('specialization', 'LIKE', $like);
    }
}