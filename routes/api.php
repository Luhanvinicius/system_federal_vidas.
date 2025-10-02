<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\NearbyClinicsController;
use App\Http\Controllers\AsaasWebhookController;

Route::get('/clinics/nearby', NearbyClinicsController::class);


require __DIR__.'/api_asaas_webhook.php';