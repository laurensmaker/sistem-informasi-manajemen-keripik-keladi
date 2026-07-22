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
        Schema::create('jenis_keripik', function (Blueprint $table) {
            $table->id();
            $table->string('nama_jenis', 50);
            $table->text('deskripsi')->nullable();
            $table->decimal('harga_jual', 10, 2);
            $table->string('satuan', 20)->default('pcs');
            $table->integer('berat');
            $table->string('gambar')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jenis_keripik');
    }
};
