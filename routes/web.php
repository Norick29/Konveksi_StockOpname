<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Middleware\OwnerMiddleware;
use App\Http\Middleware\AdminOnly;


Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/home', function () {
    return view('home');
})->middleware(['auth', 'verified'])->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/stores', [App\Http\Controllers\TokoController::class, 'index'])->name('stores.index');
    Route::get('/categories', [App\Http\Controllers\KategoriController::class, 'index'])->name('categories.index');
});

Route::middleware(['auth', OwnerMiddleware::class])->group(function () {
    // Add owner-only routes here
    Route::resource('users', UserController::class);
    
});

Route::middleware(['auth', AdminOnly::class])->group(function () {
    // Add admin-only routes here
    Route::post('/stores', [App\Http\Controllers\TokoController::class, 'store'])->name('stores.store');
    Route::put('/stores/{id_toko}', [App\Http\Controllers\TokoController::class, 'update'])->name('stores.update');
    Route::delete('/stores/{id_toko}', [App\Http\Controllers\TokoController::class, 'destroy'])->name('stores.destroy');

    Route::post('/categories', [App\Http\Controllers\KategoriController::class, 'store'])->name('categories.store');
    Route::put('/categories/{id_kategori}', [App\Http\Controllers\KategoriController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{id_kategori}', [App\Http\Controllers\KategoriController::class, 'destroy'])->name('categories.destroy');
});



require __DIR__.'/auth.php';
