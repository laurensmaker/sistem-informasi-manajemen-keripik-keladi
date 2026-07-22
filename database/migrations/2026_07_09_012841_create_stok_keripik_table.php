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
        Schema::create('stok_keripik', function (Blueprint $table) {
            $table->id();
             $table->foreignId('jenis_keripik_id')
                ->constrained('jenis_keripik')
                ->cascadeOnDelete();
            $table->integer('jumlah_stok')->default(0);
            $table->integer('jumlah_masuk')->default(0);
            $table->integer('jumlah_keluar')->default(0);
             $table->string('kode_keripik', 10)->unique();
            $table->dateTime('tanggal_update');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_keripik');
    }
};
