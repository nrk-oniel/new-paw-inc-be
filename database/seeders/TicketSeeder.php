<?php

namespace Database\Seeders;

use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        for($i = 0; $i<30;$i++){
            $date = Carbon::today()->subDays(rand(0, 365));
            Ticket::create([
                'id' => sprintf("C-%05d",$i+1),
                'clinic_id' => $i+1,
                'schedule_id' => $i+1,
                'pet_type' => Str::random(10),
                'symptoms' => Str::random(10),
                'status' => rand(1,3),
                'status_update_date' => $date->toDateTimeString(),
                'user_id' => 3,
            ]);
        }
    }
}
