<!-- ENDPOINT: BUSCA AVANÇADA (SEARCH) -->
<div id="endpoint-search" class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden endpoint-card">
    <div class="p-4 md:p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-[#004EA5]/5">
        <div class="flex items-center gap-3">
            <span class="bg-[#004EA5] text-white px-3 py-1.5 rounded-md text-xs font-bold tracking-wider shadow-sm">GET</span>
            <code class="text-lg font-bold text-[#004EA5]">/search</code>
        </div>
        <span class="text-xs font-semibold text-gray-600 bg-white border border-gray-200 px-3 py-1 rounded-full shadow-sm flex items-center">
            <i class="bi bi-funnel-fill mr-1 text-[#004EA5]"></i> Filtros Dinâmicos
        </span>
    </div>
    
    <div class="p-6">
        <p class="text-gray-700 text-sm mb-6 leading-relaxed bg-blue-50/50 p-4 rounded-lg border border-blue-100">
            O motor de busca mais poderoso da API. Permite <strong>cruzar dezenas de filtros simultaneamente</strong> (CNAE, Natureza Jurídica, Data de Abertura, UF, Porte, Termo Livre, etc.) para encontrar blocos muito específicos de empresas. O retorno utiliza paginação simples focada em alta velocidade de resposta.
            <br><br>
            <i class="bi bi-info-circle text-[#004EA5]"></i> <strong>Nota:</strong> Se nenhum parâmetro de <code>situacao</code> for enviado, a API filtra por padrão apenas empresas <strong>ATIVAS</strong> (código 2). Para buscar todas as situações, envie <code>situacao=all</code>.
        </p>

        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3"><i class="bi bi-sliders"></i> Principais Parâmetros (Query String)</h4>
        <div class="overflow-x-auto mb-8 border border-gray-200 rounded-lg">
            <table class="w-full text-sm text-left text-gray-600">
                <thead class="text-xs text-gray-500 bg-gray-50 uppercase border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3">Parâmetro</th>
                        <th class="px-4 py-3">Exemplo / Valor</th>
                        <th class="px-4 py-3">Descrição</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-gray-50">
                        <td class="px-4 py-3 font-mono font-semibold">q</td>
                        <td class="px-4 py-3"><code>"tecnologia"</code></td>
                        <td class="px-4 py-3">Termo de busca livre (pesquisa em Razão Social ou Nome Fantasia).</td>
                    </tr>
                    <tr class="border-b border-gray-50 bg-gray-50/30">
                        <td class="px-4 py-3 font-mono font-semibold">uf</td>
                        <td class="px-4 py-3"><code>SP</code></td>
                        <td class="px-4 py-3">Sigla do estado com 2 letras.</td>
                    </tr>
                    <tr class="border-b border-gray-50">
                        <td class="px-4 py-3 font-mono font-semibold">municipio</td>
                        <td class="px-4 py-3"><code>juiz-de-fora</code> ou <code>3136702</code></td>
                        <td class="px-4 py-3">Aceita tanto o nome hifenizado (Slug) quanto o Código IBGE direto.</td>
                    </tr>
                    <tr class="border-b border-gray-50 bg-gray-50/30">
                        <td class="px-4 py-3 font-mono font-semibold">situacao</td>
                        <td class="px-4 py-3"><code>all</code>, <code>2</code>, <code>8</code></td>
                        <td class="px-4 py-3">2 (Ativa), 8 (Baixada), 4 (Inapta). <em>Padrão: 2</em>.</td>
                    </tr>
                    <tr class="border-b border-gray-50">
                        <td class="px-4 py-3 font-mono font-semibold">porte</td>
                        <td class="px-4 py-3"><code>1</code>, <code>3</code>, <code>5</code></td>
                        <td class="px-4 py-3">1 (ME), 3 (EPP), 5 (Demais).</td>
                    </tr>
                    <tr class="border-b border-gray-50 bg-gray-50/30">
                        <td class="px-4 py-3 font-mono font-semibold">cnae_principal</td>
                        <td class="px-4 py-3"><code>6201501</code></td>
                        <td class="px-4 py-3">Código CNAE primário com exatamente 7 dígitos (apenas números).</td>
                    </tr>
                    <tr class="border-b border-gray-50">
                        <td class="px-4 py-3 font-mono font-semibold">abertura_de</td>
                        <td class="px-4 py-3"><code>2023-01-01</code></td>
                        <td class="px-4 py-3">Filtra empresas abertas a partir desta data exata (formato YYYY-MM-DD).</td>
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
                        <div><strong>filtros:</strong> Confirmação de todos os filtros que foram interpretados e aplicados com sucesso pela engine da API.</div>
                    </li>
                    <li class="flex items-start gap-2 border-b border-gray-100 pb-2">
                        <i class="bi bi-check-circle-fill text-[#499F2D] mt-0.5"></i> 
                        <div><strong>paginacao:</strong> Links prontos de navegação (<code>next_page_url</code>) configurados para manter a integridade de todos os filtros de pesquisa na próxima página.</div>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="bi bi-check-circle-fill text-[#499F2D] mt-0.5"></i> 
                        <div><strong>data:</strong> Array denso com Razão Social, Abertura, Capital, Localização, Porte e CNAE principal da empresa retornada.</div>
                    </li>
                </ul>
            </div>

            <!-- Payload Completo -->
            <div class="lg:col-span-8">
                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3"><i class="bi bi-code-slash"></i> Exemplo de Resposta (HTTP 200 OK)</h4>
                
                <div class="code-block rounded-xl p-5 shadow-inner max-h-[36rem] overflow-auto code-block-scroll">
