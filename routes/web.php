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


// =============================
// ROTAS CLIENTE
// =============================
Route::middleware(['auth','verified'])->group(function () {
    // Dashboard do cliente
    Route::get('/client/dashboard', fn() => view('client.dashboard'))->name('client.dashboard');

    // ✅ Tela "Minhas Consultas" — precisa do método index() no AppointmentController
    Route::get('/appointments', [ClientAppointmentController::class,'index'])->name('appointments.index');

    // Nova consulta
    Route::get('/appointments/create', [ClientAppointmentController::class,'create'])->name('appointments.create');

    // Salvar agendamento
    Route::post('/appointments', [ClientAppointmentController::class,'store'])->name('appointments.store');

    // Tela de pagamento PIX
    Route::get('/appointments/{appointment}/payment', [ClientPaymentController::class,'payment'])->name('appointments.payment');

    // ✅ (Opcional, mas recomendado) Detalhes da consulta
    Route::get('/appointments/{appointment}', [ClientAppointmentController::class,'show'])->name('appointments.show');
});


// =============================
// ROTAS ADMINISTRADOR
// =============================
Route::middleware(['auth','verified'])->prefix('admin')->as('admin.')->group(function () {
    Route::get('/dashboard', fn () => view('admin.dashboard'))->name('dashboard');

    Route::get('/clients/create', [ClientController::class, 'create'])->name('clients.create');
    Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');

    Route::resource('appointments', AdminAppointmentController::class);
});


// =============================
// PERFIL DO USUÁRIO
// =============================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// =============================
// OUTRAS ROTAS
// =============================
require __DIR__.'/auth.php';
require __DIR__.'/web_payment_status.php';
require __DIR__.'/web_success_page.php';
