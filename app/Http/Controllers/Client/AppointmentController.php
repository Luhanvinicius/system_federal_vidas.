<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Specialty;
use Illuminate\Support\Facades\Schema;

class AppointmentController extends Controller
{
    public function create()
    {
        // Busca do BD; se não existir tabela ainda, cai para array de fallback
        if (Schema::hasTable('specialties')) {
            $specialties = Specialty::orderBy('name')->get(['id','name','price']);
        } else {
            $specialties = collect([
                ['id' => 1, 'name' => 'Clínico Geral', 'price' => 30.00],
            ]);
        }
        return view('client.appointments.create', compact('specialties'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'specialty_id' => ['required','exists:specialties,id'],
            'cep' => ['required','regex:/^\d{5}-?\d{3}$/'],
            'city' => ['required','string','max:120'],
            'state' => ['required','in:AC,AL,AP,AM,BA,CE,DF,ES,GO,MA,MT,MS,MG,PA,PB,PR,PE,PI,RJ,RN,RS,RO,RR,SC,SP,SE,TO'],
            'notes' => ['nullable','string','max:1000'],
            'indication' => ['nullable','string','max:255'],
        ]);

        // valor da coparticipação pela especialidade
        $specialty = Specialty::findOrFail($request->specialty_id);
        $price = $specialty->price;

        // TODO: salvar na tabela appointments incluindo specialty_id, user_id, price, cep, city, state, notes, indication, status='requested'

        return redirect()->route('appointments.create')->with('success', 'Solicitação enviada! Valor da coparticipação: R$ '.number_format($price,2,',','.'));
    }
}
