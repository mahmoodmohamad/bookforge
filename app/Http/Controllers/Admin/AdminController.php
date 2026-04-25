<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{User, Patient, Physician, Secretary, Appointment, Diagnosis, City};
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
            'total_patients'       => Patient::count(),
            'total_physicians'     => Physician::count(),
            'total_secretaries'    => Secretary::count(),
            'total_appointments'   => Appointment::count(),
            'total_diagnoses'      => Diagnosis::count(),
            'month_appointments'   => Appointment::whereMonth('created_at', now()->month)->count(),
            'month_patients'       => Patient::whereMonth('created_at', now()->month)->count(),
            'scheduled'            => Appointment::where('status', 'scheduled')->count(),
            'completed'            => Appointment::where('status', 'completed')->count(),
            'cancelled'            => Appointment::where('status', 'cancelled')->count(),
        ];
    });

    // Today's appointments - مش هنكاش لأن بتتغير كتير
    $stats['today_appointments'] = Appointment::whereDate('appointment_date', today())->count();

    $recentAppointments = Cache::remember('admin.dashboard.recent_appointments', 120, function () {
        return Appointment::with(['patient.user', 'physician.user'])
            ->latest()
            ->take(10)
            ->get();
    });

    $recentPatients = Cache::remember('admin.dashboard.recent_patients', 120, function () {
        return Patient::with('user')->latest()->take(5)->get();
    });

    $appointmentsByStatus = Cache::remember('admin.dashboard.appointments_by_status', 300, function () {
        return Appointment::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();
    });

    $monthlyData = Cache::remember('admin.dashboard.monthly_data', 3600, function () {
        return Appointment::select(
                DB::raw('DATE_FORMAT(appointment_date, "%Y-%m") as month'),
                DB::raw('count(*) as count')
            )
            ->where('appointment_date', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    });

    $topPhysicians = Cache::remember('admin.dashboard.top_physicians', 3600, function () {
        return Physician::withCount('appointments')
            ->with('user')
            ->orderByDesc('appointments_count')
            ->take(5)
            ->get();
    });

    return view('admin.dashboard', compact(
        'stats', 'recentAppointments', 'recentPatients',
        'appointmentsByStatus', 'monthlyData', 'topPhysicians'
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
                'patients' => Patient::count(),
                'physicians' => Physician::count(),
                'secretaries' => Secretary::count(),
                'admins' => User::admins()->count(),
            ],
            
            'appointments_by_month' => Appointment::select(
                    DB::raw('DATE_FORMAT(appointment_date, "%Y-%m") as month'),
                    DB::raw('count(*) as count')
                )
                ->where('appointment_date', '>=', now()->subYear())
                ->groupBy('month')
                ->orderBy('month')
                ->get(),
            
            'appointments_by_physician' => Physician::withCount('appointments')
                ->with('user')
                ->having('appointments_count', '>', 0)
                ->orderByDesc('appointments_count')
                ->get(),
            
            'cities_distribution' => City::withCount(['patients', 'physicians', 'secretaries'])
                ->get(),
        ];

        return view('admin.statistics', compact('stats'));
    }
}