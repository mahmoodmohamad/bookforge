<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'national_id',
        'phone',
        'city_id',
        'staff_id',
		'gender',
        'birth_date',
    ];
protected static function booted()
{
    static::addGlobalScope('tenant', function ($query) {
        if (app()->has('tenant')) {
            $query->where('tenant_id', app('tenant')->id);
        }
    });
}
    protected static function boot()
    {
        parent::boot();

        static::deleting(function (self $client) {
            $client->user()->delete();
        });
    }
public function tenant()
{
    return $this->belongsTo(Tenant::class);
}
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

   public function notes()
{
    return $this->hasManyThrough(
        Note::class,
        Booking::class,
        'client_id',      // FK في bookings
        'booking_id',  // FK في diagnoses
        'id',              // PK في clients
        'id'               // PK في bookings
    );
}


    public function scopeSearch($query, string $search)
    {
        $like = "%{$search}%";
        
        return $query->whereHas('user', function($q) use ($search) {
            $q->search($search);
        })->orWhere('national_id', 'LIKE', $like)
          ->orWhere('phone', 'LIKE', $like);
    }
}