<x-app-layout>
    <!-- Estilos Customizados para a Documentação -->
    <style>
        .endpoint-card { transition: all 0.3s ease; }
        .endpoint-card:target { border-color: #004EA5; box-shadow: 0 0 0 4px rgba(0, 78, 165, 0.1); }
        .code-block { background-color: #1e1e1e; color: #d4d4d4; font-family: 'Courier New', Courier, monospace; }
        .json-key { color: #9cdcfe; }
        .json-string { color: #ce9178; }
        .json-number { color: #b5cea8; }
        .json-boolean { color: #569cd6; }
        .json-null { color: #569cd6; }
    </style>

    <!-- Header da Página -->
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl" style="color: #222222;">Documentação da API v1</h2>
                <p class="text-sm text-gray-500 mt-1">Referência técnica completa dos endpoints da plataforma BuscaCNPJs.</p>
            </div>
            <!-- Botão de Ação Rápida -->
            <button class="flex items-center justify-center gap-2 bg-[#004EA5] hover:bg-[#499F2D] text-white px-5 py-2.5 rounded-lg text-sm font-semibold transition-all shadow-md hover:-translate-y-0.5">
                <i class="bi bi-key-fill text-lg"></i> Gerar Token de Acesso
            </button>
        </div>
    </x-slot>

    <!-- Conteúdo Principal -->
    <div class="space-y-8 max-w-6xl mx-auto pb-12">

        <!-- Inclusão do Bloco de Autenticação e Menu -->
        @include('dashboard.docs.intro')

        <!-- Lista de Endpoints -->
        <div>
            <h3 class="text-2xl font-bold mb-6 flex items-center gap-2" style="color: #222222;">
                <i class="bi bi-hdd-network text-[#004EA5]"></i> Referência de Endpoints
            </h3>
            
            <div class="space-y-8">
                <!-- Inclusão de cada Endpoint Separadamente -->
                @include('dashboard.docs.endpoints.cnpj')
                @include('dashboard.docs.endpoints.cep')
                @include('dashboard.docs.endpoints.cep-estabelecimentos') <!-- Adicione essa linha aqui! -->
                @include('dashboard.docs.endpoints.ufs')
                @include('dashboard.docs.endpoints.directory')
                @include('dashboard.docs.endpoints.directory-empresas')
                @include('dashboard.docs.endpoints.search')

            </div>
        </div>

    </div>
</x-app-layout>