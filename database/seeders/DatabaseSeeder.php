<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Admin;
use App\Models\Tenant;
use App\Models\Country;
use App\Models\City;
use App\Models\Staff;
use App\Models\Provider;
use App\Models\Client;
use App\Models\Booking;
use App\Models\Note;

class DatabaseSeeder extends Seeder
{
    // Tenants will be stored here so other methods can reference them
    private array $tenants = [];

    public function run(): void
    {
        $this->command->info('');
        $this->command->info('🌱 Starting database seeding...');
        $this->command->info('');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $this->cleanDatabase();
        $this->seedLocations();
        $this->seedTenants();    // ← must come before users
        $this->seedAdmin();
        $this->seedStaff();
        $this->seedProviders();
        $this->seedClients();
        $this->seedBookings();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->printSummary();
    }

    // =========================================================
    //  CLEAN
    // =========================================================

    private function cleanDatabase(): void
    {
        $this->command->info('🧹 Cleaning old data...');

        Note::truncate();
        Booking::truncate();
        Client::truncate();
        Provider::truncate();
        Staff::truncate();
        Admin::truncate();
        User::truncate();
        Tenant::truncate();     // ← add this
        City::truncate();
        Country::truncate();

        $this->command->info('✓ Database cleaned');
    }

    // =========================================================
    //  LOCATIONS
    // =========================================================

    private function seedLocations(): void
    {
        $this->command->info('📍 Seeding locations...');

        $locations = [
            'United States'  => ['New York', 'Los Angeles', 'Chicago', 'Houston', 'Phoenix'],
            'Egypt'          => ['Cairo', 'Alexandria', 'Giza', 'Luxor', 'Aswan'],
            'United Kingdom' => ['London', 'Manchester', 'Birmingham', 'Leeds', 'Glasgow'],
            'Saudi Arabia'   => ['Riyadh', 'Jeddah', 'Dammam', 'Mecca', 'Medina'],
            'UAE'            => ['Dubai', 'Abu Dhabi', 'Sharjah', 'Ajman', 'Al Ain'],
        ];

        foreach ($locations as $countryName => $cities) {
            $country = Country::create(['name' => $countryName]);
            foreach ($cities as $cityName) {
                City::create(['name' => $cityName, 'country_id' => $country->id]);
            }
        }

        $this->command->info('✓ ' . Country::count() . ' countries, ' . City::count() . ' cities created');
    }

    // =========================================================
    //  TENANTS  ← NEW
    // =========================================================

    private function seedTenants(): void
    {
        $this->command->info('🏢 Seeding tenants...');

        $tenantData = [
            [
                'name'          => 'City Medical Clinic',
                'slug'          => 'city-clinic',
                'business_type' => 'healthcare',
                'primary_color' => '#3B82F6',
                'config'        => [
                    'provider_label'  => 'Physician',
                    'client_label'    => 'Patient',
                    'staff_label'     => 'Secretary',
                    'booking_label'   => 'Appointment',
                    'note_label'      => 'Diagnosis',
                    'features'        => ['diagnosis', 'prescriptions', 'medical_history'],
                ],
            ],
            [
                'name'          => 'Glow Beauty Salon',
                'slug'          => 'glow-salon',
                'business_type' => 'salon',
                'primary_color' => '#EC4899',
                'config'        => [
                    'provider_label'  => 'Stylist',
                    'client_label'    => 'Client',
                    'staff_label'     => 'Receptionist',
                    'booking_label'   => 'Appointment',
                    'note_label'      => 'Session Notes',
                    'features'        => ['service_menu', 'style_history'],
                ],
            ],
            [
                'name'          => 'FitLife Gym',
                'slug'          => 'fitlife-gym',
                'business_type' => 'gym',
                'primary_color' => '#10B981',
                'config'        => [
                    'provider_label'  => 'Trainer',
                    'client_label'    => 'Member',
                    'staff_label'     => 'Receptionist',
                    'booking_label'   => 'Session',
                    'note_label'      => 'Progress Notes',
                    'features'        => ['workout_plans', 'progress_tracking'],
                ],
            ],
            [
                'name'          => 'Justice Legal Firm',
                'slug'          => 'justice-legal',
                'business_type' => 'legal',
                'primary_color' => '#6366F1',
                'config'        => [
                    'provider_label'  => 'Lawyer',
                    'client_label'    => 'Client',
                    'staff_label'     => 'Paralegal',
                    'booking_label'   => 'Consultation',
                    'note_label'      => 'Case Notes',
                    'features'        => ['case_management', 'document_storage'],
                ],
            ],
        ];

        foreach ($tenantData as $data) {
            $tenant = Tenant::create([
                'name'          => $data['name'],
                'slug'          => $data['slug'],
                'business_type' => $data['business_type'],
                'primary_color' => $data['primary_color'],
                'config'        => $data['config'],
                'active'        => true,
            ]);

            // Store by slug so other methods can look them up easily
            $this->tenants[$data['slug']] = $tenant;

            $this->command->info("   ✓ {$tenant->name} ({$tenant->business_type})");
        }

        $this->command->info('✓ ' . Tenant::count() . ' tenants created');
    }

