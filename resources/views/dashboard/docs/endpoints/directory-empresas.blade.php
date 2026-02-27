<!-- ENDPOINT 6: EMPRESAS POR MUNICÍPIO (SLUG) -->
<div id="endpoint-directory-empresas" class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden endpoint-card">
    <div class="p-4 md:p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-[#004EA5]/5">
        <div class="flex items-center gap-3">
            <span class="bg-[#004EA5] text-white px-3 py-1.5 rounded-md text-xs font-bold tracking-wider shadow-sm">GET</span>
            <code class="text-lg font-bold text-[#004EA5]">/{uf}/{municipio_slug}/empresas</code>
        </div>
        <span class="text-xs font-semibold text-gray-600 bg-white border border-gray-200 px-3 py-1 rounded-full shadow-sm flex items-center">
            <i class="bi bi-building mr-1 text-[#499F2D]"></i> Listagem Paginada
        </span>
    </div>
    
    <div class="p-6">
        <p class="text-gray-700 text-sm mb-6 leading-relaxed bg-blue-50/50 p-4 rounded-lg border border-blue-100">
            Retorna a lista detalhada de <strong>empresas ativas</strong> em um município específico, utilizando uma URL amigável (Slug). Os resultados são ordenados pelo número do CNPJ (index-friendly) e usam paginação simples, otimizada para listas massivas de até 5.000 registros por página.
            <br><br>
            <i class="bi bi-rocket-takeoff-fill text-[#004EA5]"></i> <strong>Uso Ideal:</strong> Alimentar páginas finais do seu diretório web e sitemaps de motores de busca (Googlebot).
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
                        <td class="px-4 py-3 font-mono font-semibold text-gray-800">uf</td>
                        <td class="px-4 py-3"><span class="bg-blue-50 text-[#004EA5] px-2 py-1 rounded text-xs font-medium border border-blue-100">Rota</span></td>
                        <td class="px-4 py-3"><strong>Obrigatório.</strong> Sigla do estado com 2 letras.</td>
                    </tr>
                    <tr class="border-b border-gray-50 bg-gray-50/30">
                        <td class="px-4 py-3 font-mono font-semibold text-gray-800">municipio_slug</td>
                        <td class="px-4 py-3"><span class="bg-blue-50 text-[#004EA5] px-2 py-1 rounded text-xs font-medium border border-blue-100">Rota</span></td>
                        <td class="px-4 py-3"><strong>Obrigatório.</strong> Nome do município hifenizado (ex: <code>juiz-de-fora</code>).</td>
                    </tr>
                    <tr class="border-b border-gray-50">
                        <td class="px-4 py-3 font-mono font-semibold text-gray-800">per_page</td>
                        <td class="px-4 py-3"><span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs font-medium border border-gray-200">Query</span></td>
                        <td class="px-4 py-3">Registros por página (Mín: 1, Máx: 5000, Padrão: 500).</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 font-mono font-semibold text-gray-800 border-b border-gray-50">page</td>
                        <td class="px-4 py-3 border-b border-gray-50"><span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs font-medium border border-gray-200">Query</span></td>
                        <td class="px-4 py-3 border-b border-gray-50">O número da página para navegação.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mt-6">
            
            <!-- Estrutura de Retorno Descritiva -->
            <div class="lg:col-span-4">
                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3"><i class="bi bi-diagram-3"></i> Estrutura do Retorno</h4>
                <ul class="text-sm text-gray-600 space-y-3">
                    <li class="flex items-start gap-2 border-b border-gray-100 pb-2">
                        <i class="bi bi-check-circle-fill text-[#499F2D] mt-0.5"></i> 
                        <div><strong>consulta:</strong> Resolve e confirma o município buscado. Inclui também os links prontos de paginação (<code>next_page_url</code>).</div>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="bi bi-check-circle-fill text-[#499F2D] mt-0.5"></i> 
                        <div><strong>data:</strong> Array contendo os CNPJs com Razão Social, Nome Fantasia, Abertura e Capital Social já formatado.</div>
                    </li>
                </ul>

                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mt-8 mb-3"><i class="bi bi-exclamation-triangle"></i> Respostas de Erro</h4>
                <div class="space-y-2">
                    <div class="bg-red-50 border border-red-100 rounded-md p-3 text-sm">
                        <span class="font-bold text-red-700">HTTP 400 Bad Request</span><br>
                        <span class="text-red-600 font-mono text-xs">{"error": "UF inválida"}</span>
                    </div>
                    <div class="bg-orange-50 border border-orange-100 rounded-md p-3 text-sm">
                        <span class="font-bold text-orange-700">HTTP 404 Not Found</span><br>
                        <span class="text-orange-600 font-mono text-xs">{"error": "Município não encontrado..."}</span>
                    </div>
                </div>
            </div>

            <!-- Payload Completo -->
            <div class="lg:col-span-8">
                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3"><i class="bi bi-code-slash"></i> Exemplo de Resposta (HTTP 200 OK)</h4>
                
                <div class="code-block rounded-xl p-5 shadow-inner max-h-[36rem] overflow-auto code-block-scroll">
