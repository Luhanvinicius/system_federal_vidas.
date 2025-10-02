@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto py-10">

  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-semibold">Minhas consultas</h1>
    <a href="{{ route('appointments.create') }}" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">Nova consulta</a>
  </div>

  @php
    // Compat: aceitar $appointments OU $items
    $appointments = $appointments ?? ($items ?? collect());
  @endphp

  <div class="bg-white shadow rounded overflow-hidden">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50 border-b">
        <tr>
          <th class="px-4 py-2 text-left">#</th>
          <th class="px-4 py-2 text-left">Especialidade</th>
          <th class="px-4 py-2 text-left">Clínica</th>
          <th class="px-4 py-2 text-left">Criado em</th>
          <th class="px-4 py-2 text-left">Status</th>
          <th class="px-4 py-2 text-left">Ações</th>
        </tr>
      </thead>
      <tbody>
        @forelse($appointments as $it)
          <tr class="border-t">
            <td class="px-4 py-2">#{{ $it->id }}</td>
            <td class="px-4 py-2">{{ optional($it->specialty)->name ?? '-' }}</td>
            <td class="px-4 py-2">{{ optional($it->clinic)->name ?? '-' }}</td>
            <td class="px-4 py-2">{{ optional($it->created_at)->format('d/m/Y H:i') }}</td>
            <td class="px-4 py-2">
              @php
                $status = (string)($it->status ?? '');
                $badge = match($status) {
                  'confirmed'        => 'bg-green-100 text-green-800',
                  'completed'        => 'bg-blue-100 text-blue-800',
                  'canceled'         => 'bg-red-100 text-red-800',
                  'awaiting_payment' => 'bg-yellow-100 text-yellow-800',
                  default            => 'bg-gray-100 text-gray-800',
                };
                $label = match($status) {
                  'confirmed'        => 'Confirmado',
                  'completed'        => 'Concluído',
                  'canceled'         => 'Cancelado',
                  'awaiting_payment' => 'Aguardando pagamento',
                  default            => ucfirst($status ?: '—'),
                };
              @endphp
              <span class="px-2 py-1 rounded text-xs font-medium {{ $badge }}">{{ $label }}</span>
            </td>
            <td class="px-4 py-2 space-x-2">
              <a href="{{ route('appointments.payment', $it) }}" class="text-blue-600 hover:underline">Pagamento</a>
              @if(Route::has('appointments.show'))
                <a href="{{ route('appointments.show', $it) }}" class="text-gray-700 hover:underline">Detalhes</a>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="px-4 py-10 text-center text-gray-500">
              Você ainda não possui consultas. <a href="{{ route('appointments.create') }}" class="text-blue-600 underline">Solicitar agora</a>.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Paginação (se $appointments for paginator) --}}
  @if(method_exists($appointments, 'links'))
    <div class="mt-4">
      {{ $appointments->links() }}
    </div>
  @endif
</div>
@endsection