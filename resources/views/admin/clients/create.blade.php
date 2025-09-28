@extends('layouts.app')

@section('content')
<h1 class="text-xl font-bold mb-4">Cadastrar Novo Cliente</h1>

<form action="{{ route('admin.clients.store') }}" method="POST" class="space-y-4 bg-white p-6 rounded shadow">
    @csrf
    <div>
        <label class="block font-semibold">Nome</label>
        <input type="text" name="name" class="w-full border rounded p-2" required>
    </div>
    <div>
        <label class="block font-semibold">Email</label>
        <input type="email" name="email" class="w-full border rounded p-2" required>
    </div>
    <div>
        <label class="block font-semibold">Senha</label>
        <input type="password" name="password" class="w-full border rounded p-2" required>
    </div>
    <div>
        <label class="block font-semibold">Confirmar Senha</label>
        <input type="password" name="password_confirmation" class="w-full border rounded p-2" required>
    </div>
    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
        Cadastrar
    </button>
</form>
@endsection
