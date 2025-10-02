@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-4xl">
    <h1 class="text-2xl font-semibold mb-4">Pagamento da Coparticipação (Pix)</h1>

    @if($appointment->asaas_pix_qr_code)
        <div class="bg-white rounded shadow p-6 flex gap-6 items-start">
            <div>
                <img src="data:image/png;base64,{{ $appointment->asaas_pix_qr_code }}" alt="QR Code Pix" class="w-56 h-56 border" />
            </div>
            <div class="flex-1">
                <p class="mb-2 text-gray-700">Escaneie o QR Code no seu app do banco para pagar.</p>
                <label class="block text-sm font-medium text-gray-600 mb-1">Copia e Cola:</label>
                <textarea class="w-full p-2 border rounded text-xs" rows="6" readonly>{{ $appointment->asaas_pix_payload }}</textarea>
                <button id="copyPix" class="mt-2 px-3 py-2 bg-gray-800 text-white rounded text-sm">Copiar</button>

                <div class="mt-6">
                    <button id="checkStatus" class="px-4 py-2 bg-green-600 text-white rounded">Verificar pagamento</button>
                    <span id="statusLabel" class="ml-3 text-sm text-gray-600"></span>
                </div>
            </div>
        </div>
    @else
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
            <p>Não foi possível gerar o QR Code no momento.</p>
        </div>
    @endif

    <div class="mt-6">
        <a href="{{ route('client.dashboard') }}" class="text-blue-600 hover:underline">Voltar ao painel</a>
    </div>
</div>

<script>
document.getElementById('copyPix')?.addEventListener('click', () => {
    const ta = document.querySelector('textarea');
    ta.select(); ta.setSelectionRange(0, 99999);
    document.execCommand('copy');
    alert('Código copiado!');
});

document.getElementById('checkStatus')?.addEventListener('click', async () => {
    const statusLbl = document.getElementById('statusLabel');
    statusLbl.textContent = 'Consultando...';
    try {
        const r = await fetch('{{ route('client.payments.status', $appointment) }}');
        const j = await r.json();
        statusLbl.textContent = 'Status: ' + j.status;
        if (['RECEIVED', 'CONFIRMED'].includes(j.status)) {
            alert('Pagamento confirmado! Seu agendamento foi atualizado.');
            window.location.href = '{{ route('client.dashboard') }}';
        }
    } catch (e) {
        statusLbl.textContent = 'Falha ao consultar.';
    }
});
</script>
@endsection
