@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-10">
  <h1 class="text-2xl font-semibold mb-6">Pagamento via PIX</h1>

  <div class="bg-white shadow rounded p-6 text-center">
    <div class="mb-2 text-sm text-gray-600">Valor</div>
    <div class="text-2xl font-bold mb-6">R$ {{ number_format($paymentValue ?? ($appointment->coparticipation_price ?? 0), 2, ',', '.') }}</div>

    @if($appointment->asaas_pix_qr_base64)
      <p class="mb-4">Escaneie o QR Code abaixo no seu app bancário:</p>
      <img alt="QR PIX" class="mx-auto w-64 h-64 object-contain" src="data:image/png;base64,{{ $appointment->asaas_pix_qr_base64 }}"/>
    @else
      <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 p-4 rounded mb-4">
        Não foi possível gerar o QR Code agora. Tente novamente em alguns instantes.
      </div>
    @endif

    <div class="mt-6 text-left">
      <label class="block text-sm text-gray-600 mb-2">Código PIX (copia e cola):</label>
      <textarea class="w-full p-3 border rounded text-xs" rows="5" readonly>@php echo $pixPayload ?? ($appointment->asaas_pix_payload ?? $appointment->asaas_qr_code_payload); @endphp</textarea>
      <button onclick="navigator.clipboard.writeText(document.querySelector('textarea').value)" class="mt-2 px-3 py-1 rounded bg-gray-800 text-white text-sm">Copiar</button>
    </div>

    @if($appointment->asaas_invoice_url)
      <p class="mt-4"><a href="{{ $appointment->asaas_invoice_url }}" target="_blank" class="text-blue-600 underline">Abrir fatura/recibo</a></p>
    @endif
  </div>
</div>
@endsection
