<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\City;
use App\Models\User;
use App\Models\Provider;
use App\Models\Staff;
use Illuminate\Support\Facades\Hash;
class StaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
		$city = City::first();
        $provider = Provider::first();

        $user = User::create([
            'name' => 'Clinic Staff',
            'email' => 'staff@clinic.com',
            'password' => Hash::make('password'),
            'activation' => true,
        ]);

        Staff::create([
            'user_id' => $user->id,
            'phone' => '01000000002',
            'provider_id' => $provider->id,
            'city_id' => $city->id,
        ]);
    }
}
