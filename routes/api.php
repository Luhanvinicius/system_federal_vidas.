<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\NearbyClinicsController;

Route::get('/clinics/nearby', NearbyClinicsController::class);
