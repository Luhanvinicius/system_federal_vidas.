@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto py-10">
  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-semibold">Meus agendamentos</h1>
    <a href="{{ route('appointments.create') }}" class="px-4 py-2 rounded bg-blue-600 text-white">Nova solicitação</a>
  </div>

  <div class="bg-white shadow rounded overflow-hidden">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-4 py-2 text-left">#</th>
          <th class="px-4 py-2 text-left">Especialidade</th>
          <th class="px-4 py-2 text-left">Cidade/UF</th>
          <th class="px-4 py-2 text-left">Status</th>
          <th class="px-4 py-2 text-left">Ações</th>
        </tr>
      </thead>
      <tbody>
        @forelse($items as $it)
        <tr class="border-t">
          <td class="px-4 py-2">{{ $it->id }}</td>
          <td class="px-4 py-2">{{ optional($it->specialty)->name ?? '-' }}</td>
          <td class="px-4 py-2">{{ $it->city }}/{{ $it->state }}</td>
          <td class="px-4 py-2">{{ $it->status }}</td>
          <td class="px-4 py-2 space-x-2">
            <a href="{{ route('appointments.payment', $it) }}" class="text-blue-600 underline">Pagamento</a>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="5" class="px-4 py-6 text-center text-gray-500">Você ainda não tem agendamentos.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">
    {{ $items->links() }}
  </div>
</div>
@endsection
