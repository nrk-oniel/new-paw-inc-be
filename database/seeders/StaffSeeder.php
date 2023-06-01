<?php

namespace Database\Seeders;

use App\Models\Clinic;
use App\Models\Role;
use App\Models\User;
use Database\Factories\ClinicFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Clinic::factory()->count(49)->create();
        for ($i = 0;$i < 49;$i++){
            User::create([
                'username' => 'staff'.$i,
                'email' => 'staff'.$i.'@gmail.com',
                'password' => bcrypt('staff'.$i),
                'role_id' => Role::ROLE_STAFF,
                'clinic_id' => $i+1,
            ]);
        }
    }
}
