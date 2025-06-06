<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('pemesanans', function (Blueprint $table) {
            $table->decimal('total_harga', 12, 2)->after('status')->nullable();
        });
    }

    public function down(): void {
        Schema::table('pemesanans', function (Blueprint $table) {
            $table->dropColumn('total_harga');
        });
    }
};
