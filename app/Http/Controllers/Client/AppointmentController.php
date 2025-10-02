<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;
use App\Models\Specialty;
use App\Support\Coparticipation;

class AppointmentController extends Controller
{
    /**
     * LISTA as consultas do cliente logado (Minhas Consultas).
     * Suporta paginação e ordenação básica por data de criação.
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Ordena do mais recente para o mais antigo; ajuste o perPage se quiser
        $appointments = Appointment::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('client.appointments.index', compact('appointments'));
    }

    /**
     * Exibe o formulário de Nova Consulta.
     */
    public function create()
    {
        $specialties = Specialty::orderBy('name')->get();
        return view('client.appointments.create', compact('specialties'));
    }

    /**
     * Persiste o agendamento com a coparticipação correta.
     * - valor do form (normalizado) OU
     * - valor da especialidade OU
     * - fallback do .env (ASAAS_PIX_VALUE_DEFAULT; padrão 30.00)
     * Status inicial coerente com o domínio: 'awaiting_payment'.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'specialty_id'         => ['required','integer','exists:specialties,id'],
            'cep'                  => ['required','string','max:16'],
            'city'                 => ['required','string','max:80'],
            'state'                => ['required','string','max:2'],
            'clinic_id'            => ['required','integer','exists:clinics,id'],
            'available_days'       => ['nullable','string','max:255'],
            'preferred_time'       => ['nullable','string','max:255'],
            'indication'           => ['nullable','string','max:255'],
            'notes'                => ['nullable','string','max:2000'],
            'coparticipation_price'=> ['nullable','string','max:32'],
        ]);

        // 1) Valor vindo do form (ex.: "R$ 40,00")
        $value = Coparticipation::normalize($request->input('coparticipation_price'));

        // 2) Se não veio do form, usa o valor da especialidade
        if ($value === null) {
            $specialty = Specialty::find($data['specialty_id']);
            $value = Coparticipation::resolveFromSpecialty($specialty);
        }

        // 3) Fallback do .env
        if ($value === null || $value <= 0) {
            $value = (float) env('ASAAS_PIX_VALUE_DEFAULT', 30.00);
        }

        $data['user_id'] = Auth::id();

        // Usa o status do domínio do seu modelo (dashboard/contadores)
        // Preferimos a string para não acoplar à constante em tempo de build.
        $data['status']  = 'awaiting_payment';

        $data['coparticipation_price'] = $value;

        $appointment = Appointment::create($data);

        // Redireciona para a etapa de pagamento (PIX)
        return redirect()->route('appointments.payment', $appointment);
    }

    /**
     * (Opcional) Detalhes de uma consulta específica.
     */
    public function show(Appointment $appointment)
    {
        $user = Auth::user();
        if (!$user || $appointment->user_id !== $user->id) {
            abort(403, 'Acesso negado.');
        }

        return view('client.appointments.show', compact('appointment'));
    }
}
