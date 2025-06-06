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
        Schema::create('pemesanans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_booking', 50)->unique();
            $table->foreignId('kamar_id')->constrained('kamars');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->string('nama_pemesan');
            $table->date('tanggal_lahir');
            $table->date('tanggal_checkin');
            $table->date('tanggal_checkout');
            $table->integer('jumlah_tamu');
            $table->string('nomor_hp', 20);
            $table->string('email');
            $table->enum('sumber', ['online', 'walkin'])->default('online');
            $table->enum('status', ['belum_bayar', 'lunas', 'checkin', 'checkout']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemesanans');
    }
};
