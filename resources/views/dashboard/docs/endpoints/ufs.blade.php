<!-- ENDPOINT 4: ESTATÍSTICAS (UFs) -->
<div id="endpoint-ufs" class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden endpoint-card">
    <div class="p-4 md:p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-[#004EA5]/5">
        <div class="flex items-center gap-3">
            <span class="bg-[#004EA5] text-white px-3 py-1.5 rounded-md text-xs font-bold tracking-wider shadow-sm">GET</span>
            <code class="text-lg font-bold text-[#004EA5]">/ufs</code>
        </div>
        <span class="text-xs font-semibold text-gray-600 bg-white border border-gray-200 px-3 py-1 rounded-full shadow-sm flex items-center">
            <i class="bi bi-clock-history mr-1 text-[#004EA5]"></i> Cache 3 Meses
        </span>
    </div>
    
    <div class="p-6">
        <p class="text-gray-700 text-sm mb-6 leading-relaxed bg-blue-50/50 p-4 rounded-lg border border-blue-100">
            Este endpoint retorna um <strong>consolidado estatístico completo do Brasil</strong>, agrupado por Unidade Federativa (UF). É a rota ideal para a construção de painéis analíticos (Dashboards) e mapas interativos de densidade empresarial, já que entrega até o percentual de distribuição pré-calculado.
            <br><br>
            <i class="bi bi-info-circle text-[#004EA5]"></i> <strong>Nota de Performance:</strong> Devido ao altíssimo volume de processamento necessário (milhões de registros cruzados), a resposta desta rota possui um cache ativo de longa duração.
        </p>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mt-6">
            
            <!-- Estrutura de Retorno Descritiva -->
            <div class="lg:col-span-4">
                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3"><i class="bi bi-diagram-3"></i> Estrutura do Retorno</h4>
                <ul class="text-sm text-gray-600 space-y-3">
                    <li class="flex items-start gap-2 border-b border-gray-100 pb-2">
                        <i class="bi bi-check-circle-fill text-[#499F2D] mt-0.5"></i> 
                        <div><strong>consulta:</strong> Detalhes da requisição e metadados vitais de cache (status, chave de armazenamento e tempo de vida estipulado).</div>
                    </li>
                    <li class="flex items-start gap-2 border-b border-gray-100 pb-2">
                        <i class="bi bi-check-circle-fill text-[#499F2D] mt-0.5"></i> 
                        <div><strong>stats:</strong> Totalizadores macro por estado: volume geral de estabelecimentos, ativos, baixados e número de municípios abrangidos.</div>
                    </li>
                    <li class="flex items-start gap-2 border-b border-gray-100 pb-2">
                        <i class="bi bi-check-circle-fill text-[#499F2D] mt-0.5"></i> 
                        <div><strong>inovacoes:</strong> O histórico ano-a-ano (últimos 3 anos) de quantas empresas abriram e quantas fecharam no estado (Excelente para gráficos de linha).</div>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="bi bi-check-circle-fill text-[#499F2D] mt-0.5"></i> 
                        <div><strong>distribuicoes:</strong> Quebra da situação cadastral contendo os totais e também o percentual proporcional (Ex: 34.55% das empresas no AC estão ATIVAS).</div>
                    </li>
                </ul>
            </div>

            <!-- Payload Completo -->
            <div class="lg:col-span-8">
                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3"><i class="bi bi-code-slash"></i> Exemplo de Resposta (HTTP 200 OK)</h4>
                
                <div class="code-block rounded-xl p-5 shadow-inner max-h-[36rem] overflow-auto code-block-scroll">
<pre class="text-sm leading-relaxed"><code>{
  <span class="json-key">"consulta"</span>: {
    <span class="json-key">"endpoint"</span>: <span class="json-string">"ufs"</span>,
    <span class="json-key">"anos"</span>: [
      <span class="json-number">2023</span>,
      <span class="json-number">2024</span>,
      <span class="json-number">2025</span>
    ],
    <span class="json-key">"ok"</span>: <span class="json-boolean">true</span>,
    <span class="json-key">"cache"</span>: {
      <span class="json-key">"enabled"</span>: <span class="json-boolean">true</span>,
      <span class="json-key">"key"</span>: <span class="json-string">"ufs:geral:v2:sem_natureza"</span>,
      <span class="json-key">"ttl"</span>: <span class="json-string">"3 meses"</span>
    }
  },
  <span class="json-key">"data"</span>: [
    {
      <span class="json-key">"uf"</span>: <span class="json-string">"AC"</span>,
      <span class="json-key">"stats"</span>: {
        <span class="json-key">"total_estabelecimentos"</span>: <span class="json-number">159615</span>,
        <span class="json-key">"total_ativas"</span>: <span class="json-number">55141</span>,
        <span class="json-key">"total_baixadas"</span>: <span class="json-number">74889</span>,
        <span class="json-key">"total_municipios"</span>: <span class="json-number">22</span>
      },
      <span class="json-key">"inovacoes"</span>: {
        <span class="json-key">"abertas_ultimos_3_anos"</span>: {
          <span class="json-key">"2023"</span>: <span class="json-number">7334</span>,
          <span class="json-key">"2024"</span>: <span class="json-number">10468</span>,
          <span class="json-key">"2025"</span>: <span class="json-number">9757</span>
        },
        <span class="json-key">"fechadas_ultimos_3_anos"</span>: {
          <span class="json-key">"2023"</span>: <span class="json-number">3987</span>,
          <span class="json-key">"2024"</span>: <span class="json-number">6599</span>,
          <span class="json-key">"2025"</span>: <span class="json-number">4929</span>
        }
      },
      <span class="json-key">"distribuicoes"</span>: {
        <span class="json-key">"situacao_cadastral"</span>: {
          <span class="json-key">"base"</span>: <span class="json-string">"estabelecimentos"</span>,
          <span class="json-key">"total_base"</span>: <span class="json-number">159615</span>,
          <span class="json-key">"itens"</span>: [
            {
              <span class="json-key">"codigo"</span>: <span class="json-number">1</span>,
              <span class="json-key">"descricao"</span>: <span class="json-string">"NULA"</span>,
              <span class="json-key">"total"</span>: <span class="json-number">117</span>,
              <span class="json-key">"percentual"</span>: <span class="json-number">0.07</span>
            },
            {
              <span class="json-key">"codigo"</span>: <span class="json-number">2</span>,
              <span class="json-key">"descricao"</span>: <span class="json-string">"ATIVA"</span>,
              <span class="json-key">"total"</span>: <span class="json-number">55141</span>,
              <span class="json-key">"percentual"</span>: <span class="json-number">34.55</span>
            },
            {
              <span class="json-key">"codigo"</span>: <span class="json-number">4</span>,
              <span class="json-key">"descricao"</span>: <span class="json-string">"INAPTA"</span>,
              <span class="json-key">"total"</span>: <span class="json-number">29170</span>,
              <span class="json-key">"percentual"</span>: <span class="json-number">18.28</span>
            },
            {
              <span class="json-key">"codigo"</span>: <span class="json-number">8</span>,
              <span class="json-key">"descricao"</span>: <span class="json-string">"BAIXADA"</span>,
              <span class="json-key">"total"</span>: <span class="json-number">74889</span>,
              <span class="json-key">"percentual"</span>: <span class="json-number">46.92</span>
            }
          ]
        }
      }
    },
    {
      <span class="json-key">"uf"</span>: <span class="json-string">"AL"</span>
      <span class="text-gray-500">// ... array continua com os dados dos demais estados do Brasil ...</span>
    }
  ]
}</code></pre>
                </div>
            </div>
        </div>
    </div>
</div>