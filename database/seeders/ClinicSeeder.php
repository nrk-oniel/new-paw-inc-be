<?php

namespace Database\Seeders;

use App\Models\Clinic;
use Illuminate\Database\Seeder;

class ClinicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Clinic::create([
            'clinic_name' => 'Clinic Staff 1',
            'clinic_address' => 'Jalan Garuda Kencana',
        ]);
    }
}
