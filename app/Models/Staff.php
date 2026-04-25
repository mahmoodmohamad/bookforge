<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'phone',
        'city_id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function (self $staff) {
            $staff->user()->delete();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function clients()
    {
        return $this->hasMany(Client::class, 'staff_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'staff_id');
    }

    public function scopeSearch($query, string $search)
    {
        return $query->whereHas('user', function($q) use ($search) {
            $q->search($search);
        })->orWhere('phone', 'LIKE', "%{$search}%");
    }
}