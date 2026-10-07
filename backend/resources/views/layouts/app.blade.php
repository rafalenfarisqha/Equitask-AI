<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'EquiTask AI')</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            letter-spacing: 0.01em;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col">
    <!-- Navbar -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="bg-indigo-600 text-white font-bold p-2 rounded-lg">ET</div>
                <span class="text-xl font-bold tracking-tight text-indigo-900">EquiTask AI</span>
            </div>
            <div>
                @auth
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-800">Keluar</button>
                    </form>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-6 py-8">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-4 text-center text-sm text-slate-500">
        &copy; 2026 EquiTask AI — Platform Evaluasi Diferensiasi Otomatis Berbasis LLM Multi-Agent.
    </footer>
</body>
</html>
