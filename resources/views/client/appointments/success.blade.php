@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-12">
  <div class="bg-white shadow rounded p-8 text-center">
    <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-16 w-16 text-green-600" viewBox="0 0 24 24" fill="currentColor">
      <path fill-rule="evenodd" d="M10.28 15.53a.75.75 0 0 1-1.06 0l-3-3a.75.75 0 1 1 1.06-1.06l2.47 2.47 5.47-5.47a.75.75 0 1 1 1.06 1.06l-6 6z" clip-rule="evenodd"/>
      <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zM3.75 12a8.25 8.25 0 1 1 16.5 0 8.25 8.25 0 0 1-16.5 0z" clip-rule="evenodd"/>
    </svg>
    <h1 class="text-2xl font-semibold mt-4">Pagamento confirmado 🎉</h1>
    <p class="text-gray-600 mt-2">Recebemos seu pagamento. Em breve entraremos em contato para confirmar o agendamento.</p>

    <div class="mt-6 text-left text-sm bg-gray-50 border rounded p-4">
      <p><strong>ID do agendamento:</strong> {{ $appointment->id }}</p>
      <p><strong>Status:</strong> {{ $appointment->status }}</p>
      @if(isset($appointment->paid_at))
      <p><strong>Pago em:</strong> {{ $appointment->paid_at }}</p>
      @endif
    </div>

    <a href="{{ route('client.dashboard') }}" class="mt-8 inline-block px-4 py-2 bg-blue-600 text-white rounded">Voltar ao painel</a>
  </div>
</div>
@endsection
