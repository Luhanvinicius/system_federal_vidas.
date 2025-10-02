<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\AppointmentController;

Route::middleware(['web','auth'])->group(function () {
    Route::get('/appointments/{appointment}/payment', [AppointmentController::class, 'payment'])
        ->name('appointments.payment');
});
