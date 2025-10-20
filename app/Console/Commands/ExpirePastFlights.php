<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Flight;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class ExpirePastFlights extends Command
{
    protected $signature = 'flights:expire-past {--dry : Prikaži šta bi se uradilo, bez izmene baze}';
    protected $description = 'Označava sve letove čiji je polazak u prošlosti kao expired';

    public function handle(): int
    {
        $now = Carbon::now();

        $query = Flight::query()
            ->where('departure_time', '<', $now)    
            ->where('status', '!=', 'expired');

        $count = $query->count();

        if ($this->option('dry')) {
            $this->info("DRY RUN: {$count} letova bi bilo označeno kao expired.");
            return self::SUCCESS;
        }

        $updated = $query->update(['status' => 'expired']);

        Log::info("flights:expire-past ran", ['updated' => $updated, 'at' => $now->toDateTimeString()]);
        $this->info("Označeno kao expired: {$updated}");

        return self::SUCCESS;
    }
}
