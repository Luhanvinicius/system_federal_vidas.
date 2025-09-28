<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index()
    {
        // Listagem de consultas do cliente
        return view('client.appointments.index');
    }

    public function create()
    {
        // Form para solicitar nova consulta
        return view('client.appointments.create');
    }

    public function store(Request $request)
    {
        // TODO: validar e criar solicitação
        return redirect()->route('appointments.index')->with('success', 'Solicitação criada!');
    }

    public function show($id)
    {
        // Detalhes da consulta
        return view('client.appointments.show', ['id' => $id]);
    }

    public function edit($id)
    {
        // Editar (se aplicável)
        return view('client.appointments.edit', ['id' => $id]);
    }

    public function update(Request $request, $id)
    {
        // TODO: atualizar
        return redirect()->route('appointments.index')->with('success', 'Atualizado!');
    }

    public function destroy($id)
    {
        // TODO: cancelar/remover
        return redirect()->route('appointments.index')->with('success', 'Removido!');
    }
}
