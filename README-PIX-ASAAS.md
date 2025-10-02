# PIX (Asaas) – Pacote rápido
**Somente PIX**. Copie sobre o seu projeto, rode `php artisan migrate`, adicione as rotas:

```php
Route::middleware(['auth','verified','role:client'])->group(function () {
    Route::post('appointments', [App\Http\Controllers\Client\AppointmentController::class,'store'])->name('appointments.store');
    Route::get('appointments/{appointment}/payment', [App\Http\Controllers\Client\AppointmentController::class,'payment'])->name('appointments.payment');
});
```

Env:
```
ASAAS_TOKEN=SEU_TOKEN
ASAAS_BASE_URL=https://api.asaas.com/v3
```

Sem token, um QR **fake** é mostrado para desenvolvimento local.
