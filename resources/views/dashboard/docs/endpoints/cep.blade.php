<!-- Estilo para a barra de rolagem do bloco de código -->
<style>
    .code-block-scroll::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    .code-block-scroll::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.2);
        border-radius: 8px;
    }
    .code-block-scroll::-webkit-scrollbar-thumb {
        background: #3f3f46;
        border-radius: 8px;
    }
    .code-block-scroll::-webkit-scrollbar-thumb:hover {
        background: #52525b;
    }
</style>

<!-- ENDPOINT 2: CEP (Estatísticas e Agrupamentos) -->
<div id="endpoint-cep" class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden endpoint-card">
    <div class="p-4 md:p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-[#004EA5]/5">
        <div class="flex items-center gap-3">
            <span class="bg-[#004EA5] text-white px-3 py-1.5 rounded-md text-xs font-bold tracking-wider shadow-sm">GET</span>
            <code class="text-lg font-bold text-[#004EA5]">/cep/{cep}</code>
        </div>
        <span class="text-xs font-semibold text-gray-600 bg-white border border-gray-200 px-3 py-1 rounded-full shadow-sm"><i class="bi bi-bar-chart-fill mr-1 text-[#004EA5]"></i> Analytics & Agrupamentos</span>
    </div>
    
    <div class="p-6">
        <p class="text-gray-700 text-sm mb-6 leading-relaxed bg-blue-50/50 p-4 rounded-lg border border-blue-100">
            Este endpoint funciona como um <strong>raio-x completo</strong> de um CEP. Em vez de retornar endereços detalhados, ele retorna estatísticas gerais (quantidade de matrizes, filiais, ativas, inativas), identifica as localidades (UF e Municípios) e gera <em>breakdowns</em> detalhados por Situação Cadastral, Porte Empresarial e Top 10 CNAEs. 
            <br><br>
            <i class="bi bi-info-circle text-[#004EA5]"></i> <strong>Nota:</strong> Para cada agrupamento estatístico, a API retorna um array (<code>cnpjs_ativos</code> ou <code>cnpjs</code>) contendo a <strong>lista completa</strong> (sem limites ou paginação) de todos os CNPJs que compõem aquele número.
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
                        <td class="px-4 py-3 font-mono font-semibold text-gray-800 border-b border-gray-50">cep</td>
                        <td class="px-4 py-3 border-b border-gray-50"><span class="bg-blue-50 text-[#004EA5] px-2 py-1 rounded text-xs font-medium border border-blue-100">string</span></td>
                        <td class="px-4 py-3 border-b border-gray-50"><strong>Obrigatório.</strong> O CEP a ser consultado. Deve possuir 8 dígitos numéricos válidos.</td>
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
                        <div><strong>consulta:</strong> Metadados da requisição, confirmando o endpoint e se as listas de CNPJs vieram completas.</div>
                    </li>
                    <li class="flex items-start gap-2 border-b border-gray-100 pb-2">
                        <i class="bi bi-check-circle-fill text-[#499F2D] mt-0.5"></i> 
                        <div><strong>localidades:</strong> Array com UF e código/descrição do Município (um CEP pode cruzar limites municipais), incluindo a lista de CNPJs ativos do local.</div>
                    </li>
                    <li class="flex items-start gap-2 border-b border-gray-100 pb-2">
                        <i class="bi bi-check-circle-fill text-[#499F2D] mt-0.5"></i> 
                        <div><strong>stats:</strong> Consolidação de métricas totais (ativos, inativos, matrizes, filiais) e o <em>range</em> de datas de início de atividades.</div>
                    </li>
                    <li class="flex items-start gap-2 border-b border-gray-100 pb-2">
                        <i class="bi bi-check-circle-fill text-[#499F2D] mt-0.5"></i> 
                        <div><strong>breakdowns:</strong> Agrupamentos estratégicos divididos em 3 categorias fundamentais:</div>
                    </li>
                </ul>
                <ul class="text-sm text-gray-600 space-y-1 ml-6 mt-2 list-disc">
                    <li><code>situacao_cadastral</code>: Ativa, Baixada, Inapta, etc.</li>
                    <li><code>porte_empresarial_ativos</code>: ME, EPP, Demais.</li>
                    <li><code>top_cnaes_principais_ativos</code>: Top 10 atividades principais.</li>
                </ul>

                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mt-8 mb-3"><i class="bi bi-exclamation-triangle"></i> Respostas de Erro</h4>
                <div class="space-y-2">
                    <div class="bg-red-50 border border-red-100 rounded-md p-3 text-sm">
                        <span class="font-bold text-red-700">HTTP 400 Bad Request</span><br>
                        <span class="text-red-600 font-mono text-xs">{"error": "CEP inválido"}</span>
                    </div>
                    <div class="bg-orange-50 border border-orange-100 rounded-md p-3 text-sm">
                        <span class="font-bold text-orange-700">HTTP 404 Not Found</span><br>
                        <span class="text-orange-600 font-mono text-xs">{"error": "CEP não encontrado"}</span>
                    </div>
                </div>
            </div>

            <!-- Payload Completo -->
            <div class="lg:col-span-8">
                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3"><i class="bi bi-code-slash"></i> Exemplo de Resposta (HTTP 200 OK)</h4>
                <!-- Adicionado max-h-[36rem], overflow-auto e a classe customizada do scroll -->
                <div class="code-block rounded-xl p-5 shadow-inner max-h-[38rem] overflow-auto code-block-scroll">
