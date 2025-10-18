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
        Schema::create('fares', function (Blueprint $t ) {
            $t->id();
            $t->foreignId('flight_id')->constrained()->cascadeOnDelete();
            $t->enum('cabin_class',['ECONOMY','BUSINESS','FIRST'])->default('ECONOMY');
            $t->decimal('price',10,2);
            $t->char('currency',3)->default('EUR');
            $t->unsignedSmallInteger('available_seats')->default(9);
            $t->json('rules')->nullable();
            $t->timestamps();
            $t->index(['cabin_class','price']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fares');
    }
};
