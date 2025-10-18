<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('flights', function (Blueprint $table) {
            if (!Schema::hasColumn('flights', 'flight_no')) {
                $table->string('flight_no', 8)->after('airport_to_id');
            }
            if (!Schema::hasColumn('flights', 'dep_time')) {
                $table->dateTime('dep_time')->after('flight_no');
            }
            if (!Schema::hasColumn('flights', 'arr_time')) {
                $table->dateTime('arr_time')->after('dep_time');
            }
            if (!Schema::hasColumn('flights', 'duration_min')) {
                $table->unsignedSmallInteger('duration_min')->after('arr_time');
            }
            if (!Schema::hasColumn('flights', 'stops')) {
                $table->unsignedTinyInteger('stops')->default(0)->after('duration_min');
            }

            // korisni indeksi
            if (!Schema::hasColumn('flights', 'dep_time')) {
                
            }
            $table->index(['airport_from_id', 'airport_to_id', 'dep_time']);
        });
    }

    public function down(): void
    {
        Schema::table('flights', function (Blueprint $table) {
            if (Schema::hasColumn('flights', 'stops')) $table->dropColumn('stops');
            if (Schema::hasColumn('flights', 'duration_min')) $table->dropColumn('duration_min');
            if (Schema::hasColumn('flights', 'arr_time')) $table->dropColumn('arr_time');
            if (Schema::hasColumn('flights', 'dep_time')) $table->dropColumn('dep_time');
            if (Schema::hasColumn('flights', 'flight_no')) $table->dropColumn('flight_no');

        
        });
    }
};
