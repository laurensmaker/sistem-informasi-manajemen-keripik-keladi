<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BahanBakuController;
use App\Http\Controllers\JenisKeripikController;
use App\Http\Controllers\KomposisiController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\StokBahanBakuController;
use App\Http\Controllers\StokKeripikController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    
    Route::resource('jenis-keripik', JenisKeripikController::class);
    Route::resource('bahan-baku', BahanBakuController::class);
    Route::resource('stok-keripik', StokKeripikController::class);
    
    // Profile
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    
    // Manajemen User (hanya untuk owner)
    Route::resource('users', UserController::class)->middleware('role:owner');
    // Route tambahan untuk stok
    Route::get('stok-keripik/laporan', [StokKeripikController::class, 'laporan'])->name('stok-keripik.laporan');
    Route::get('stok-keripik/get-stok/{jenisKeripikId}', [StokKeripikController::class, 'getStokByJenis'])->name('stok-keripik.get-stok');
    
    Route::resource('stok-bahan-baku', StokBahanBakuController::class);
    
    // Route tambahan untuk stok bahan baku
    Route::get('stok-bahan-baku/laporan', [StokBahanBakuController::class, 'laporan'])->name('stok-bahan-baku.laporan');
    Route::get('stok-bahan-baku/dashboard', [StokBahanBakuController::class, 'dashboard'])->name('stok-bahan-baku.dashboard');
    Route::get('stok-bahan-baku/get-stok/{bahanBakuId}', [StokBahanBakuController::class, 'getStokByBahan'])->name('stok-bahan-baku.get-stok');
    Route::resource('penjualan', PenjualanController::class);
   
    
    
    
    
    // Route tambahan untuk penjualan
    Route::get('penjualan/laporan', [PenjualanController::class, 'laporan'])->name('penjualan.laporan');
    Route::get('penjualan/get-produk/{id}', [PenjualanController::class, 'getProduk'])->name('penjualan.get-produk');
    Route::patch('penjualan/{penjualan}/status', [PenjualanController::class, 'updateStatus'])->name('penjualan.update-status');
    
    Route::prefix('laporan')->group(function() {
        Route::get('laba-rugi', [LaporanController::class, 'labaRugi'])->name('laba-rugi');
        // Route::get('keuntungan-produk', [LaporanController::class, 'keuntunganProduk'])->name('keuntungan-produk');
        // Route::get('keuntungan-bahan', [LaporanController::class, 'keuntunganBahan'])->name('keuntungan-bahan');
        Route::get('dashboard-keuangan', [LaporanController::class, 'dashboardKeuangan'])->name('dashboard-keuangan');
        // Route::get('print-laba-rugi', [LaporanController::class, 'printLabaRugi'])->name('print-laba-rugi');

         // Halaman Index Laporan
        Route::get('/', [LaporanController::class, 'index'])->name('laporan.index');

        // Laporan Penjualan
        Route::get('/penjualan', [LaporanController::class, 'laporanPenjualan'])->name('laporan.penjualan');
        Route::post('/penjualan/download', [LaporanController::class, 'downloadPenjualanPDF'])->name('laporan.penjualan.download');
        Route::get('/penjualan/filter', [LaporanController::class, 'laporanPenjualan'])->name('laporan.penjualan.filter');

        // Laporan Jenis Keripik
        Route::get('/jenis-keripik', [LaporanController::class, 'laporanJenisKeripik'])->name('laporan.jenis-keripik');
        Route::get('/jenis-keripik/download', [LaporanController::class, 'downloadJenisKeripikPDF'])->name('laporan.jenis-keripik.download');

        // Laporan Bahan Baku
        Route::get('/bahan-baku', [LaporanController::class, 'laporanBahanBaku'])->name('laporan.bahan-baku');
        Route::get('/bahan-baku/download', [LaporanController::class, 'downloadBahanBakuPDF'])->name('laporan.bahan-baku.download');
    });
     Route::resource('komposisi', KomposisiController::class);
    
    // Route tambahan untuk komposisi
    Route::get('komposisi/laporan', [KomposisiController::class, 'laporan'])->name('komposisi.laporan');
    Route::get('komposisi/biaya-produksi', [KomposisiController::class, 'biayaProduksi'])->name('komposisi.biaya-produksi');
    Route::get('komposisi/get-bahan/{jenisKeripikId}', [KomposisiController::class, 'getBahanByJenis'])->name('komposisi.get-bahan');
    Route::get('/{kodeProduksi}', [KomposisiController::class, 'show'])->name('komposisi.show');

    Route::delete('/{kodeProduksi}', [KomposisiController::class, 'destroy'])->name('komposisi.destroy');
});
