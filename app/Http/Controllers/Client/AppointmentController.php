<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Specialty;
use App\Support\Coparticipation;

class AppointmentController extends Controller
{
    /**
     * RESTAURA o método create() que somente renderiza o formulário
     * de Nova Consulta sem alterar nada no fluxo existente.
     */
    public function create()
    {
        // Mantém a mesma view já usada no projeto
        $specialties = Specialty::orderBy('name')->get();
        return view('client.appointments.create', compact('specialties'));
    }

    /**
     * Salva o agendamento garantindo que o valor de coparticipação
     * seja o correto (do formulário ou da especialidade).
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'specialty_id'   => ['required','integer','exists:specialties,id'],
            'cep'            => ['required','string','max:16'],
            'city'           => ['required','string','max:80'],
            'state'          => ['required','string','max:2'],
            'clinic_id'      => ['required','integer','exists:clinics,id'],
            'available_days' => ['nullable','string','max:255'],
            'preferred_time' => ['nullable','string','max:255'],
            'indication'     => ['nullable','string','max:255'],
            'notes'          => ['nullable','string','max:2000'],
            'coparticipation_price' => ['nullable','string','max:32'],
        ]);

        // 1) valor vindo do form (ex.: "R$ 40,00")
        $value = Coparticipation::normalize($request->input('coparticipation_price'));

        // 2) se não veio do form, pega da especialidade
        if ($value === null) {
            $specialty = Specialty::find($data['specialty_id']);
            $value = Coparticipation::resolveFromSpecialty($specialty);
        }

        // 3) fallback
        if ($value === null || $value <= 0) {
            $value = (float) env('ASAAS_PIX_VALUE_DEFAULT', 30.00);
        }

        $data['user_id'] = auth()->id();
        $data['status']  = 'pending';
        $data['coparticipation_price'] = $value;

        $appointment = Appointment::create($data);

        return redirect()->route('appointments.payment', $appointment);
    }
}
