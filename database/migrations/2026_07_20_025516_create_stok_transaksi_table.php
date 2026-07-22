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
        Schema::create('stok_transaksi', function (Blueprint $table) {
            $table->id();
              $table->foreignId('bahan_baku_id')
                ->constrained('bahan_baku')
                ->cascadeOnDelete();
            $table->enum('jenis_transaksi', ['masuk', 'keluar']);
            $table->decimal('jumlah', 10, 2);
            $table->decimal('stok_sebelum', 10, 2);
            $table->decimal('stok_sesudah', 10, 2);
            $table->text('keterangan')->nullable();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->dateTime('tanggal_transaksi');
            $table->timestamps();
            
            $table->index(['bahan_baku_id', 'tanggal_transaksi']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_transaksi');
    }
};
