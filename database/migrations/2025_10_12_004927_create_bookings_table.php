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
        Schema::create('bookings', function (Blueprint $t ) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('flight_id')->constrained()->cascadeOnDelete();
            $t->foreignId('fare_id')->constrained()->cascadeOnDelete();
            $t->unsignedTinyInteger('passengers')->default(1);
            $t->enum('status',['pending','confirmed','cancelled'])->default('pending');
            $t->decimal('total_price',10,2);
            $t->char('currency',3)->default('EUR');
            $t->string('payment_reference')->nullable();
            $t->timestamps();
            $t->index(['user_id','status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