    // =========================================================
    //  ADMIN  (global — no tenant)
    // =========================================================

    private function seedAdmin(): void
    {
        $this->command->info('👑 Seeding admins...');

        $admins = [
            ['name' => 'System Administrator', 'email' => 'admin@app.com'],
            ['name' => 'Super Admin',           'email' => 'superadmin@app.com'],
        ];

        foreach ($admins as $data) {
            $user = User::create([
                'name'       => $data['name'],
                'email'      => $data['email'],
                'password'   => Hash::make('password'),
                'activation' => true,
            ]);

            Admin::create(['user_id' => $user->id]);
        }

        $this->command->info('✓ ' . Admin::count() . ' admins created');
    }

    // =========================================================
    //  STAFF  — distributed across tenants
    // =========================================================

    private function seedStaff(): void
    {
        $this->command->info('📋 Seeding staff...');

        // Each tenant gets at least one staff member
        $staffList = [
            ['name' => 'Sarah Johnson', 'email' => 'sarah@app.com',   'phone' => '555-0101', 'tenant' => 'city-clinic'],
            ['name' => 'Emily Davis',   'email' => 'emily@app.com',    'phone' => '555-0102', 'tenant' => 'city-clinic'],
            ['name' => 'Michael Brown', 'email' => 'michael@app.com',  'phone' => '555-0103', 'tenant' => 'glow-salon'],
            ['name' => 'Laura Wilson',  'email' => 'laura@app.com',    'phone' => '555-0104', 'tenant' => 'fitlife-gym'],
            ['name' => 'Chris Taylor',  'email' => 'chris@app.com',    'phone' => '555-0105', 'tenant' => 'justice-legal'],
        ];

        foreach ($staffList as $data) {
            $user = User::create([
                'name'       => $data['name'],
                'email'      => $data['email'],
                'password'   => Hash::make('password'),
                'activation' => true,
            ]);

            Staff::create([
                'user_id'   => $user->id,
                'phone'     => $data['phone'],
                'city_id'   => City::inRandomOrder()->first()->id,
                'tenant_id' => $this->tenants[$data['tenant']]->id,   // ← tenant scoped
            ]);
        }

        $this->command->info('✓ ' . Staff::count() . ' staff created');
    }

    // =========================================================
    //  PROVIDERS  — distributed across tenants
    // =========================================================

    private function seedProviders(): void
    {
        $this->command->info('🧑‍💼 Seeding providers...');

        $providers = [
            // Healthcare
            ['name' => 'Dr. Alice Williams',    'email' => 'alice@app.com',    'service' => 'Cardiology',        'phone' => '555-1001', 'tenant' => 'city-clinic'],
            ['name' => 'Dr. Robert Smith',      'email' => 'robert@app.com',   'service' => 'Neurology',         'phone' => '555-1002', 'tenant' => 'city-clinic'],
            ['name' => 'Dr. Jennifer Martinez', 'email' => 'jennifer@app.com', 'service' => 'Pediatrics',        'phone' => '555-1003', 'tenant' => 'city-clinic'],
            // Salon
            ['name' => 'Mona Hassan',           'email' => 'mona@app.com',     'service' => 'Hair Styling',      'phone' => '555-1004', 'tenant' => 'glow-salon'],
            ['name' => 'Layla Ahmed',           'email' => 'layla@app.com',    'service' => 'Skin Care',         'phone' => '555-1005', 'tenant' => 'glow-salon'],
            // Gym
            ['name' => 'Jake Miller',           'email' => 'jake@app.com',     'service' => 'Personal Training', 'phone' => '555-1006', 'tenant' => 'fitlife-gym'],
            ['name' => 'Sara Connor',           'email' => 'sara@app.com',     'service' => 'Yoga & Pilates',    'phone' => '555-1007', 'tenant' => 'fitlife-gym'],
            // Legal
            ['name' => 'James Knight',          'email' => 'jamesk@app.com',   'service' => 'Corporate Law',     'phone' => '555-1008', 'tenant' => 'justice-legal'],
        ];

        foreach ($providers as $data) {
            $user = User::create([
                'name'       => $data['name'],
                'email'      => $data['email'],
                'password'   => Hash::make('password'),
                'activation' => true,
            ]);

            Provider::create([
                'user_id'        => $user->id,
                'specialization' => $data['service'],
                'phone'          => $data['phone'],
                'city_id'        => City::inRandomOrder()->first()->id,
                'tenant_id'      => $this->tenants[$data['tenant']]->id,   // ← tenant scoped
            ]);
        }

        $this->command->info('✓ ' . Provider::count() . ' providers created');
    }

