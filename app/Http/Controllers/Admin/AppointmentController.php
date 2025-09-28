<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index()
    {
        // Listagem geral para admin
        return view('admin.appointments.index');
    }

    public function create()
    {
        // Admin cria/agendar manual
        return view('admin.appointments.create');
    }

    public function store(Request $request)
    {
        // TODO: criar/agendar
        return redirect()->route('appointments.index')->with('success', 'Agendamento criado!');
    }

    public function show($id)
    {
        // Detalhe
        return view('admin.appointments.show', ['id' => $id]);
    }

    public function edit($id)
    {
        // Editar agendamento
        return view('admin.appointments.edit', ['id' => $id]);
    }

    public function update(Request $request, $id)
    {
        // TODO: atualizar
        return redirect()->route('appointments.index')->with('success', 'Atualizado!');
    }

    public function destroy($id)
    {
        // TODO: remover
        return redirect()->back()->with('success', 'Removido!');
    }
}
