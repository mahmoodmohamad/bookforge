<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{User, Client, Provider, Staff, Booking, Note, City};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class AdminController extends Controller
{
    /**
     * Admin Dashboard - Overview
     */
    public function dashboard()
{
    $stats = Cache::remember('admin.dashboard.stats', 300, function () {
        return [
            'total_users'          => User::count(),
            'total_clients'       => Client::count(),
            'total_providers'     => Provider::count(),
            'total_secretaries'    => Staff::count(),
            'total_bookings'   => Booking::count(),
            'total_diagnoses'      => Note::count(),
            'month_bookings'   => Booking::whereMonth('created_at', now()->month)->count(),
            'month_clients'       => Client::whereMonth('created_at', now()->month)->count(),
            'scheduled'            => Booking::where('status', 'scheduled')->count(),
            'completed'            => Booking::where('status', 'completed')->count(),
            'cancelled'            => Booking::where('status', 'cancelled')->count(),
        ];
    });

    // Today's bookings - مش هنكاش لأن بتتغير كتير
    $stats['today_bookings'] = Booking::whereDate('booking_date', today())->count();

    $recentBookings = Cache::remember('admin.dashboard.recent_bookings', 120, function () {
        return Booking::with(['client.user', 'provider.user'])
            ->latest()
            ->take(10)
            ->get();
    });

    $recentClients = Cache::remember('admin.dashboard.recent_clients', 120, function () {
        return Client::with('user')->latest()->take(5)->get();
    });

    $bookingsByStatus = Cache::remember('admin.dashboard.bookings_by_status', 300, function () {
        return Booking::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();
    });

    $monthlyData = Cache::remember('admin.dashboard.monthly_data', 3600, function () {
        return Booking::select(
                DB::raw('DATE_FORMAT(booking_date, "%Y-%m") as month'),
                DB::raw('count(*) as count')
            )
            ->where('booking_date', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    });

    $topProviders = Cache::remember('admin.dashboard.top_providers', 3600, function () {
        return Provider::withCount('bookings')
            ->with('user')
            ->orderByDesc('bookings_count')
            ->take(5)
            ->get();
    });

    return view('admin.dashboard', compact(
        'stats', 'recentBookings', 'recentClients',
        'bookingsByStatus', 'monthlyData', 'topProviders'
    ));
}

    /**
     * System Statistics Page
     */
    public function statistics()
    {
        // Detailed statistics
        $stats = [
            'users_by_role' => [
                'clients' => Client::count(),
                'providers' => Provider::count(),
                'secretaries' => Staff::count(),
                'admins' => User::admins()->count(),
            ],
            
            'bookings_by_month' => Booking::select(
                    DB::raw('DATE_FORMAT(booking_date, "%Y-%m") as month'),
                    DB::raw('count(*) as count')
                )
                ->where('booking_date', '>=', now()->subYear())
                ->groupBy('month')
                ->orderBy('month')
                ->get(),
            
            'bookings_by_provider' => Provider::withCount('bookings')
                ->with('user')
                ->having('bookings_count', '>', 0)
                ->orderByDesc('bookings_count')
                ->get(),
            
            'cities_distribution' => City::withCount(['clients', 'providers', 'secretaries'])
                ->get(),
        ];

        return view('admin.statistics', compact('stats'));
    }
}