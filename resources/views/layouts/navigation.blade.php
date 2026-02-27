<!-- Estilos da Sidebar -->
<style>
    .sidebar-link {
        transition: all 0.3s ease;
        border-left: 3px solid transparent;
    }
    .sidebar-link:hover {
        background-color: rgba(255, 255, 255, 0.05);
        border-left-color: #499F2D; /* Verde da marca */
        color: #ffffff !important;
    }
    .sidebar-link.active {
        background-color: #004EA5; /* Azul da marca */
        border-left-color: #499F2D; /* Verde da marca */
        color: #ffffff !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
</style>

<!-- =====================================
     SIDEBAR PARA TELAS GRANDES (Desktop)
     ===================================== -->
<!-- Aumentamos a largura de w-64 para w-80 -->
<aside class="w-70 bg-[#222222] text-gray-300 flex flex-col justify-between shadow-2xl h-full hidden md:flex z-20">
    
    <div>
        <!-- Logo -->
        <div class="flex items-center justify-center h-24 border-b border-gray-700/50 px-6 mt-2">
                <img src="{{ asset('logo/buscaCnpjs.png') }}" alt="Logo BuscaCNPJs" class="h-14 w-auto object-contain transition-transform hover:scale-105">
        </div>

        <!-- Links de Navegação -->
        <nav class="mt-8 px-5 space-y-2">
            <!-- Único link: API Docs -->
            <!-- Substitua o link antigo por este aqui: -->
            <a href="{{ route('api-docs') }}" class="flex items-center px-4 py-3 rounded-lg sidebar-link {{ request()->routeIs('api-docs') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-code text-lg"></i>
                <span class="mx-3 font-medium text-base">API Docs</span>
            </a>
            <!-- Link: Chaves de API (Tokens CRUD) -->
            <a href="{{ route('tokens.index') }}" class="flex items-center px-4 py-3 rounded-lg sidebar-link {{ request()->routeIs('tokens.*') ? 'active' : '' }}">
                <i class="bi bi-key-fill text-lg"></i>
                <span class="mx-3 font-medium text-base">Chaves de API</span>
            </a>
        </nav>
    </div>

    <!-- Área Inferior: Dados do Usuário e Logout -->
    <div class="p-5 border-t border-gray-700/50 bg-[#1a1a1a]">
        <div class="flex items-center mb-5 px-2">
            <!-- Avatar Inicial -->
            <div class="h-12 w-12 rounded-full bg-[#004EA5] text-white flex items-center justify-center text-lg font-bold border-2 border-[#499F2D] shadow-md shrink-0">
                {{ substr(Auth::user()->name, 0, 1) }}
            </div>
            <!-- Info (E-mail em cima, Nome embaixo) -->
            <div class="ml-4 overflow-hidden">
                <p class="text-xs text-gray-400 truncate" title="{{ Auth::user()->email }}">{{ Auth::user()->email }}</p>
                <p class="text-sm font-semibold text-white truncate mt-0.5">{{ Auth::user()->name }}</p>
            </div>
        </div>

        <!-- Botão Logout -->
        <form method="POST" action="{{ route('logout') }}" class="mb-0">
            @csrf
            <button type="submit" class="w-full flex items-center px-4 py-2.5 rounded-lg text-sm font-medium text-gray-400 hover:bg-red-500/10 hover:text-red-500 transition-colors cursor-pointer">
                <i class="bi bi-box-arrow-left text-lg"></i>
                <span class="mx-3">Sair da Plataforma</span>
            </button>
        </form>
    </div>
</aside>

<!-- =====================================
     NAVBAR SIMPLIFICADA PARA MOBILE
     ===================================== -->
<div class="md:hidden bg-[#222222] text-white flex justify-between items-center p-4 shadow-md w-full z-20">
        <img src="{{ asset('logo/buscaCnpjs.png') }}" alt="Logo BuscaCNPJs" class="h-8 w-auto">
    
    <form method="POST" action="{{ route('logout') }}" class="m-0">
        @csrf
        <button type="submit" class="text-gray-400 hover:text-red-500 transition-colors cursor-pointer">
            <i class="bi bi-box-arrow-right text-2xl"></i>
        </button>
    </form>
</div>