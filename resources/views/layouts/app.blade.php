<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Gestão - Sistema de Agendamento') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js para toggles simples -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 text-gray-800">
<div x-data="{ open:false }" class="min-h-screen flex">
    <!-- Sidebar -->
    <aside
        x-cloak
        :class="open ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
        class="fixed md:static inset-y-0 left-0 z-40 w-72 bg-white border-r transform transition-transform duration-200 ease-out">
        <div class="px-5 py-4 flex items-center gap-2 border-b">
            <button @click="open=false" class="md:hidden p-2 rounded hover:bg-slate-100" aria-label="Fechar menu">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-7 h-7 text-sky-600">
              <path d="M12 2a10 10 0 100 20 10 10 0 000-20zM7 9h10v2H7V9zm0 4h7v2H7v-2z" />
            </svg>
            <div>
                <div class="font-bold text-slate-800 leading-tight">Gestão</div>
                <div class="text-xs text-slate-500 -mt-0.5">Sistema de Agendamento</div>
            </div>
        </div>

        <nav class="p-3">
            <ul class="space-y-1 text-sm">
                <li>
                    <a href="{{ route('client.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-sky-50 text-slate-700 {{ request()->routeIs('client.dashboard') ? 'bg-sky-100 text-sky-700 font-semibold' : '' }}">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6"/></svg>
                        Home
                    </a>
                </li>
                <li>
                    <a href="/appointments/create" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-sky-50 text-slate-700">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6v12m6-6H6"/></svg>
                        Especialidade
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-sky-50 text-slate-700">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8c-3.866 0-7 2.239-7 5s3.134 5 7 5 7-2.239 7-5-3.134-5-7-5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8V4m0 16v-4"/></svg>
                        Clube de Desconto
                    </a>
                </li>
                <li>
                    <a href="/profile" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-sky-50 text-slate-700">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 12a5 5 0 100-10 5 5 0 000 10zm-7 9a7 7 0 1114 0H5z"/></svg>
                        Perfil
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-sky-50 text-slate-700">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 10h.01M12 10h.01M16 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Suporte
                    </a>
                </li>
                <li class="pt-2">
                    <form action="/logout" method="POST" class="px-3">
                        @csrf
                        <button class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-red-600 hover:bg-red-50">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H6a2 2 0 01-2-2V7a2 2 0 012-2h5a2 2 0 012 2v1"/></svg>
                            Sair
                        </button>
                    </form>
                </li>
            </ul>
        </nav>
    </aside>

    <!-- Overlay mobile -->
    <div
        x-cloak
        x-show="open"
        @click="open=false"
        class="fixed inset-0 bg-black/40 z-30 md:hidden"></div>

    <!-- Main -->
    <div class="flex-1 flex flex-col min-w-0 md:ml-0 ml-0">
        <!-- Topbar -->
        <header class="h-16 bg-white border-b flex items-center justify-between px-4 sm:px-6">
            <div class="flex items-center gap-2">
                <button @click="open=true" class="md:hidden p-2 rounded hover:bg-slate-100" aria-label="Abrir menu">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <h1 class="text-base sm:text-lg font-semibold truncate">@yield('title', '')</h1>
            </div>
            <div class="flex items-center gap-3">
                <div class="hidden sm:block text-right leading-tight">
                    <div class="text-sm font-semibold truncate max-w-[160px]">{{ auth()->user()->name ?? 'Usuário' }}</div>
                    <div class="text-xs text-slate-500 truncate max-w-[160px]">{{ auth()->user()->email ?? '' }}</div>
                </div>
                <div class="w-9 h-9 rounded-full bg-slate-200 grid place-content-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-slate-600" viewBox="0 0 24 24" fill="currentColor"><path d="M12 12a5 5 0 100-10 5 5 0 000 10zm-7 9a7 7 0 1114 0H5z"/></svg>
                </div>
            </div>
        </header>

        <!-- Content -->
        <main class="p-4 sm:p-6">
            @yield('content')
        </main>
    </div>
</div>
</body>
</html>
