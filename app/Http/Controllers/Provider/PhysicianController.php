<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\{Booking, Note, Client};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProviderController extends Controller
{


    /**
     * View all bookings
     */
    public function bookings(Request $request)
    {
        $provider = auth()->user()->provider;

        $query = $provider->bookings()
            ->with(['client.user', 'note']);

        // Filter by status
        if ($status = $request->status) {
            $query->where('status', $status);
        }

        // Filter by date
        if ($date = $request->date) {
            $query->whereDate('booking_date', $date);
        }

        // Filter: today, upcoming, past
        if ($filter = $request->filter) {
            switch ($filter) {
                case 'today':
                    $query->whereDate('booking_date', today());
                    break;
                case 'upcoming':
                    $query->where('booking_date', '>', now());
                    break;
                case 'past':
                    $query->where('booking_date', '<', now());
                    break;
            }
        }

        $bookings = $query->orderByDesc('booking_date')
            ->orderByDesc('booking_time')
            ->paginate(15);

        return view('provider.bookings.index', compact('bookings'));
    }

    /**
     * View single booking
     */
    public function showBooking(Booking $booking)
    {
        

        $booking->load(['client.user', 'client.city', 'note']);

        return view('provider.bookings.show', compact('booking'));
    }


    /**
     * Show note form
     */
    public function createNote(Booking $booking)
    {
        

        if ($booking->note) {
            return redirect()
                ->route('provider.bookings.show', $booking)
                ->with('error', 'This booking already has a note.');
        }

        return view('provider.note.create', compact('booking'));
    }


    /**
     * Store note
     */
    public function storeNote(Request $request, Booking $booking)
    {
        $this->authorize('update', $booking);

        if ($booking->note) {
            return redirect()->back()->with('error', 'Note already exists');
        }

        $request->validate([
            'symptoms' => 'required|string',
            'note' => 'required|string',
            'prescription' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $booking) {
            Note::create([
                'booking_id' => $booking->id,
                'client_id' => $booking->client_id,
                'provider_id' => $booking->provider_id,
                'symptoms' => $request->symptoms,
                'note' => $request->note,
                'prescription' => $request->prescription,
                'notes' => $request->notes,
            ]);

            $booking->update(['status' => 'completed']);
        });

        return redirect()
            ->route('provider.bookings.show', $booking)
            ->with('success', 'Note saved successfully!');
    }


    /**
     * View client medical history
     */
    public function clientHistory(Client $client)
    {
        $provider = auth()->user()->provider;

        // Get all bookings for this client with this provider
        $bookings = $client->bookings()
            ->where('provider_id', $provider->id)
            ->with(['note'])
            ->orderByDesc('booking_date')
            ->get();

        $client->load(['user', 'city']);

        return view('provider.clients.history', compact('client', 'bookings'));
    }

    /**
     * Edit note
     */
    public function editNote(Booking $booking)
    {
        $this->authorize('update', $booking);

        if (!$booking->note) {
            return redirect()
                ->route('provider.bookings.show', $booking)
                ->with('error', 'No note found for this booking.');
        }

        $booking->load(['client.user', 'note']);

        return view('provider.note.edit', compact('booking'));
    }


    /**
     * Update note
     */
    public function updateNote(Request $request, Booking $booking)
    {
        $this->authorize('update', $booking);

        if (!$booking->note) {
            return redirect()
                ->route('provider.bookings.show', $booking)
                ->with('error', 'No note found.');
        }

        $request->validate([
            'symptoms' => 'required|string',
            'note' => 'required|string',
            'prescription' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $booking->note->update($request->only([
            'symptoms',
            'note',
            'prescription',
            'notes',
        ]));

        return redirect()
            ->route('provider.bookings.show', $booking)
            ->with('success', 'Note updated successfully!');
    }
}
