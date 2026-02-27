<!-- ENDPOINT 5: DIRETÓRIO MUNICIPIOS -->
<div id="endpoint-directory" class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden endpoint-card">
    <div class="p-4 md:p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-[#004EA5]/5">
        <div class="flex items-center gap-3">
            <span class="bg-[#004EA5] text-white px-3 py-1.5 rounded-md text-xs font-bold tracking-wider shadow-sm">GET</span>
            <code class="text-lg font-bold text-[#004EA5]">/ufs/{uf}/municipios</code>
        </div>
        <span class="text-xs font-semibold text-gray-600 bg-white border border-gray-200 px-3 py-1 rounded-full shadow-sm flex items-center">
            <i class="bi bi-clock-history mr-1 text-[#004EA5]"></i> Cache 3 Meses
        </span>
    </div>
    
    <div class="p-6">
        <p class="text-gray-700 text-sm mb-6 leading-relaxed bg-blue-50/50 p-4 rounded-lg border border-blue-100">
            Esta rota detalha o cenário empresarial de um estado (UF) específico, listando <strong>todos os seus municípios</strong>. Os dados já vêm estrategicamente ordenados do município com maior número de empresas ativas para o menor.
            <br><br>
            <i class="bi bi-lightbulb-fill text-[#499F2D]"></i> <strong>Dica de Uso:</strong> É a estrutura perfeita para montar um portal de "Diretório" (ex: "Empresas em São Paulo > Empresas em Campinas"), ajudando drasticamente na indexação de SEO orgânico da sua plataforma.
        </p>

        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3"><i class="bi bi-link-45deg"></i> Parâmetros de Rota</h4>
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
                    <tr>
                        <td class="px-4 py-3 font-mono font-semibold text-gray-800 border-b border-gray-50">uf</td>
                        <td class="px-4 py-3 border-b border-gray-50"><span class="bg-blue-50 text-[#004EA5] px-2 py-1 rounded text-xs font-medium border border-blue-100">string</span></td>
                        <td class="px-4 py-3 border-b border-gray-50"><strong>Obrigatório.</strong> A sigla do estado com exatas 2 letras (ex: <code>SP</code>, <code>AM</code>, <code>MG</code>).</td>
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
                        <div><strong>consulta:</strong> Parâmetros usados na busca (UF, anos base) e status do cache.</div>
                    </li>
                    <li class="flex items-start gap-2 border-b border-gray-100 pb-2">
                        <i class="bi bi-check-circle-fill text-[#499F2D] mt-0.5"></i> 
                        <div><strong>municipio:</strong> Objeto identificador contendo o código IBGE e o nome oficial do município.</div>
                    </li>
                    <li class="flex items-start gap-2 border-b border-gray-100 pb-2">
                        <i class="bi bi-check-circle-fill text-[#499F2D] mt-0.5"></i> 
                        <div><strong>stats:</strong> Os totais absolutos de empresas daquela cidade específica (ativas, baixadas e geral).</div>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="bi bi-check-circle-fill text-[#499F2D] mt-0.5"></i> 
                        <div><strong>inovacoes:</strong> Ritmo de crescimento do município (empresas abertas vs fechadas) nos anos de 2023, 2024 e 2025.</div>
                    </li>
                </ul>

                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mt-8 mb-3"><i class="bi bi-exclamation-triangle"></i> Respostas de Erro</h4>
                <div class="space-y-2">
                    <div class="bg-red-50 border border-red-100 rounded-md p-3 text-sm">
                        <span class="font-bold text-red-700">HTTP 400 Bad Request</span><br>
                        <span class="text-red-600 font-mono text-xs">{"error": "UF inválida"}</span>
                    </div>
                </div>
            </div>

            <!-- Payload Completo -->
            <div class="lg:col-span-8">
                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3"><i class="bi bi-code-slash"></i> Exemplo de Resposta (HTTP 200 OK)</h4>
                
                <div class="code-block rounded-xl p-5 shadow-inner max-h-[36rem] overflow-auto code-block-scroll">
