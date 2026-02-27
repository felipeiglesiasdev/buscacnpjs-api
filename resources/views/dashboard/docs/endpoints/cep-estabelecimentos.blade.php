<!-- ENDPOINT 2.1: CEP ESTABELECIMENTOS (Listagem Paginada) -->
<div id="endpoint-cep-estabelecimentos" class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden endpoint-card">
    <div class="p-4 md:p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-[#004EA5]/5">
        <div class="flex items-center gap-3">
            <span class="bg-[#004EA5] text-white px-3 py-1.5 rounded-md text-xs font-bold tracking-wider shadow-sm">GET</span>
            <code class="text-lg font-bold text-[#004EA5]">/cep/{cep}/estabelecimentos</code>
        </div>
        <span class="text-xs font-semibold text-gray-600 bg-white border border-gray-200 px-3 py-1 rounded-full shadow-sm"><i class="bi bi-card-list mr-1 text-[#499F2D]"></i> Listagem Paginada</span>
    </div>
    
    <div class="p-6">
        <p class="text-gray-700 text-sm mb-6 leading-relaxed bg-blue-50/50 p-4 rounded-lg border border-blue-100">
            Diferente do endpoint analítico, esta rota retorna a <strong>lista detalhada de empresas</strong> cadastradas em um determinado CEP. O retorno é obrigatoriamente paginado para garantir performance e segurança.
            <br><br>
            <i class="bi bi-info-circle text-[#004EA5]"></i> <strong>Nota:</strong> É ideal para alimentar tabelas ou datagrids no seu front-end, permitindo navegação através dos parâmetros <code>page</code> e <code>per_page</code>.
        </p>
        
        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3"><i class="bi bi-sliders"></i> Parâmetros da Requisição</h4>
        <div class="overflow-x-auto mb-8">
            <table class="w-full text-sm text-left text-gray-600">
                <thead class="text-xs text-gray-500 bg-gray-50 uppercase border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3">Parâmetro</th>
                        <th class="px-4 py-3">Tipo</th>
                        <th class="px-4 py-3">Descrição</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-gray-50">
                        <td class="px-4 py-3 font-mono font-semibold text-gray-800">cep</td>
                        <td class="px-4 py-3"><span class="bg-blue-50 text-[#004EA5] px-2 py-1 rounded text-xs font-medium border border-blue-100">Rota</span></td>
                        <td class="px-4 py-3"><strong>Obrigatório.</strong> O CEP a ser consultado (8 dígitos).</td>
                    </tr>
                    <tr class="border-b border-gray-50 bg-gray-50/30">
                        <td class="px-4 py-3 font-mono font-semibold text-gray-800">per_page</td>
                        <td class="px-4 py-3"><span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs font-medium border border-gray-200">Query</span></td>
                        <td class="px-4 py-3">Quantidade de registros por página (Mín: 1, Máx: 200, Padrão: 50).</td>
                    </tr>
                    <tr class="border-b border-gray-50">
                        <td class="px-4 py-3 font-mono font-semibold text-gray-800">page</td>
                        <td class="px-4 py-3"><span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs font-medium border border-gray-200">Query</span></td>
                        <td class="px-4 py-3">O número da página para navegação (Padrão: 1).</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 font-mono font-semibold text-gray-800 border-b border-gray-50">ativos</td>
                        <td class="px-4 py-3 border-b border-gray-50"><span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs font-medium border border-gray-200">Query</span></td>
                        <td class="px-4 py-3 border-b border-gray-50">Boolean (true/false). Define se retorna apenas empresas com situação ATIVA. (Padrão: true).</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            <!-- Estrutura de Retorno Descritiva -->
            <div class="lg:col-span-4">
                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3"><i class="bi bi-diagram-3"></i> Estrutura do Retorno</h4>
                <ul class="text-sm text-gray-600 space-y-3">
                    <li class="flex items-start gap-2 border-b border-gray-100 pb-2">
                        <i class="bi bi-check-circle-fill text-[#499F2D] mt-0.5"></i> 
                        <div><strong>consulta:</strong> Reflete os parâmetros aplicados na busca (CEP e filtro de ativos).</div>
                    </li>
                    <li class="flex items-start gap-2 border-b border-gray-100 pb-2">
                        <i class="bi bi-check-circle-fill text-[#499F2D] mt-0.5"></i> 
                        <div><strong>meta:</strong> Dados de paginação vitais para o front-end, informando a página atual, limite, total de registros encontrados e a última página possível.</div>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="bi bi-check-circle-fill text-[#499F2D] mt-0.5"></i> 
                        <div><strong>data:</strong> O array de objetos. Cada item traz o CNPJ, Razão Social, Data de Abertura, Capital Social formatado, UF, Município e Situação Cadastral.</div>
                    </li>
                </ul>

                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mt-8 mb-3"><i class="bi bi-exclamation-triangle"></i> Respostas de Erro</h4>
                <div class="space-y-2">
                    <div class="bg-red-50 border border-red-100 rounded-md p-3 text-sm">
                        <span class="font-bold text-red-700">HTTP 400 Bad Request</span><br>
                        <span class="text-red-600 font-mono text-xs">{"error": "CEP inválido"}</span>
                    </div>
                </div>
            </div>

            <!-- Payload Completo -->
            <div class="lg:col-span-8">
                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3"><i class="bi bi-code-slash"></i> Exemplo de Resposta (HTTP 200 OK)</h4>
                <!-- Bloco de código com rolagem ativada -->
                <div class="code-block rounded-xl p-5 shadow-inner max-h-[36rem] overflow-auto code-block-scroll">
