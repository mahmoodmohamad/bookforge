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
        'note',
        'prescription',
        'notes',
    ];

    // Relationships
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    // ✅ للوصول للـ client عن طريق booking
    public function client()
    {
        return $this->hasOneThrough(
            Client::class,
            Booking::class,
            'id',           // FK في bookings
            'id',           // FK في clients
            'booking_id', // Local key في diagnoses
            'client_id'     // Local key في bookings
        );
    }

    // ✅ للوصول للـ provider عن طريق booking
    public function provider()
    {
        return $this->hasOneThrough(
            Provider::class,
            Booking::class,
            'id',
            'id',
            'booking_id',
            'provider_id'
        );
    }
}