<pre class="text-sm leading-relaxed"><code>{
  <span class="json-key">"consulta"</span>: {
    <span class="json-key">"endpoint"</span>: <span class="json-string">"{uf}/{municipio_slug}/empresas"</span>,
    <span class="json-key">"uf"</span>: <span class="json-string">"SP"</span>,
    <span class="json-key">"municipio"</span>: {
      <span class="json-key">"codigo"</span>: <span class="json-number">6181</span>,
      <span class="json-key">"nome"</span>: <span class="json-string">"ATIBAIA"</span>,
      <span class="json-key">"slug"</span>: <span class="json-string">"atibaia"</span>
    },
    <span class="json-key">"ok"</span>: <span class="json-boolean">true</span>,
    <span class="json-key">"filtros"</span>: {
      <span class="json-key">"situacao_cadastral"</span>: <span class="json-number">2</span>
    },
    <span class="json-key">"paginacao"</span>: {
      <span class="json-key">"per_page"</span>: <span class="json-number">500</span>,
      <span class="json-key">"current_page"</span>: <span class="json-number">1</span>,
      <span class="json-key">"next_page_url"</span>: <span class="json-string">"https://api.buscacnpjs.com/api/v1/sp/atibaia/empresas?page=2"</span>,
      <span class="json-key">"prev_page_url"</span>: <span class="json-null">null</span>
    }
  },
  <span class="json-key">"data"</span>: [
    {
      <span class="json-key">"cnpj"</span>: <span class="json-string">"00000000041548"</span>,
      <span class="json-key">"razao_social"</span>: <span class="json-string">"BANCO DO BRASIL SA"</span>,
      <span class="json-key">"nome_fantasia"</span>: <span class="json-string">"ATIBAIA (SP)"</span>,
      <span class="json-key">"data_abertura"</span>: <span class="json-string">"01-08-1966"</span>,
      <span class="json-key">"capital_social"</span>: <span class="json-string">"R$ 120.000.000.000,00"</span>,
      <span class="json-key">"localizacao"</span>: {
        <span class="json-key">"uf"</span>: <span class="json-string">"SP"</span>,
        <span class="json-key">"municipio"</span>: {
          <span class="json-key">"codigo"</span>: <span class="json-number">6181</span>,
          <span class="json-key">"nome"</span>: <span class="json-string">"ATIBAIA"</span>,
          <span class="json-key">"slug"</span>: <span class="json-string">"atibaia"</span>
        }
      },
      <span class="json-key">"situacao_cadastral"</span>: {
        <span class="json-key">"codigo"</span>: <span class="json-number">2</span>,
        <span class="json-key">"descricao"</span>: <span class="json-string">"ATIVA"</span>
      }
    },
    {
      <span class="json-key">"cnpj"</span>: <span class="json-string">"00000834000105"</span>,
      <span class="json-key">"razao_social"</span>: <span class="json-string">"SICEL COMERCIO DE MATERIAIS ELETRICOS..."</span>,
      <span class="json-key">"nome_fantasia"</span>: <span class="json-string">""</span>,
      <span class="json-key">"data_abertura"</span>: <span class="json-string">"05-05-1994"</span>,
      <span class="json-key">"capital_social"</span>: <span class="json-string">"R$ 30.000,00"</span>,
      <span class="json-key">"localizacao"</span>: {
        <span class="json-key">"uf"</span>: <span class="json-string">"SP"</span>,
        <span class="json-key">"municipio"</span>: {
          <span class="json-key">"codigo"</span>: <span class="json-number">6181</span>,
          <span class="json-key">"nome"</span>: <span class="json-string">"ATIBAIA"</span>,
          <span class="json-key">"slug"</span>: <span class="json-string">"atibaia"</span>
        }
      },
      <span class="json-key">"situacao_cadastral"</span>: {
        <span class="json-key">"codigo"</span>: <span class="json-number">2</span>,
        <span class="json-key">"descricao"</span>: <span class="json-string">"ATIVA"</span>
      }
    }
    <span class="text-gray-500">// ... array continua com os resultados da página atual ...</span>
  ]
}</code></pre>
                </div>
            </div>
        </div>
    </div>
</div>