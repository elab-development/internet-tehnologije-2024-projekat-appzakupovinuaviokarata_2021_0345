<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('flights', function (Blueprint $table) {
            // add the column only if it doesn't exist (safe reruns)
            if (!Schema::hasColumn('flights', 'carrier_id')) {
                $table->foreignId('carrier_id')
                      ->after('id')
                      ->constrained()
                      ->cascadeOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('flights', function (Blueprint $table) {
            if (Schema::hasColumn('flights', 'carrier_id')) {
                $table->dropForeign(['carrier_id']);
                $table->dropColumn('carrier_id');
            }
        });
    }
};
