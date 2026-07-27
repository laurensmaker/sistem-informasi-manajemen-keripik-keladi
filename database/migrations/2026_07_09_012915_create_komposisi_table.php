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
        Schema::create('komposisi', function (Blueprint $table) {
             $table->id();
            
            // Foreign Keys
            $table->foreignId('jenis_keripik_id')
                ->constrained('jenis_keripik')
                ->cascadeOnDelete();
            $table->foreignId('bahan_baku_id')
                ->constrained('bahan_baku')
                ->cascadeOnDelete();
            
            // Field komposisi dasar
            $table->decimal('jumlah_dibutuhkan', 10, 2);
            
            // Field untuk produksi (nullable agar komposisi dasar tetap bisa dibuat)
            $table->string('kode_produksi', 50)->nullable();
            $table->integer('jumlah_produksi')->default(0);
            $table->decimal('total_biaya', 15, 2)->default(0);
            $table->dateTime('tanggal_produksi')->nullable();
            $table->foreignId('user_id')->nullable()
                ->constrained('users')
                ->cascadeOnDelete();
            $table->string('status_produksi', 20)->default('draft');
            
            // Timestamps
            $table->timestamps();
            
            // Index untuk optimasi query
            $table->index(['kode_produksi', 'tanggal_produksi']);
            $table->index('jenis_keripik_id');
            $table->index('bahan_baku_id');
            $table->index('status_produksi');
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('komposisi');
    }
};
