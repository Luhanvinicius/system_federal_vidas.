<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\AsaasService;
use Carbon\Carbon;

class PaymentStatusController extends Controller
{
    public function status(Appointment $appointment, AsaasService $asaas)
    {
        $user = Auth::user();
        if (!$user) abort(401);
        if ($appointment->user_id !== $user->id && (($user->role ?? null) !== 'admin')) {
            abort(403);
        }

        // Sempre sincroniza com ASAAS quando possível (sucesso/cancelado)
        if ($appointment->asaas_payment_id) {
            try {
                $p = $asaas->getPayment($appointment->asaas_payment_id);
                $remoteStatus = strtoupper((string)($p['status'] ?? ''));
                $paidStatuses   = ['RECEIVED','CONFIRMED','RECEIVED_IN_CASH','PAYMENT_RECEIVED','PAYMENT_CONFIRMED'];
                $cancelStatuses = ['CANCELED','REFUNDED'];

                if (in_array($remoteStatus, $paidStatuses, true)) {
                    if ($appointment->status !== 'confirmed') {
                        $appointment->status  = 'confirmed';
                        $appointment->save();
                        Log::info('ASAAS sync: confirmado', ['appointment_id'=>$appointment->id, 'remote'=>$remoteStatus]);
                    }
                } elseif (in_array($remoteStatus, $cancelStatuses, true)) {
                    if ($appointment->status !== 'canceled') {
                        $appointment->status = 'canceled';
                        $appointment->save();
                        Log::info('ASAAS sync: cancelado', ['appointment_id'=>$appointment->id, 'remote'=>$remoteStatus]);
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('ASAAS sync falhou', ['appointment_id'=>$appointment->id, 'err'=>$e->getMessage()]);
            }
        }

        return response()->json([
            'id'     => $appointment->id,
            'status' => (string) ($appointment->status ?? ''),
        ]);
    }

    public function success(Appointment $appointment, AsaasService $asaas)
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');
        if ($appointment->user_id !== $user->id && (($user->role ?? null) !== 'admin')) {
            abort(403);
        }

        // Last-mile: carrega do banco + sincroniza para garantir exibição correta
        $appointment->refresh();
        if ($appointment->status !== 'confirmed' && ($appointment->asaas_payment_id ?? null)) {
            try {
                $p = $asaas->getPayment($appointment->asaas_payment_id);
                $remoteStatus = strtoupper((string)($p['status'] ?? ''));
                $paidStatuses   = ['RECEIVED','CONFIRMED','RECEIVED_IN_CASH','PAYMENT_RECEIVED','PAYMENT_CONFIRMED'];
                if (in_array($remoteStatus, $paidStatuses, true)) {
                    $appointment->status  = 'confirmed';
                    $appointment->save();
                }
            } catch (\Throwable $e) { /* ignore */ }
        }

        if ($appointment->status !== 'confirmed') {
            return redirect()->route('appointments.payment', $appointment);
        }
        return view('client.appointments.success', compact('appointment'));
    }
}
