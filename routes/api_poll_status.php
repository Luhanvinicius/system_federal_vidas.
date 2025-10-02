<?php

use Illuminate\Support\Facades\Route;
use App\Models\Appointment;

Route::middleware('api')->get('/appointments/{appointment}/status', function (Appointment $appointment) {
    return response()->json(['id'=>$appointment->id,'status'=>$appointment->status,'paid_at'=>$appointment->paid_at ?? null]);
})->name('appointments.status');
