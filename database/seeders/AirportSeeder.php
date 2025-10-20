<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Airport;

class AirportSeeder extends Seeder
{
    public function run(): void
    {
        // some random plus true data 
        $preset = [
            ['iata'=>'BEG','name'=>'Nikola Tesla','city'=>'Belgrade','country'=>'Serbia'],
            ['iata'=>'AMS','name'=>'Schiphol','city'=>'Amsterdam','country'=>'Netherlands'],
            ['iata'=>'FRA','name'=>'Frankfurt','city'=>'Frankfurt','country'=>'Germany'],
        ];
        foreach ($preset as $a) { Airport::firstOrCreate(['iata'=>$a['iata']], $a); }
        Airport::factory()->count(20)->create();
    }
}
