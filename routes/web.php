<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\AppointmentController as AdminAppointmentController;
use App\Http\Controllers\Client\AppointmentController as ClientAppointmentController;
use App\Http\Controllers\Client\PaymentController as ClientPaymentController;

Route::redirect('/', '/login');

Route::get('/dashboard', function () {
    $user = auth()->user();
    if (!$user) return redirect()->route('login');
    if (($user->role ?? null) === 'admin') return redirect()->route('admin.dashboard');
    return redirect()->route('client.dashboard');
})->middleware(['auth','verified'])->name('dashboard');

// CLIENTE
Route::middleware(['auth','verified'])->group(function () {
    Route::get('/client/dashboard', fn() => view('client.dashboard'))->name('client.dashboard');
    Route::get('/appointments', [ClientAppointmentController::class,'index'])->name('appointments.index'); // <- NOVO
    Route::get('/appointments/create', [ClientAppointmentController::class,'create'])->name('appointments.create');
    Route::post('/appointments', [ClientAppointmentController::class,'store'])->name('appointments.store');
    Route::get('/appointments/{appointment}/payment', [ClientPaymentController::class,'payment'])->name('appointments.payment');
});

// ADMIN em /admin
Route::middleware(['auth','verified'])->prefix('admin')->as('admin.')->group(function () {
    Route::get('/dashboard', fn () => view('admin.dashboard'))->name('dashboard');
    Route::get('/clients/create', [ClientController::class, 'create'])->name('clients.create');
    Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
    Route::resource('appointments', AdminAppointmentController::class);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
