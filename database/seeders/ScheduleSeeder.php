<?php

namespace Database\Seeders;

use App\Models\Schedule;
use Faker\Guesser\Name;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($j = 2023; $j<=2025;$j++){
            for($i = 1; $i <= 12; $i++) {
                for($k = 0; $k < 35; $k++) {
                    if($k%2==0){
                        continue;
                    }
                    Schedule::create([
                        'year' => $j,
                        'month' => $i,
                        'date' => $k,
                        'hour' => rand(1,23),
                        'minute' => rand(0,59),
                        'doctor_name' => Str::random(30),
                        'clinic_id' => rand(1,50),
                    ]);
                }
            }
        }
    }
}
