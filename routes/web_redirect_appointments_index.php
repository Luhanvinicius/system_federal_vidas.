<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['web','auth'])->get('/appointments', function () {
    try {
        return redirect()->route('appointments.create');
    } catch (Throwable $e) {
        return redirect('/appointments/create');
    }
});
