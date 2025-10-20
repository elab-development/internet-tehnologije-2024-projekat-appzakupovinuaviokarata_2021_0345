<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('flights', function (Blueprint $t) {
            $t->unsignedSmallInteger('seats_total')->default(180)->after('stops');
        });
    }
    public function down(): void {
        Schema::table('flights', function (Blueprint $t) {
            $t->dropColumn('seats_total');
        });
    }
};