    // =========================================================
    //  CLIENTS  — distributed across tenants
    // =========================================================

    private function seedClients(): void
    {
        $this->command->info('👥 Seeding clients...');

        $clients = [
            ['name' => 'Jane Doe',           'email' => 'jane@app.com',      'phone' => '555-2001', 'national_id' => 'CLI001', 'tenant' => 'city-clinic'],
            ['name' => 'John Smith',          'email' => 'john@app.com',      'phone' => '555-2002', 'national_id' => 'CLI002', 'tenant' => 'city-clinic'],
            ['name' => 'Emma Wilson',         'email' => 'emma@app.com',      'phone' => '555-2003', 'national_id' => 'CLI003', 'tenant' => 'city-clinic'],
            ['name' => 'Michael Johnson',     'email' => 'michaelj@app.com',  'phone' => '555-2004', 'national_id' => 'CLI004', 'tenant' => 'city-clinic'],
            ['name' => 'Sophia Brown',        'email' => 'sophia@app.com',    'phone' => '555-2005', 'national_id' => 'CLI005', 'tenant' => 'city-clinic'],
            ['name' => 'Olivia Martinez',     'email' => 'olivia@app.com',    'phone' => '555-2007', 'national_id' => 'CLI007', 'tenant' => 'glow-salon'],
            ['name' => 'James Garcia',        'email' => 'jamesg@app.com',    'phone' => '555-2008', 'national_id' => 'CLI008', 'tenant' => 'glow-salon'],
            ['name' => 'Isabella Rodriguez',  'email' => 'isabella@app.com',  'phone' => '555-2009', 'national_id' => 'CLI009', 'tenant' => 'glow-salon'],
            ['name' => 'Benjamin Lee',        'email' => 'benjamin@app.com',  'phone' => '555-2010', 'national_id' => 'CLI010', 'tenant' => 'fitlife-gym'],
            ['name' => 'Mia Anderson',        'email' => 'mia@app.com',       'phone' => '555-2011', 'national_id' => 'CLI011', 'tenant' => 'fitlife-gym'],
            ['name' => 'Lucas Taylor',        'email' => 'lucas@app.com',     'phone' => '555-2012', 'national_id' => 'CLI012', 'tenant' => 'fitlife-gym'],
            ['name' => 'Charlotte Thomas',    'email' => 'charlotte@app.com', 'phone' => '555-2013', 'national_id' => 'CLI013', 'tenant' => 'justice-legal'],
            ['name' => 'Henry Moore',         'email' => 'henry@app.com',     'phone' => '555-2014', 'national_id' => 'CLI014', 'tenant' => 'justice-legal'],
            ['name' => 'Amelia Jackson',      'email' => 'amelia@app.com',    'phone' => '555-2015', 'national_id' => 'CLI015', 'tenant' => 'justice-legal'],
            ['name' => 'Noah White',          'email' => 'noah@app.com',      'phone' => '555-2016', 'national_id' => 'CLI016', 'tenant' => 'city-clinic'],
            ['name' => 'Ava Harris',          'email' => 'ava@app.com',       'phone' => '555-2017', 'national_id' => 'CLI017', 'tenant' => 'glow-salon'],
            ['name' => 'Liam Martin',         'email' => 'liam@app.com',      'phone' => '555-2018', 'national_id' => 'CLI018', 'tenant' => 'fitlife-gym'],
            ['name' => 'Grace Thompson',      'email' => 'grace@app.com',     'phone' => '555-2019', 'national_id' => 'CLI019', 'tenant' => 'justice-legal'],
            ['name' => 'Ethan Clark',         'email' => 'ethan@app.com',     'phone' => '555-2020', 'national_id' => 'CLI020', 'tenant' => 'city-clinic'],
            ['name' => 'Zoe Baker',           'email' => 'zoe@app.com',       'phone' => '555-2021', 'national_id' => 'CLI021', 'tenant' => 'glow-salon'],
        ];

        foreach ($clients as $data) {
            $tenantId = $this->tenants[$data['tenant']]->id;

            // Pick a staff member from the same tenant
            $staffId = Staff::where('tenant_id', $tenantId)->inRandomOrder()->first()?->id;

            $user = User::create([
                'name'       => $data['name'],
                'email'      => $data['email'],
                'password'   => Hash::make('password'),
                'activation' => true,
            ]);

            Client::create([
                'user_id'     => $user->id,
                'national_id' => $data['national_id'],
                'phone'       => $data['phone'],
                'gender'      => rand(0, 1) ? 'male' : 'female',
                'birth_date'  => now()->subYears(rand(18, 65))->subDays(rand(0, 365)),
                'city_id'     => City::inRandomOrder()->first()->id,
                'staff_id'    => $staffId,
                'tenant_id'   => $tenantId,   // ← tenant scoped
            ]);
        }

        $this->command->info('✓ ' . Client::count() . ' clients created');
    }

