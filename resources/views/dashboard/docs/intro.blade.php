<!-- ==========================================
     1. AUTENTICAÇÃO E BASE URL
     ========================================== -->
<div class="bg-white rounded-2xl shadow-sm border-t-4 border-t-[#499F2D] overflow-hidden">
    <div class="p-6 sm:p-8">
        <div class="flex items-center gap-3 mb-4">
            <div class="h-10 w-10 rounded-full bg-green-50 flex items-center justify-center text-[#499F2D]">
                <i class="bi bi-shield-lock-fill text-xl"></i>
            </div>
            <h3 class="text-xl font-bold" style="color: #222222;">Autenticação & Base URL</h3>
        </div>
        
        <p class="text-gray-600 text-sm leading-relaxed mb-6">
            A API do BuscaCNPJs utiliza a arquitetura REST. Todas as requisições devem ser feitas para a URL base abaixo e necessitam de um <strong>Bearer Token</strong> no cabeçalho <code>Authorization</code>.
        </p>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Base URL -->
            <div>
                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">URL Base da API</h4>
                <div class="bg-gray-100 rounded-lg p-3 flex items-center gap-3 border border-gray-200">
                    <span class="text-gray-500"><i class="bi bi-globe"></i></span>
                    <code class="text-sm font-semibold text-gray-800">https://api.buscacnpjs.com/api/v1</code>
                </div>
            </div>

            <!-- Header Auth -->
            <div>
                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Header de Autenticação</h4>
                <div class="code-block rounded-lg p-3 relative group border border-gray-800">
                    <pre class="text-sm overflow-x-auto"><code>Authorization: Bearer <span class="text-[#499F2D]">{seu_token_aqui}</span></code></pre>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ==========================================
     MENU DE NAVEGAÇÃO INTERNA RÁPIDA
     ========================================== -->
<div class="flex flex-wrap gap-2">
    <a href="#endpoint-cnpj" class="px-4 py-2 bg-white border border-gray-200 rounded-full text-sm font-medium text-gray-600 hover:text-[#004EA5] hover:border-[#004EA5] transition-colors shadow-sm">Consulta CNPJ</a>
    <a href="#endpoint-cep" class="px-4 py-2 bg-white border border-gray-200 rounded-full text-sm font-medium text-gray-600 hover:text-[#004EA5] hover:border-[#004EA5] transition-colors shadow-sm">Busca por CEP</a>
    <a href="#endpoint-search" class="px-4 py-2 bg-white border border-gray-200 rounded-full text-sm font-medium text-gray-600 hover:text-[#004EA5] hover:border-[#004EA5] transition-colors shadow-sm">Busca Avançada</a>
    <a href="#endpoint-ufs" class="px-4 py-2 bg-white border border-gray-200 rounded-full text-sm font-medium text-gray-600 hover:text-[#004EA5] hover:border-[#004EA5] transition-colors shadow-sm">Estatísticas (UFs)</a>
    <a href="#endpoint-directory" class="px-4 py-2 bg-white border border-gray-200 rounded-full text-sm font-medium text-gray-600 hover:text-[#004EA5] hover:border-[#004EA5] transition-colors shadow-sm">Diretório Municipios</a>
</div>