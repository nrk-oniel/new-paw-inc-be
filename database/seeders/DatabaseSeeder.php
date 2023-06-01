<?php

namespace Database\Seeders;

use App\Models\Clinic;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            RoleSeeder::class,
            ClinicSeeder::class,
            UserSeeder::class,
            StaffSeeder::class,
            ScheduleSeeder::class,
            TicketSeeder::class,
        ]);
    }
}
