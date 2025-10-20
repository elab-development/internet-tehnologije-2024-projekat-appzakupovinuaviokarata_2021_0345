<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Carrier;

class CarrierSeeder extends Seeder
{
    public function run(): void
    {
        // Preset (sigurno ubaci, više puta bez greške)
        $preset = [
            ['code' => 'JU', 'name' => 'Air Serbia'],
            ['code' => 'KL', 'name' => 'KLM'],
            ['code' => 'LH', 'name' => 'Lufthansa'],
        ];

        foreach ($preset as $c) {
            Carrier::updateOrCreate(
                ['code' => strtoupper($c['code'])],
                ['name' => $c['name']]
            );
        }

        // Random 5 
        Carrier::factory()->count(5)->make()->each(function ($c) {
            $code = strtoupper($c->code);
            // ako se poklopi, generiši novi dvoslovni kpd dok ne bude slobodan
            while (Carrier::where('code', $code)->exists()) {
                $code = Str::upper(Str::replaceArray('?', [mt_rand(65,90), mt_rand(65,90)], '??'));
                // ??
            }

            Carrier::firstOrCreate(
                ['code' => $code],
                ['name' => $c->name]
            );
        });
    }
}