<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\City;
use App\Models\User;
use App\Models\Provider;
use App\Models\Client;
use App\Models\Staff;
use Carbon\Carbon;
use App\Models\Booking;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
		$provider = Provider::first();
        $staff = Staff::first();
        $clients = Client::all();

        $startTime = Carbon::createFromTime(10, 0);

        foreach ($clients as $index => $client) {

            Booking::create([
                'client_id' => $client->id,
                'provider_id' => $provider->id,
                'staff_id' => $staff->id,
                'booking_date' => now()->addDays(1),
                'booking_time' => $startTime->copy()->addMinutes($index * 30),
                'status' => 'scheduled',
                'notes' => 'Initial visit',
            ]);
        }
    }
}