<pre class="text-sm leading-relaxed"><code>{
  <span class="json-key">"consulta"</span>: {
    <span class="json-key">"cep"</span>: <span class="json-string">"36036533"</span>,
    <span class="json-key">"endpoint"</span>: <span class="json-string">"cep"</span>,
    <span class="json-key">"ok"</span>: <span class="json-boolean">true</span>,
    <span class="json-key">"cnpjs_completos"</span>: <span class="json-boolean">true</span>
  },
  <span class="json-key">"localidades"</span>: [
    {
      <span class="json-key">"uf"</span>: <span class="json-string">"MG"</span>,
      <span class="json-key">"municipio"</span>: {
        <span class="json-key">"codigo"</span>: <span class="json-number">4733</span>,
        <span class="json-key">"descricao"</span>: <span class="json-string">"JUIZ DE FORA"</span>
      },
      <span class="json-key">"stats"</span>: {
        <span class="json-key">"total_estabelecimentos"</span>: <span class="json-number">40</span>,
        <span class="json-key">"ativos"</span>: <span class="json-number">21</span>,
        <span class="json-key">"matrizes"</span>: <span class="json-number">40</span>,
        <span class="json-key">"filiais"</span>: <span class="json-number">0</span>
      },
      <span class="json-key">"cnpjs_ativos"</span>: [
        <span class="json-string">"28175681000148"</span>,
        <span class="json-string">"34933213000198"</span>
        <span class="text-gray-500">// ... array completo com todos os 21 CNPJs</span>
      ]
    }
  ],
  <span class="json-key">"stats"</span>: {
    <span class="json-key">"total_estabelecimentos"</span>: <span class="json-number">40</span>,
    <span class="json-key">"total_ativos"</span>: <span class="json-number">21</span>,
    <span class="json-key">"total_nao_ativos"</span>: <span class="json-number">19</span>,
    <span class="json-key">"total_matrizes"</span>: <span class="json-number">40</span>,
    <span class="json-key">"total_filiais"</span>: <span class="json-number">0</span>,
    <span class="json-key">"cnpjs_distintos"</span>: <span class="json-number">40</span>,
    <span class="json-key">"empresas_distintas"</span>: <span class="json-number">40</span>,
    <span class="json-key">"datas_inicio_atividade_ativos"</span>: {
      <span class="json-key">"mais_antiga"</span>: <span class="json-string">"02-02-2016"</span>,
      <span class="json-key">"mais_recente"</span>: <span class="json-string">"03-12-2025"</span>
    }
  },
  <span class="json-key">"breakdowns"</span>: {
    <span class="json-key">"situacao_cadastral"</span>: [
      {
        <span class="json-key">"codigo"</span>: <span class="json-number">2</span>,
        <span class="json-key">"descricao"</span>: <span class="json-string">"ATIVA"</span>,
        <span class="json-key">"total"</span>: <span class="json-number">21</span>,
        <span class="json-key">"cnpjs"</span>: [ <span class="text-gray-500">/* array completo... */</span> ]
      },
      {
        <span class="json-key">"codigo"</span>: <span class="json-number">4</span>,
        <span class="json-key">"descricao"</span>: <span class="json-string">"INAPTA"</span>,
        <span class="json-key">"total"</span>: <span class="json-number">5</span>,
        <span class="json-key">"cnpjs"</span>: [ <span class="text-gray-500">/* array completo... */</span> ]
      }
    ],
    <span class="json-key">"porte_empresarial_ativos"</span>: [
      {
        <span class="json-key">"codigo"</span>: <span class="json-number">1</span>,
        <span class="json-key">"descricao"</span>: <span class="json-string">"MICRO EMPRESA"</span>,
        <span class="json-key">"total"</span>: <span class="json-number">17</span>,
        <span class="json-key">"cnpjs_ativos"</span>: [ <span class="text-gray-500">/* array completo... */</span> ]
      }
    ],
    <span class="json-key">"top_cnaes_principais_ativos"</span>: [
      {
        <span class="json-key">"codigo"</span>: <span class="json-string">"8599699"</span>,
        <span class="json-key">"descricao"</span>: <span class="json-string">"Outras atividades de ensino não especificadas..."</span>,
        <span class="json-key">"total"</span>: <span class="json-number">2</span>,
        <span class="json-key">"cnpjs_ativos"</span>: [
          <span class="json-string">"42260165000142"</span>,
          <span class="json-string">"63331809000116"</span>
        ]
      }
      <span class="text-gray-500">// ... retorna até o top 10 CNAEs</span>
    ]
  }
}</code></pre>
                </div>
            </div>
        </div>
    </div>
</div>