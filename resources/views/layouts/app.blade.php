<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Laravel') }}</title>
        <!-- Bloquear indexação e rastreamento de motores de busca (Google, Bing, etc) -->
        <meta name="robots" content="noindex, nofollow, noarchive">
        <meta name="googlebot" content="noindex, nofollow">
        <!-- Fonte Poppins -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <!-- Bootstrap Icons (CDN) -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            body { font-family: 'Poppins', sans-serif !important; }
        </style>
    </head>
    <body class="font-sans antialiased text-gray-900 bg-gray-50 flex h-screen overflow-hidden">
        <!-- 1. ESQUERDA: Aqui incluímos o navigation que agora é a nossa Sidebar -->
        @include('layouts.navigation')
        <!-- 2. DIREITA: Conteúdo Principal e Topo -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Page Heading (Topo) -->
            @isset($header)
                <header class="bg-white shadow-sm z-10 border-b border-gray-200">
                    <div class="py-4 px-6 sm:px-6 lg:px-8 text-xl font-semibold text-gray-800">
                        {{ $header }}
                    </div>
                </header>
            @endisset
            <!-- Page Content (Onde o dashboard.blade.php vai aparecer) -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6">
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