<pre class="text-sm leading-relaxed"><code>{
  <span class="json-key">"consulta"</span>: {
    <span class="json-key">"endpoint"</span>: <span class="json-string">"search"</span>,
    <span class="json-key">"ok"</span>: <span class="json-boolean">true</span>,
    <span class="json-key">"filtros"</span>: {
      <span class="json-key">"uf"</span>: <span class="json-string">"MG"</span>,
      <span class="json-key">"municipio"</span>: <span class="json-null">null</span>,
      <span class="json-key">"municipio_codigo_resolvido"</span>: <span class="json-null">null</span>,
      <span class="json-key">"cep"</span>: <span class="json-null">null</span>,
      <span class="json-key">"situacao"</span>: <span class="json-number">2</span>,
      <span class="json-key">"matriz_filial"</span>: <span class="json-null">null</span>,
      <span class="json-key">"porte"</span>: <span class="json-number">1</span>,
      <span class="json-key">"natureza"</span>: <span class="json-null">null</span>,
      <span class="json-key">"cnae_principal"</span>: <span class="json-string">"6201501"</span>,
      <span class="json-key">"cnae_secundario"</span>: [],
      <span class="json-key">"abertura_de"</span>: <span class="json-null">null</span>,
      <span class="json-key">"abertura_ate"</span>: <span class="json-null">null</span>,
      <span class="json-key">"q"</span>: <span class="json-null">null</span>
    },
    <span class="json-key">"paginacao"</span>: {
      <span class="json-key">"per_page"</span>: <span class="json-number">500</span>,
      <span class="json-key">"current_page"</span>: <span class="json-number">1</span>,
      <span class="json-key">"next_page_url"</span>: <span class="json-string">"https://api.buscacnpjs.com/api/v1/search?page=2"</span>,
      <span class="json-key">"prev_page_url"</span>: <span class="json-null">null</span>
    },
    <span class="json-key">"ordenacao"</span>: <span class="json-string">"cnpj_asc"</span>
  },
  <span class="json-key">"data"</span>: [
    {
      <span class="json-key">"cnpj"</span>: <span class="json-string">"00237393000151"</span>,
      <span class="json-key">"razao_social"</span>: <span class="json-string">"INFFORMATICA SOFTWARE SCHOOL E CONSULTORIA LTDA"</span>,
      <span class="json-key">"nome_fantasia"</span>: <span class="json-string">"INFFORMATICA"</span>,
      <span class="json-key">"data_abertura"</span>: <span class="json-string">"05-10-1994"</span>,
      <span class="json-key">"capital_social"</span>: <span class="json-string">"R$ 20.000,00"</span>,
      <span class="json-key">"localizacao"</span>: {
        <span class="json-key">"uf"</span>: <span class="json-string">"MG"</span>,
        <span class="json-key">"municipio"</span>: {
          <span class="json-key">"codigo"</span>: <span class="json-number">4079</span>,
          <span class="json-key">"nome"</span>: <span class="json-string">"ARAXA"</span>
        }
      },
      <span class="json-key">"situacao_cadastral"</span>: {
        <span class="json-key">"codigo"</span>: <span class="json-number">2</span>,
        <span class="json-key">"descricao"</span>: <span class="json-string">"ATIVA"</span>
      },
      <span class="json-key">"porte"</span>: {
        <span class="json-key">"codigo"</span>: <span class="json-number">1</span>,
        <span class="json-key">"descricao"</span>: <span class="json-string">"MICRO EMPRESA"</span>
      },
      <span class="json-key">"natureza_juridica_codigo"</span>: <span class="json-number">2062</span>,
      <span class="json-key">"cnae_principal"</span>: <span class="json-string">"6201501"</span>
    },
    {
      <span class="json-key">"cnpj"</span>: <span class="json-string">"00376815000170"</span>,
      <span class="json-key">"razao_social"</span>: <span class="json-string">"2 N PRODUCOES WEB LTDA"</span>,
      <span class="json-key">"nome_fantasia"</span>: <span class="json-string">""</span>,
      <span class="json-key">"data_abertura"</span>: <span class="json-string">"13-12-1994"</span>,
      <span class="json-key">"capital_social"</span>: <span class="json-string">"R$ 30.000,00"</span>,
      <span class="json-key">"localizacao"</span>: {
        <span class="json-key">"uf"</span>: <span class="json-string">"MG"</span>,
        <span class="json-key">"municipio"</span>: {
          <span class="json-key">"codigo"</span>: <span class="json-number">4123</span>,
          <span class="json-key">"nome"</span>: <span class="json-string">"BELO HORIZONTE"</span>
        }
      },
      <span class="json-key">"situacao_cadastral"</span>: {
        <span class="json-key">"codigo"</span>: <span class="json-number">2</span>,
        <span class="json-key">"descricao"</span>: <span class="json-string">"ATIVA"</span>
      },
      <span class="json-key">"porte"</span>: {
        <span class="json-key">"codigo"</span>: <span class="json-number">1</span>,
        <span class="json-key">"descricao"</span>: <span class="json-string">"MICRO EMPRESA"</span>
      },
      <span class="json-key">"natureza_juridica_codigo"</span>: <span class="json-number">2240</span>,
      <span class="json-key">"cnae_principal"</span>: <span class="json-string">"6201501"</span>
    }
    <span class="text-gray-500">// ... array continua com até 500 itens por padrão ...</span>
  ]
}</code></pre>
                </div>
            </div>
        </div>
    </div>
</div>