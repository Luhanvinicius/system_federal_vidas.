<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AsaasWebhookController;

// GET ping (navegador) e POST real
Route::get('/asaas/webhook',  [AsaasWebhookController::class, 'ping']);
Route::post('/asaas/webhook', [AsaasWebhookController::class, 'handle']);
