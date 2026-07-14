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
        Schema::create('penjualan', function (Blueprint $table) {
            $table->id();
            $table->string('no_transaksi', 20)->unique();
            $table->dateTime('tanggal');
            $table->string('nama_pembeli', 50);
            $table->string('no_hp_pembeli', 15)->nullable();
            $table->decimal('total_harga', 12, 2)->default(0);
            $table->enum('status', ['pesan', 'proses', 'selesai', 'batal'])
                ->default('pesan');
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penjualan');
    }
};
