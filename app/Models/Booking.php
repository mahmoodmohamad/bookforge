<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'provider_id',
        'staff_id',
        'booking_date',
        'booking_time', // ✅ Add this
        'status',
        'notes'
    ];

    protected $casts = [
        'booking_date' => 'datetime'
    ];

    public function provider()  // was: provider()
{
    return $this->belongsTo(Provider::class);
}

public function client()  // was: client()
{
    return $this->belongsTo(Client::class);
}

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function note()
    {
        return $this->hasOne(Note::class);
    }

    // ✅ Add this method
    public static function isAvailable($providerId, $date, $time)
    {
        return !self::where('provider_id', $providerId)
            ->whereDate('booking_date', $date)
            ->where('booking_time', $time)
            ->where('status', '!=', 'cancelled')
            ->exists();
    }

    // ✅ Helper to get full datetime
    public function getFullDateTimeAttribute()
    {
        if ($this->booking_time) {
            return Carbon::parse($this->booking_date->format('Y-m-d') . ' ' . $this->booking_time);
        }
        return $this->booking_date;
    }
}