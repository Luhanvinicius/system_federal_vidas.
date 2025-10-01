<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Specialty;

class AppointmentController extends Controller
{
    /** Formulário de nova consulta */
    public function create()
    {
        $specialties = Specialty::orderBy('name')->get();
        return view('client.appointments.create', compact('specialties'));
    }

    /** Grava a solicitação */
    public function store(Request $request)
    {
        $data = $request->validate([
            'specialty_id' => ['required','integer','exists:specialties,id'],
            'cep'          => ['required','regex:/^\d{5}-?\d{3}$/'],
            'city'         => ['required','string','max:120'],
            'state'        => ['required','string','size:2'],
            'clinic_id'    => ['required','integer','exists:clinics,id'],
            'indication'   => ['nullable','string','max:255'],
            'notes'        => ['nullable','string','max:2000'],
        ]);

        $data['user_id'] = auth()->id();
        $data['status']  = 'pending';

        Appointment::create($data);

        return redirect()->route('client.dashboard')->with('success','Solicitação registrada com sucesso!');
    }
}