<pre class="text-sm leading-relaxed"><code>{
  <span class="json-key">"consulta"</span>: {
    <span class="json-key">"endpoint"</span>: <span class="json-string">"ufs/{uf}/municipios"</span>,
    <span class="json-key">"uf"</span>: <span class="json-string">"AM"</span>,
    <span class="json-key">"anos"</span>: [
      <span class="json-number">2023</span>,
      <span class="json-number">2024</span>,
      <span class="json-number">2025</span>
    ],
    <span class="json-key">"ok"</span>: <span class="json-boolean">true</span>,
    <span class="json-key">"cache"</span>: {
      <span class="json-key">"enabled"</span>: <span class="json-boolean">true</span>,
      <span class="json-key">"key"</span>: <span class="json-string">"dir:ufs:AM:municipios:v1"</span>,
      <span class="json-key">"ttl"</span>: <span class="json-string">"3 meses"</span>
    }
  },
  <span class="json-key">"data"</span>: [
    {
      <span class="json-key">"municipio"</span>: {
        <span class="json-key">"codigo"</span>: <span class="json-number">255</span>,
        <span class="json-key">"nome"</span>: <span class="json-string">"MANAUS"</span>,
        <span class="json-key">"uf"</span>: <span class="json-string">"AM"</span>
      },
      <span class="json-key">"stats"</span>: {
        <span class="json-key">"total_estabelecimentos"</span>: <span class="json-number">528311</span>,
        <span class="json-key">"total_ativas"</span>: <span class="json-number">215164</span>,
        <span class="json-key">"total_baixadas"</span>: <span class="json-number">217410</span>
      },
      <span class="json-key">"inovacoes"</span>: {
        <span class="json-key">"abertas_ultimos_3_anos"</span>: {
          <span class="json-key">"2023"</span>: <span class="json-number">34456</span>,
          <span class="json-key">"2024"</span>: <span class="json-number">38911</span>,
          <span class="json-key">"2025"</span>: <span class="json-number">50001</span>
        },
        <span class="json-key">"fechadas_ultimos_3_anos"</span>: {
          <span class="json-key">"2023"</span>: <span class="json-number">16646</span>,
          <span class="json-key">"2024"</span>: <span class="json-number">19416</span>,
          <span class="json-key">"2025"</span>: <span class="json-number">22214</span>
        }
      }
    },
    {
      <span class="json-key">"municipio"</span>: {
        <span class="json-key">"codigo"</span>: <span class="json-number">269</span>,
        <span class="json-key">"nome"</span>: <span class="json-string">"PARINTINS"</span>,
        <span class="json-key">"uf"</span>: <span class="json-string">"AM"</span>
      },
      <span class="json-key">"stats"</span>: {
        <span class="json-key">"total_estabelecimentos"</span>: <span class="json-number">14667</span>,
        <span class="json-key">"total_ativas"</span>: <span class="json-number">5397</span>,
        <span class="json-key">"total_baixadas"</span>: <span class="json-number">6225</span>
      },
      <span class="json-key">"inovacoes"</span>: {
        <span class="json-key">"abertas_ultimos_3_anos"</span>: {
          <span class="json-key">"2023"</span>: <span class="json-number">717</span>,
          <span class="json-key">"2024"</span>: <span class="json-number">949</span>,
          <span class="json-key">"2025"</span>: <span class="json-number">1573</span>
        },
        <span class="json-key">"fechadas_ultimos_3_anos"</span>: {
          <span class="json-key">"2023"</span>: <span class="json-number">293</span>,
          <span class="json-key">"2024"</span>: <span class="json-number">570</span>,
          <span class="json-key">"2025"</span>: <span class="json-number">410</span>
        }
      }
    }
    <span class="text-gray-500">// ... array continua com os demais municípios do estado,</span>
    <span class="text-gray-500">// sempre em ordem decrescente pelo número de ativas.</span>
  ]
}</code></pre>
                </div>
            </div>
        </div>
    </div>
</div>