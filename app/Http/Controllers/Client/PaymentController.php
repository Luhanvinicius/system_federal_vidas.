<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Services\AsaasService;
use Illuminate\Support\Facades\Auth;
use App\Support\Coparticipation;

class PaymentController extends Controller
{
    public function payment(Appointment $appointment, AsaasService $asaas)
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');
        if (($appointment->user_id ?? null) !== $user->id && ($user->role ?? null) !== 'admin') {
            abort(403, 'Acesso negado a este pagamento.');
        }

        // Valor: 1) do agendamento  2) da especialidade  3) fallback .env
        $value = null;
        if (isset($appointment->coparticipation_price)) {
            $value = Coparticipation::normalize($appointment->coparticipation_price);
        }
        if ($value === null) {
            $value = Coparticipation::resolveFromSpecialty($appointment->specialty ?? null);
        }
        if ($value === null || $value <= 0) {
            $value = (float) env('ASAAS_PIX_VALUE_DEFAULT', 30.00);
        }

        // se o appointment não tinha o valor salvo, atualiza
        if (!isset($appointment->coparticipation_price) || !$appointment->coparticipation_price) {
            $appointment->coparticipation_price = $value;
            $appointment->save();
        }

        if (!$appointment->asaas_payment_id || !$appointment->asaas_pix_qr_base64) {
            // ===== CORREÇÃO: evitar ternários aninhados sem parênteses (PHP 8.2) =====
            $customerId = env('ASAAS_CUSTOMER_ID');

            if (!$customerId) {
                $email = env('ASAAS_CUSTOMER_EMAIL');
                if ($email) {
                    $customerId = $asaas->findCustomerIdByEmail($email);
                }
            }

            if (!$customerId) {
                $name = env('ASAAS_CUSTOMER_NAME');
                if ($name) {
                    $customerId = $asaas->findCustomerIdByName($name);
                }
            }

            if (!$customerId) {
                $customerId = $asaas->findCustomerIdByEmail($user->email ?? '');
            }

            if (!$customerId) {
                throw new \RuntimeException('Cliente Asaas não encontrado. Defina ASAAS_CUSTOMER_EMAIL, ASAAS_CUSTOMER_ID ou ASAAS_CUSTOMER_NAME no .env.');
            }

            $pix = $asaas->createPixCharge($customerId, $value, env('ASAAS_PIX_DESCRIPTION', 'Consulta médica'));

            $appointment->asaas_payment_id      = $pix['id'] ?? null;
            $appointment->asaas_invoice_url     = $pix['invoiceUrl'] ?? null;
            $appointment->asaas_pix_qr_base64   = $pix['encodedImage'] ?? null;
            $appointment->asaas_pix_payload     = $pix['payload'] ?? null;
            $appointment->asaas_qr_code_payload = $pix['payload'] ?? null;
            $appointment->status                = 'awaiting_payment';
            if (\Schema::hasColumn('appointments','payment_value')) {
                $appointment->payment_value = $value;
            }
            $appointment->save();
        }

        $displayValue = (isset($appointment->payment_value) && $appointment->payment_value)
            ? (float)$appointment->payment_value
            : (float)$appointment->coparticipation_price;

        return view('client.appointments.payment', [
            'appointment'  => $appointment,
            'paymentValue' => $displayValue,
            'pixPayload'   => $appointment->asaas_pix_payload ?? $appointment->asaas_qr_code_payload,
        ]);
    }
}
