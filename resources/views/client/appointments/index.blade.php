@extends('layouts.app')

@section('content')
<h1 class="text-xl font-bold mb-4">Minhas Consultas</h1>

<table class="min-w-full bg-white shadow rounded">
    <thead>
        <tr class="bg-gray-200 text-left">
            <th class="py-2 px-4">Especialidade</th>
            <th class="py-2 px-4">Clínica</th>
            <th class="py-2 px-4">Data/Hora</th>
            <th class="py-2 px-4">Status</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="py-2 px-4">Clínico Geral</td>
            <td class="py-2 px-4">Clínica Saúde Vida</td>
            <td class="py-2 px-4">28/09/2025 14:30</td>
            <td class="py-2 px-4"><span class="px-2 py-1 bg-green-200 text-green-800 rounded">Agendado</span></td>
        </tr>
    </tbody>
</table>
@endsection
