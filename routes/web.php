<?php

use App\Http\Middleware\AdminOnly;
use App\Http\Controllers\DailyReport;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Middleware\OwnerMiddleware;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\DB;


Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/home', [App\Http\Controllers\Dashboard::class, 'index'])->middleware(['auth', 'verified'])->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::get('/stores', [App\Http\Controllers\TokoController::class, 'index'])->name('stores.index');
    Route::get('/categories', [App\Http\Controllers\KategoriController::class, 'index'])->name('categories.index');
    Route::get('/product', [App\Http\Controllers\ProductController::class, 'index'])->name('product.index');
    Route::get('/activity-logs', [App\Http\Controllers\ActivityLogs::class, 'index'])->name('activity-logs.index');
    Route::get('/daily-report', [DailyReport::class, 'index'])->name('daily-report.index');
    Route::get('/monthly-report', [App\Http\Controllers\MonthlyReport::class, 'index'])->name('monthly-report.index');
    Route::post('/daily-report/generate', [DailyReport::class, 'generate'])->name('daily-report.generate');
    Route::get('/report/monthly/export', [App\Http\Controllers\MonthlyReport::class, 'generateMonthlyReport'])
    ->middleware('auth')
    ->name('report.monthly.export');
});

Route::middleware(['auth', OwnerMiddleware::class])->group(function () {
    // Add owner-only routes here
    Route::resource('users', UserController::class);
    Route::get('/owner/low-stock-alert', function () {
    return \App\Models\StockBathces::select(
            'id_produk',
            'id_toko',
            DB::raw('SUM(qty_sisa) as total_sisa')
        )
        ->groupBy('id_produk', 'id_toko')
        ->having('total_sisa', '<=', 50)
        ->with('produk', 'toko')
        ->orderBy('total_sisa')
        ->get();
    });
    Route::get('/monitoring-stock', [App\Http\Controllers\MonitoringStock::class, 'index'])->name('monitoring-stock.index');

});

Route::middleware(['auth', AdminOnly::class])->group(function () {
    // Add admin-only routes here
    Route::post('/stores', [App\Http\Controllers\TokoController::class, 'store'])->name('stores.store');
    Route::put('/stores/{id_toko}', [App\Http\Controllers\TokoController::class, 'update'])->name('stores.update');
    Route::delete('/stores/{id_toko}', [App\Http\Controllers\TokoController::class, 'destroy'])->name('stores.destroy');

    Route::post('/categories', [App\Http\Controllers\KategoriController::class, 'store'])->name('categories.store');
    Route::put('/categories/{id_kategori}', [App\Http\Controllers\KategoriController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{id_kategori}', [App\Http\Controllers\KategoriController::class, 'destroy'])->name('categories.destroy');

    Route::post('/product', [App\Http\Controllers\ProductController::class, 'store'])->name('product.store');
    Route::put('/product/{id_produk}', [App\Http\Controllers\ProductController::class, 'update'])->name('product.update');
    Route::delete('/product/{id_produk}', [App\Http\Controllers\ProductController::class, 'destroy'])->name('product.destroy');
    Route::resource('opening-stock', App\Http\Controllers\OpeningStokController::class)->only(['index', 'store', 'destroy', 'update']);
    Route::resource('stock-in', App\Http\Controllers\StockIn::class);
    Route::resource('stock-out', App\Http\Controllers\StockOut::class);
    Route::resource('stock-adjust', App\Http\Controllers\StockAdjust::class);
    Route::get('/daily-report/pdf', [DailyReport::class, 'exportPdf'])->name('daily-report.pdf');
    Route::post('/daily-report/out-summary', [DailyReport::class, 'outSummary'])->name('daily-report.out-summary');
    
});



require __DIR__.'/auth.php';
