<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\PaymentStatusController;

Route::middleware(['auth'])->group(function () {
    Route::get('/appointments/{appointment}/status', [PaymentStatusController::class, 'status'])->name('appointments.status');
    Route::get('/appointments/{appointment}/success', [PaymentStatusController::class, 'success'])->name('appointments.success');
});
