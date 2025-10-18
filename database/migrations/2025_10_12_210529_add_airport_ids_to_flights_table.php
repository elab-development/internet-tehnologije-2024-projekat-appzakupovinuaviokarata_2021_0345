<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('flights', function (Blueprint $table) {
            if (!Schema::hasColumn('flights', 'airport_from_id')) {
                $table->foreignId('airport_from_id')
                      ->after('carrier_id')
                      ->constrained('airports')
                      ->cascadeOnDelete();
            }
            if (!Schema::hasColumn('flights', 'airport_to_id')) {
                $table->foreignId('airport_to_id')
                      ->after('airport_from_id')
                      ->constrained('airports')
                      ->cascadeOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('flights', function (Blueprint $table) {
            if (Schema::hasColumn('flights', 'airport_from_id')) {
                $table->dropForeign(['airport_from_id']);
                $table->dropColumn('airport_from_id');
            }
            if (Schema::hasColumn('flights', 'airport_to_id')) {
                $table->dropForeign(['airport_to_id']);
                $table->dropColumn('airport_to_id');
            }
        });
    }
};
