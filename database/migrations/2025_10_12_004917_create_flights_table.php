<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('flights', function (Blueprint $t ) {
            $t->id();
            $t->foreignId('carrier_id')->constrained()->cascadeOnDelete();
            $t->foreignId('airport_from_id')->constrained('airports')->cascadeOnDelete();
            $t->foreignId('airport_to_id')->constrained('airports')->cascadeOnDelete();
            $t->string('flight_no',8);
            $t->dateTime('dep_time');
            $t->dateTime('arr_time');
            $t->unsignedSmallInteger('duration_min');
            $t->unsignedTinyInteger('stops')->default(0);
            $t->timestamps();
            $t->index(['airport_from_id','airport_to_id','dep_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flights');
    }
};
