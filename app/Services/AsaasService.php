<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AsaasService
{
    protected string $baseUrl;
    protected ?string $token;

    public function __construct()
    {
        $this->baseUrl = rtrim(env('ASAAS_BASE_URL', 'https://sandbox.asaas.com/api/v3'), '/');
        $this->token   = env('ASAAS_API_KEY', env('ASAAS_TOKEN'));
    }

    protected function http()
    {
        return Http::withHeaders([
            'accept'       => 'application/json',
            'content-type' => 'application/json',
            'access_token' => $this->token,
        ]);
    }

    public function createPixCharge(string $customerId, float $value, string $description = 'Pagamento de consulta'): array
    {
        $days   = (int) env('ASAAS_DUE_DAYS', 0);
        $today  = date('Y-m-d', strtotime('+' . $days + 0 . ' day'));

        $payload = [
            'customer'     => $customerId,
            'billingType'  => 'PIX',
            'value'        => round($value, 2),
            'description'  => $description,
            'dueDate'      => $today,
        ];

        if ($ref = env('ASAAS_EXTERNAL_REFERENCE')) {
            $payload['externalReference'] = $ref;
        }

        $create = $this->http()->post("{$this->baseUrl}/payments", $payload);
        if (!$create->successful()) {
            throw new \RuntimeException('Erro ao criar cobrança PIX: ' . $create->status() . ' ' . $create->body());
        }

        $paymentId  = $create->json('id');
        $invoiceUrl = $create->json('invoiceUrl');

        $qr = $this->http()->get("{$this->baseUrl}/payments/{$paymentId}/pixQrCode");
        if (!$qr->successful()) {
            throw new \RuntimeException('Erro ao obter QRCode PIX: ' . $qr->status() . ' ' . $qr->body());
        }

        return [
            'id'          => $paymentId,
            'invoiceUrl'  => $invoiceUrl,
            'encodedImage'=> $qr->json('encodedImage'),
            'payload'     => $qr->json('payload'),
        ];
    }
}