    // =========================================================
    //  BOOKINGS + NOTES
    // =========================================================

    private function seedBookings(): void
    {
        $this->command->info('📅 Seeding bookings & notes...');

        $bookingCount = 0;
        $noteCount    = 0;

        // Iterate per tenant so bookings never mix across tenants
        foreach ($this->tenants as $slug => $tenant) {
            $clients   = Client::where('tenant_id', $tenant->id)->get();
            $providers = Provider::where('tenant_id', $tenant->id)->get();
            $staffList = Staff::where('tenant_id', $tenant->id)->get();

            if ($clients->isEmpty() || $providers->isEmpty()) continue;

            foreach ($clients as $client) {
                $numBookings = rand(2, 5);

                for ($i = 0; $i < $numBookings; $i++) {
                    $provider = $providers->random();
                    $staff    = $staffList->isNotEmpty() ? $staffList->random() : null;

                    $daysOffset = match(true) {
                        $i === 0 => -rand(14, 60),
                        $i === 1 => -rand(1, 13),
                        $i === 2 => 0,
                        default  => rand(1, 21),
                    };

                    $bookingDate = now()->addDays($daysOffset)->format('Y-m-d');
                    $bookingTime = sprintf('%02d:00', rand(9, 16));

                    $status = match(true) {
                        $daysOffset < -1 => 'completed',
                        $daysOffset === 0 => rand(0, 1) ? 'completed' : 'scheduled',
                        $daysOffset > 0  => 'scheduled',
                        default          => rand(0, 8) === 0 ? 'cancelled' : 'completed',
                    };

                    $booking = Booking::create([
                        'client_id'    => $client->id,
                        'provider_id'  => $provider->id,
                        'staff_id'     => $staff?->id,
                        'tenant_id'    => $tenant->id,   // ← tenant scoped
                        'booking_date' => $bookingDate . ' ' . $bookingTime,
                        'booking_time' => $bookingTime,
                        'status'       => $status,
                        'notes'        => $this->randomBookingNote(),
                    ]);

                    $bookingCount++;

                    if ($status === 'completed' && rand(1, 10) <= 8) {
                        $noteData = $this->randomNote();

                        Note::create([
                            'booking_id'   => $booking->id,
                            'symptoms'     => $noteData['symptoms'],
                            'prescription' => $noteData['prescription'],
                            'notes'        => $noteData['notes'],
                            'tenant_id'    => $tenant->id,
                        ]);

                        $noteCount++;
                    }
                }
            }
        }

        $this->command->info("✓ {$bookingCount} bookings created");
        $this->command->info("✓ {$noteCount} notes created");
    }

    // =========================================================
    //  HELPERS
    // =========================================================

