<?php

use Illuminate\Support\Facades\Route;
use App\Models\Appointment;

Route::middleware(['web','auth'])->get('/appointments/{appointment}/success', function (Appointment $appointment) {
    return view('client.appointments.success', compact('appointment'));
})->name('appointments.success');
