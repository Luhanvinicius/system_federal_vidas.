@extends('layouts.app')

@section('content')
<h1 class="text-xl font-bold mb-4">Solicitar Nova Consulta</h1>

<form action="{{ route('appointments.store') }}" method="POST" class="space-y-4 bg-white p-6 rounded shadow">
    @csrf
    <div>
        <label class="block font-semibold">Especialidade</label>
        <select name="specialty" class="w-full border rounded p-2">
            <option>Clínico Geral</option>
            <option>Cardiologia</option>
        </select>
    </div>
    <div>
        <label class="block font-semibold">CEP</label>
        <input type="text" name="cep" class="w-full border rounded p-2" placeholder="00000-000">
    </div>
    <div>
        <label class="block font-semibold">Observações</label>
        <textarea name="notes" class="w-full border rounded p-2"></textarea>
    </div>
    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Solicitar</button>
</form>
@endsection
