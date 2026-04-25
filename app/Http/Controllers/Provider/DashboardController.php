<?php

namespace App\Http\Controllers\provider;


use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Provider Dashboard
     */
    public function dashboard()
    {
        $provider = auth()->user()->provider;

        // Today's statistics
        $stats = [
            'today_bookings' => $provider->bookings()
                ->whereDate('booking_date', today())
                ->count(),
            
            'today_completed' => $provider->bookings()
                ->whereDate('booking_date', today())
                ->where('status', 'completed')
                ->count(),
            
            'today_pending' => $provider->bookings()
                ->whereDate('booking_date', today())
                ->where('status', 'scheduled')
                ->count(),
            
            'total_clients' => $provider->bookings()
                ->distinct('client_id')
                ->count('client_id'),
        ];

        // Today's bookings
        $todayBookings = $provider->bookings()
            ->with(['client.user', 'note'])
            ->whereDate('booking_date', today())
            ->orderBy('booking_time')
            ->get();

        // Upcoming bookings (next 7 days)
        $upcomingBookings = $provider->bookings()
            ->with(['client.user'])
            ->where('booking_date', '>', now())
            ->where('booking_date', '<=', now()->addDays(7))
            ->where('status', 'scheduled')
            ->orderBy('booking_date')
            ->orderBy('booking_time')
            ->take(5)
            ->get();

        return view('provider.dashboard', compact('stats', 'todayBookings', 'upcomingBookings'));
    }

}
