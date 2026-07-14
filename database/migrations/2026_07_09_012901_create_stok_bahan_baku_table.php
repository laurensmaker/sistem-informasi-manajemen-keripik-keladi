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
        Schema::create('stok_bahan_baku', function (Blueprint $table) {
            $table->id();
               $table->foreignId('bahan_baku_id')
                ->constrained('bahan_baku')
                ->cascadeOnDelete();
            $table->decimal('jumlah_stok', 10, 2)->default(0);
            $table->decimal('jumlah_masuk', 10, 2)->default(0);
            $table->decimal('jumlah_keluar', 10, 2)->default(0);
            $table->dateTime('tanggal_update');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_bahan_baku');
    }
};
