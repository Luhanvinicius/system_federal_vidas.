<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Appointment;
use Carbon\Carbon;

class AsaasWebhookController extends Controller
{
    /**
     * Simple GET for health-check/debug in the browser.
     */
    public function ping(Request $request)
    {
        return response()->json([
            'ok' => true,
            'requires' => 'POST',
            'token' => $request->query('token'),
        ]);
    }

    /**
     * Handle ASAAS webhook callbacks.
     *
     * IMPORTANT: a webhook is a server-to-server request. You cannot redirect
     * the user's browser from here. Instead, we persist the payment status on
     * the Appointment and the client side polls /appointments/{id}/status.
     * When it turns 'paid', the page auto-redirects to appointments.success.
     */
    public function handle(Request $request)
    {
        // Optional shared secret via query param ?token=...
        $expected = env('ASAAS_WEBHOOK_TOKEN');
        $provided = (string) $request->query('token', '');
        if ($expected && $expected !== $provided) {
            Log::warning('ASAAS webhook: invalid token', ['provided' => $provided]);
            return response()->json(['ok' => false, 'error' => 'invalid token'], 403);
        }

        $payload = $request->all();
        Log::info('ASAAS webhook received', ['event' => $payload['event'] ?? null]);

        // We accept both legacy "event" string and nested payment status
        $event      = strtoupper((string)($payload['event'] ?? ''));
        $paymentId  = $this->get($payload, ['payment.id','data.payment.id','data.id','id']);
        $status     = strtoupper((string) $this->get($payload, ['payment.status','data.payment.status','data.status','status']));
        $invoiceUrl = $this->get($payload, ['payment.invoiceUrl','data.payment.invoiceUrl','invoiceUrl']);
        $externalRef= $this->get($payload, ['payment.externalReference','data.payment.externalReference','externalReference']);
        $paidAtIso  = $this->get($payload, ['payment.clientPaymentDate','data.payment.clientPaymentDate','clientPaymentDate']) 
                      ?? $this->get($payload, ['payment.paymentDate','data.payment.paymentDate','paymentDate']);

        if (!$paymentId && !$invoiceUrl && !$externalRef) {
            Log::warning('ASAAS webhook: no identifiers present');
            return response()->json(['ok' => true, 'ignored' => true]);
        }

        // Find our local appointment
        $appointment = Appointment::query()
            ->when($paymentId,  fn($q)=>$q->orWhere('asaas_payment_id', $paymentId))
            ->when($invoiceUrl, fn($q)=>$q->orWhere('asaas_invoice_url', $invoiceUrl))
            ->when($externalRef,fn($q)=>$q->orWhere('id', intval($externalRef)))
            ->first();

        if (!$appointment) {
            Log::warning('ASAAS webhook: appointment not found', [
                'paymentId' => $paymentId,
                'invoiceUrl'=> $invoiceUrl,
                'external'  => $externalRef,
            ]);
            return response()->json(['ok' => true, 'not_found' => true]);
        }

        // Normalize "paid" signals
        $paidEvents = ['PAYMENT_CONFIRMED','PAYMENT_RECEIVED','PAYMENT_RECEIVED_IN_CASH','CONFIRMED','RECEIVED'];
        $paidStatuses = ['RECEIVED','CONFIRMED','RECEIVED_IN_CASH','PAYMENT_RECEIVED','PAYMENT_CONFIRMED'];
        $cancelStatuses = ['CANCELED','REFUNDED'];

        $isPaid = in_array($event, $paidEvents, true) || in_array($status, $paidStatuses, true);

        // Persist state atomically
        DB::transaction(function() use ($appointment, $status, $paymentId, $invoiceUrl, $paidAtIso, $isPaid, $cancelStatuses) {
            if ($paymentId && (!$appointment->asaas_payment_id)) {
                $appointment->asaas_payment_id = $paymentId;
            }
            if ($invoiceUrl && (!$appointment->asaas_invoice_url)) {
                $appointment->asaas_invoice_url = $invoiceUrl;
            }

            if ($isPaid) {
                $appointment->status = 'confirmed';
                if ($paidAtIso) {
                $appointment->status = 'confirmed';
                if ($paidAtIso) {
                    try {
                    } catch (\Throwable $e) {
                        // fallback: now()
                    }
                } elseif (in_array($status, $cancelStatuses, true)) {
                $appointment->status = 'canceled';
            } else {
                }
            } elseif (in_array($status, $cancelStatuses, true)) {
                $appointment->status = 'canceled';
            } else {
                // keep last known status string from Asaas for debugging (optional column may not exist)
                if (property_exists($appointment, 'asaas_last_status')) {
                    $appointment->asaas_last_status = $status ?: ($event ?: null);
                }
            }
            $appointment->save();
        });

        Log::info('ASAAS webhook: appointment updated', [
            'id' => $appointment->id,
            'isPaid' => $isPaid,
            'status' => $status,
            'event' => $event,
        ]);

        // Return 200 OK quickly to Asaas
        return response()->json(['ok' => true]);
    }

    private function get(array $arr, array $paths, $default = null)
    {
        foreach ($paths as $p) {
            $v = data_get($arr, $p);
            if ($v !== null && $v !== '') return $v;
        }
        return $default;
    }
}
