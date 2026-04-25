<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BookingPolicy
{
    use HandlesAuthorization;

    /**
     * Admin bypass (optional but recommended)
     */
    public function before(User $user)
    {
        if ($user->hasRole('admin')) {
            return true;
        }
    }

    /**
     * View list of bookings
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('staff')
            || $user->hasRole('provider');
    }

    /**
     * View a single booking
     */
    public function view(User $user, Booking $booking): bool
    {
        // Provider can view his own bookings
        if ($user->hasRole('provider')) {
            return $booking->provider_id === $user->provider?->id;
        }

        // Staff can view bookings she created
        if ($user->hasRole('staff')) {
            return $booking->staff_id === $user->staff?->id;
        }

        return false;
    }

    /**
     * Create booking (staff only)
     */
    public function create(User $user): bool
    {
        return $user->hasRole('staff');
    }

    /**
     * Update booking
     * (Provider updates note / status)
     */
    public function update(User $user, Booking $booking): bool
    {
        return $user->hasRole('provider')
            && $booking->provider_id === $user->provider?->id
            && $booking->status !== 'cancelled';
    }

    /**
     * Delete booking (staff only)
     */
    public function delete(User $user, Booking $booking): bool
    {
        return $user->hasRole('staff')
            && $booking->staff_id === $user->staff?->id
            && $booking->status !== 'completed';
    }
}
