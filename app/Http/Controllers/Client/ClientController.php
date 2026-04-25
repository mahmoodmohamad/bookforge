<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\{Client, User, City};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    /**
     * Display a listing of clients
     */
    public function index(Request $request)
    {
        $query = Client::with(['user', 'city', 'staff.user']);

        // Search by name, national_id, or phone
        if ($search = $request->search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($userQuery) use ($search) {
                    $userQuery->where('name', 'LIKE', "%{$search}%")
                             ->orWhere('email', 'LIKE', "%{$search}%");
                })
                ->orWhere('national_id', 'LIKE', "%{$search}%")
                ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        // Filter by city
        if ($cityId = $request->city_id) {
            $query->where('city_id', $cityId);
        }

        $clients = $query->latest()->paginate(15);
        $cities = City::all();

        return view('clients.index', compact('clients', 'cities'));
    }

    /**
     * Show the form for creating a new client
     */
    public function create()
    {
        $cities = City::all();
        return view('clients.create', compact('cities'));
    }

    /**
     * Store a newly created client
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'national_id' => 'required|string|unique:clients,national_id',
            'phone' => 'required|string',
            'city_id' => 'required|exists:cities,id',
        ]);

        DB::beginTransaction();
        try {
            // Create user account
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'activation' => true,
            ]);

            // Create client profile
            $client = Client::create([
                'user_id' => $user->id,
                'national_id' => $request->national_id,
                'phone' => $request->phone,
                'city_id' => $request->city_id,
                'staff_id' => auth()->user()->staff->id ?? null,
            ]);

            DB::commit();

            return redirect()->route('clients.index')
                ->with('success', 'Client registered successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to register client. Please try again.'])
                ->withInput();
        }
    }

    /**
     * Display the specified client
     */
    public function show(Client $client)
{
    // Load basic client relations
    $client->load([
        'user',
        'city.country',
        'staff.user',
        'bookings.provider.user',
        'bookings.note',
    ]);

    /**
     * =========================
     * 📊 Statistics
     * =========================
     */
    $stats = [
        'total_bookings' => $client->bookings()->count(),

        'upcoming_bookings' => $client->bookings()
            ->where('booking_date', '>', now())
            ->where('status', 'scheduled')
            ->count(),

        'completed_bookings' => $client->bookings()
            ->where('status', 'completed')
            ->count(),

        // via hasManyThrough (Client -> Booking -> Note)
        'total_diagnoses' => $client->diagnoses()->count(),
    ];

    /**
     * =========================
     * 📅 Upcoming Bookings
     * =========================
     */
    $upcomingBookings = $client->bookings()
        ->with(['provider.user'])
        ->where('booking_date', '>', now())
        ->where('status', 'scheduled')
        ->orderBy('booking_date')
        ->get();

    /**
     * =========================
     * 🩺 Recent Diagnoses
     * =========================
     * Note -> Booking -> Provider -> User
     */
    $recentDiagnoses = $client->diagnoses()
        ->with(['booking.provider.user'])
        ->latest()
        ->take(5)
        ->get();

    return view('clients.show', compact(
        'client',
        'stats',
        'upcomingBookings',
        'recentDiagnoses'
    ));
}

    /**
     * Show client's complete medical history
     */
    public function medicalHistory(Client $client)
    {
        $client->load(['user', 'city']);

        $bookings = $client->bookings()
            ->with(['provider.user', 'note'])
            ->orderByDesc('booking_date')
            ->paginate(10);

        return view('clients.medical-history', compact('client', 'bookings'));
    }

    /**
     * Show the form for editing the client
     */
   public function edit(Client $client)
{
    $cities = City::all();
    $secretaries = \App\Models\Staff::with('user')->get(); // جلب كل السكرتير مع المستخدم

    return view('clients.edit', compact('client', 'cities', 'secretaries'));
}


    /**
     * Update the specified client
     */
    public function update(Request $request, Client $client)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $client->user_id,
            'national_id' => 'required|string|unique:clients,national_id,' . $client->id,
            'phone' => 'required|string',
            'city_id' => 'required|exists:cities,id',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        DB::beginTransaction();
        try {
            // Update user
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $client->user()->update($userData);

            // Update client
            $client->update([
                'national_id' => $request->national_id,
                'phone' => $request->phone,
                'city_id' => $request->city_id,
            ]);

            DB::commit();

            return redirect()->route('clients.show', $client)
                ->with('success', 'Client updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to update client. Please try again.'])
                ->withInput();
        }
    }
}