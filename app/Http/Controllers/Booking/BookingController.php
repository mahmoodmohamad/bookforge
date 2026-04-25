<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Booking, Provider, Client};
use Carbon\Carbon;

class BookingController extends Controller
{
    // List all bookings
    public function index(Request $request)
    {
        $query = Booking::with(['client.user', 'provider.user', 'staff.user']);

        // Filter by status
        if ($status = $request->status) {
            $query->where('status', $status);
        }

        // Filter by date
        if ($date = $request->date) {
            $query->whereDate('booking_date', $date);
        }

        $bookings = $query->latest('booking_date')->paginate(15);

        return view('bookings.index', compact('bookings'));
    }

    // Show create form
    public function create()
    {
        $providers = Provider::with('user')->get();
        $clients = Client::with('user')->get();
        
        return view('bookings.create', compact('providers', 'clients'));
    }

    // Store booking
    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'provider_id' => 'required|exists:providers,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => 'required|date_format:H:i',
        ]);

        // Check availability
        $available = Booking::isAvailable(
            $request->provider_id,
            $request->booking_date,
            $request->booking_time
        );

        if (!$available) {
            return back()
                ->withErrors(['booking_time' => 'This time slot is already booked.'])
                ->withInput();
        }

        // Combine date and time into booking_date
        $bookingDateTime = Carbon::parse($request->booking_date . ' ' . $request->booking_time);

        Booking::create([
            'client_id' => $request->client_id,
            'provider_id' => $request->provider_id,
            'staff_id' => auth()->user()->staff->id ?? null,
            'booking_date' => $bookingDateTime,
            'booking_time' => $request->booking_time,
            'status' => 'scheduled',
            'notes' => $request->notes,
        ]);

        return redirect()->route('bookings.index')
            ->with('success', 'Booking booked successfully!');
    }

    // Show single booking
    public function show(Booking $booking)
    {
        $booking->load(['client.user', 'provider.user', 'staff.user', 'note']);
        
        return view('bookings.show', compact('booking'));
    }

    // Cancel booking
    public function destroy(Booking $booking)
    {
        $booking->update(['status' => 'cancelled']);
        
        return redirect()->route('bookings.index')
            ->with('success', 'Booking cancelled successfully!');
    }

    // ✅ Calendar view - Fixed method name
    public function calendar(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        
        $date = Carbon::create($year, $month, 1);
        $startDate = $date->copy()->startOfMonth()->startOfWeek();
        $endDate = $date->copy()->endOfMonth()->endOfWeek();
        
        // Get bookings for this month
        $bookingsQuery = Booking::with(['client.user', 'provider.user'])
            ->whereBetween('booking_date', [$startDate, $endDate]);

        // Filter by provider if requested
        if ($providerId = $request->get('provider_id')) {
            $bookingsQuery->where('provider_id', $providerId);
        }

        $bookings = $bookingsQuery->get()
            ->groupBy(function($booking) {
                return $booking->booking_date->format('Y-m-d');
            });
        
        // Get providers for filter
        $providers = Provider::with('user')->get();
        
        return view('bookings.calendar', compact('bookings', 'date', 'providers'));
    }
    
    // Get bookings by date (for AJAX)
    public function getBookings(Request $request)
    {
        $date = $request->get('date');
        $providerId = $request->get('provider_id');
        
        $query = Booking::with(['client.user', 'provider.user'])
            ->whereDate('booking_date', $date);
        
        if ($providerId) {
            $query->where('provider_id', $providerId);
        }
        
        $bookings = $query->orderBy('booking_time')->get();
        
        return response()->json($bookings);
    }

    // ✅ NEW: Get available time slots
    public function getAvailableSlots(Request $request)
    {
        $providerId = $request->provider_id;
        $date = $request->date;

        // Define working hours (you can move this to config or database)
        $workingHours = [
            ['start' => '09:00', 'end' => '12:00'],
            ['start' => '14:00', 'end' => '17:00'],
        ];

        // Get booked slots
        $bookedSlots = Booking::where('provider_id', $providerId)
            ->whereDate('booking_date', $date)
            ->where('status', '!=', 'cancelled')
            ->pluck('booking_time')
            ->toArray();

        // Generate available slots
        $availableSlots = [];
        foreach ($workingHours as $hours) {
            $current = Carbon::parse($hours['start']);
            $end = Carbon::parse($hours['end']);

            while ($current < $end) {
                $timeSlot = $current->format('H:i');
                if (!in_array($timeSlot, $bookedSlots)) {
                    $availableSlots[] = $timeSlot;
                }
                $current->addMinutes(30); // 30-minute slots
            }
        }

        return response()->json($availableSlots);
    }
}