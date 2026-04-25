<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;
use App\Models\Client;
use Illuminate\Support\Facades\Cache;
class DashboardController extends Controller
{
    public function __invoke()
    {
        $staff = Auth::user()->staff;

        $totalClients = $staff->clients()->count();
        $totalBookings = $staff->bookings()->count();

        $todayBookings = $staff->bookings()
            ->whereDate('booking_date', today())
            ->count();

        $upcomingBookings = $staff->bookings()
            ->whereDate('booking_date', '>', today())
            ->count();

        return view('staff.dashboard', compact(
            'totalClients',
            'totalBookings',
            'todayBookings',
            'upcomingBookings'
        ));
    }
}
