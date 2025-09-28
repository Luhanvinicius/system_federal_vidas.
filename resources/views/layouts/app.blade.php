<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name', 'Clínica Saúde') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
<div class="flex min-h-screen">
    <!-- Sidebar -->
    <aside class="w-64 bg-green-700 text-white p-4">
        <h2 class="text-xl font-bold mb-6">{{ config('app.name', 'Clínica Saúde') }}</h2>
        <nav>
            <ul class="space-y-2">
                <li><a href="/dashboard" class="block py-2 px-3 rounded hover:bg-green-600">Dashboard</a></li>
                <li><a href="/appointments" class="block py-2 px-3 rounded hover:bg-green-600">Consultas</a></li>
                <li><a href="/profile" class="block py-2 px-3 rounded hover:bg-green-600">Perfil</a></li>
                <li>
                    <form action="/logout" method="POST" class="mt-6">
                        @csrf
                        <button class="w-full text-left py-2 px-3 bg-red-600/80 hover:bg-red-700 rounded">Sair</button>
                    </form>
                </li>
            </ul>
        </nav>
    </aside>

    <!-- Conteúdo -->
    <main class="flex-1 p-6">
        @yield('content')
    </main>
</div>
</body>
</html>