<pre class="text-sm leading-relaxed"><code>{
  <span class="json-key">"consulta"</span>: {
    <span class="json-key">"cep"</span>: <span class="json-string">"36036533"</span>,
    <span class="json-key">"endpoint"</span>: <span class="json-string">"cep_estabelecimentos"</span>,
    <span class="json-key">"ativos"</span>: <span class="json-boolean">true</span>
  },
  <span class="json-key">"meta"</span>: {
    <span class="json-key">"page"</span>: <span class="json-number">1</span>,
    <span class="json-key">"per_page"</span>: <span class="json-number">50</span>,
    <span class="json-key">"total"</span>: <span class="json-number">21</span>,
    <span class="json-key">"last_page"</span>: <span class="json-number">1</span>
  },
  <span class="json-key">"data"</span>: [
    {
      <span class="json-key">"cnpj"</span>: <span class="json-string">"34933213000198"</span>,
      <span class="json-key">"razao_social"</span>: <span class="json-string">"34.933.213 FERNANDO VITORINO VALE"</span>,
      <span class="json-key">"data_abertura"</span>: <span class="json-string">"19-09-2019"</span>,
      <span class="json-key">"capital_social"</span>: <span class="json-string">"R$ 100,00"</span>,
      <span class="json-key">"uf"</span>: <span class="json-string">"MG"</span>,
      <span class="json-key">"municipio"</span>: <span class="json-string">"JUIZ DE FORA"</span>,
      <span class="json-key">"situacao_cadastral"</span>: {
        <span class="json-key">"codigo"</span>: <span class="json-number">2</span>,
        <span class="json-key">"descricao"</span>: <span class="json-string">"ATIVA"</span>
      }
    },
    {
      <span class="json-key">"cnpj"</span>: <span class="json-string">"50418305000196"</span>,
      <span class="json-key">"razao_social"</span>: <span class="json-string">"50.418.305 GISELE DE AVELAR BARBOSA FERREIRA"</span>,
      <span class="json-key">"data_abertura"</span>: <span class="json-string">"24-04-2023"</span>,
      <span class="json-key">"capital_social"</span>: <span class="json-string">"R$ 100,00"</span>,
      <span class="json-key">"uf"</span>: <span class="json-string">"MG"</span>,
      <span class="json-key">"municipio"</span>: <span class="json-string">"JUIZ DE FORA"</span>,
      <span class="json-key">"situacao_cadastral"</span>: {
        <span class="json-key">"codigo"</span>: <span class="json-number">2</span>,
        <span class="json-key">"descricao"</span>: <span class="json-string">"ATIVA"</span>
      }
    },
    <span class="text-gray-500">// ... array continua com os resultados da página atual ...</span>
  ]
}</code></pre>
                </div>
            </div>
        </div>
    </div>
</div>