    private function randomBookingNote(): string
    {
        return collect([
            'Regular checkup',
            'Follow-up visit',
            'Urgent consultation requested',
            'Annual screening',
            'Post-treatment evaluation',
            'New client consultation',
            'Routine examination',
            'Client experiencing symptoms',
            'Referred by another provider',
            'Second opinion requested',
        ])->random();
    }

    private function randomNote(): array
    {
        return collect([
            [
                'symptoms'     => 'Persistent headache, fatigue, and mild fever for 3 days.',
                'diagnosis'    => 'Upper respiratory tract infection. Mild dehydration noted.',
                'prescription' => "1. Paracetamol 500mg every 6 hours\n2. Increase fluids to 2L/day\n3. Rest 2-3 days",
                'notes'        => 'Return if fever persists beyond 3 days.',
            ],
            [
                'symptoms'     => 'Acute lower back pain after heavy lifting.',
                'diagnosis'    => 'Lumbar muscle strain. No nerve compression.',
                'prescription' => "1. Ibuprofen 400mg three times daily\n2. Hot compress 3x daily\n3. Rest 48 hours",
                'notes'        => 'Avoid heavy lifting for 2 weeks.',
            ],
            [
                'symptoms'     => 'Dry cough, mild chest tightness, no fever.',
                'diagnosis'    => 'Allergic bronchitis. O2 saturation 98%.',
                'prescription' => "1. Cetirizine 10mg once daily\n2. Steam inhalation twice daily",
                'notes'        => 'Follow-up in 1 week if symptoms persist.',
            ],
            [
                'symptoms'     => 'Severe sore throat, difficulty swallowing, fever 38.5°C.',
                'diagnosis'    => 'Acute bacterial tonsillitis.',
                'prescription' => "1. Amoxicillin 500mg 3x daily for 7 days\n2. Warm salt water gargles",
                'notes'        => 'Complete the full antibiotic course.',
            ],
            [
                'symptoms'     => 'Intermittent abdominal pain, bloating after meals.',
                'diagnosis'    => 'Functional dyspepsia. Stress-induced gastritis.',
                'prescription' => "1. Omeprazole 20mg before breakfast\n2. Small frequent meals",
                'notes'        => 'Consider stress counseling.',
            ],
        ])->random();
    }

    // =========================================================
    //  SUMMARY
    // =========================================================

    private function printSummary(): void
    {
        $this->command->info('');
        $this->command->info('✅ Database seeded successfully!');
        $this->command->info('');
        $this->command->info('📊 Summary:');
        $this->command->info('   Countries : ' . Country::count());
        $this->command->info('   Cities    : ' . City::count());
        $this->command->info('   Tenants   : ' . Tenant::count());
        $this->command->info('   Admins    : ' . Admin::count());
        $this->command->info('   Staff     : ' . Staff::count());
        $this->command->info('   Providers : ' . Provider::count());
        $this->command->info('   Clients   : ' . Client::count());
        $this->command->info('   Bookings  : ' . Booking::count());
        $this->command->info('   Notes     : ' . Note::count());
        $this->command->info('');
        $this->command->info('🔑 Demo Accounts (password: password)');
        $this->command->info('   Admin           : admin@app.com');
        $this->command->info('   Provider/Clinic : alice@app.com     (City Medical Clinic)');
        $this->command->info('   Provider/Salon  : mona@app.com      (Glow Beauty Salon)');
        $this->command->info('   Provider/Gym    : jake@app.com      (FitLife Gym)');
        $this->command->info('   Provider/Legal  : jamesk@app.com    (Justice Legal Firm)');
        $this->command->info('   Staff/Clinic    : sarah@app.com     (City Medical Clinic)');
        $this->command->info('   Client/Clinic   : jane@app.com      (City Medical Clinic)');
        $this->command->info('');

        // Per-tenant breakdown
        $this->command->info('🏢 Per-Tenant Breakdown:');
        foreach ($this->tenants as $slug => $tenant) {
            $this->command->info("   {$tenant->name}:");
            $this->command->info('      Providers : ' . Provider::where('tenant_id', $tenant->id)->count());
            $this->command->info('      Staff     : ' . Staff::where('tenant_id', $tenant->id)->count());
            $this->command->info('      Clients   : ' . Client::where('tenant_id', $tenant->id)->count());
            $this->command->info('      Bookings  : ' . Booking::where('tenant_id', $tenant->id)->count());
        }
        $this->command->info('');
    }
}