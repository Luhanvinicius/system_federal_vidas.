@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <!-- Hero card similar à imagem -->
    <section class="bg-gradient-to-r from-sky-600 via-sky-500 to-sky-400 rounded-2xl overflow-hidden shadow">
        <div class="grid md:grid-cols-2">
            <div class="p-8 md:p-10 text-white">
                <h2 class="text-2xl md:text-3xl font-extrabold mb-2">Bem-vindo {{ auth()->user()->name ?? '' }}!</h2>
                <p class="text-white/90">Aqui você acompanha seus agendamentos, solicita novas consultas e atualiza seus dados.</p>
                <div class="mt-6 flex gap-3">
                    <a href="{{ route('appointments.create') }}" class="px-4 py-2 bg-white text-sky-700 rounded-lg font-semibold hover:bg-slate-100">Solicitar atendimento</a>
                    <a href="{{ route('appointments.index') }}" class="px-4 py-2 border border-white/70 text-white rounded-lg hover:bg-white/10">Minhas consultas</a>
                </div>
            </div>
            <div class="relative h-56 md:h-auto">
                <div class="absolute inset-0">
                    <!-- Ilustração abstrata -->
                    <svg viewBox="0 0 600 300" class="w-full h-full">
                        <defs>
                            <linearGradient id="g1" x1="0" x2="1">
                                <stop offset="0%" stop-color="#38bdf8" />
                                <stop offset="100%" stop-color="#ffffff" stop-opacity=".3"/>
                            </linearGradient>
                        </defs>
                        <rect x="0" y="0" width="600" height="300" fill="url(#g1)" opacity=".1"/>
                        <g stroke="white" stroke-opacity=".7" fill="none" stroke-width="2">
                            <path d="M0,220 C80,240 120,160 180,170 C240,180 270,230 330,220 C390,210 420,160 480,170 C540,180 560,210 600,200"/>
                            <path d="M0,140 C90,120 120,200 180,190 C240,180 270,130 330,140 C390,150 430,190 480,180 C540,170 560,140 600,160" opacity=".6"/>
                        </g>
                        <g fill="white" fill-opacity=".9">
                            <circle cx="480" cy="70" r="60" />
                            <circle cx="470" cy="65" r="22" class="opacity-80" />
                        </g>
                    </svg>
                </div>
            </div>
        </div>
    </section>

    <!-- Cards de status -->
    <section class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
        <div class="bg-white rounded-xl p-5 shadow">
            <div class="text-slate-500">Aguardando Pagamento</div>
            <div class="text-3xl font-extrabold text-rose-600 mt-1">0</div>
        </div>
        <div class="bg-white rounded-xl p-5 shadow">
            <div class="text-slate-500">Agendamentos Confirmados</div>
            <div class="text-3xl font-extrabold text-emerald-600 mt-1">0</div>
        </div>
        <div class="bg-white rounded-xl p-5 shadow">
            <div class="text-slate-500">Atendimentos Concluídos</div>
            <div class="text-3xl font-extrabold text-sky-600 mt-1">0</div>
        </div>
        <div class="bg-white rounded-xl p-5 shadow">
            <div class="text-slate-500">Cancelados</div>
            <div class="text-3xl font-extrabold text-amber-600 mt-1">0</div>
        </div>
    </section>
@endsection
