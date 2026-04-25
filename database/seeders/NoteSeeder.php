<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Booking;
use App\Models\Note;

class NoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
		$bookings = Booking::take(3)->get();

        foreach ($bookings as $booking) {
            $booking->update(['status' => 'completed']);

            Note::create([
                'booking_id' => $booking->id,
                'description' => 'General examination completed',
                'prescription' => 'Vitamin C + Rest',
            ]);
        }
    }
}
