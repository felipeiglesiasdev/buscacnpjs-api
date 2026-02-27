<!-- ENDPOINT 1: CNPJ -->
<div id="endpoint-cnpj" class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden endpoint-card">
    <div class="p-4 md:p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-[#004EA5]/5">
        <div class="flex items-center gap-3">
            <span class="bg-[#004EA5] text-white px-3 py-1.5 rounded-md text-xs font-bold tracking-wider shadow-sm">GET</span>
            <code class="text-lg font-bold text-[#004EA5]">/cnpj/{cnpj}</code>
        </div>
        <span class="text-xs font-semibold text-gray-600 bg-white border border-gray-200 px-3 py-1 rounded-full shadow-sm"><i class="bi bi-filetype-json mr-1 text-[#499F2D]"></i> JSON Completo</span>
    </div>
    
    <div class="p-6">
        <p class="text-gray-700 text-sm mb-6 leading-relaxed bg-blue-50/50 p-4 rounded-lg border border-blue-100">
            Este é o endpoint principal da API. Ele retorna os dados cadastrais completos de uma empresa, cruzando informações do Estabelecimento com a Matriz, incluindo o Quadro de Sócios e Administradores (QSA), capital social formatado, naturezas jurídicas e CNAEs (Principal e Secundários). 
            <br><br>
            <i class="bi bi-info-circle text-[#004EA5]"></i> <strong>Nota:</strong> A API limpa automaticamente qualquer máscara do CNPJ recebido. Você pode enviar "00.000.000/0001-91" ou "00000000000191", o sistema processará corretamente.
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
                        <td class="px-4 py-3 font-mono font-semibold text-gray-800 border-b border-gray-50">cnpj</td>
                        <td class="px-4 py-3 border-b border-gray-50"><span class="bg-blue-50 text-[#004EA5] px-2 py-1 rounded text-xs font-medium border border-blue-100">string</span></td>
                        <td class="px-4 py-3 border-b border-gray-50"><strong>Obrigatório.</strong> O CNPJ a ser consultado. Deve possuir 14 dígitos válidos.</td>
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
                        <div><strong>informacoes:</strong> Objeto principal com CNPJ, Razão Social, Natureza Jurídica, Porte, Tipo de Estabelecimento (Matriz/Filial) e Situação.</div>
                    </li>
                    <li class="flex items-start gap-2 border-b border-gray-100 pb-2">
                        <i class="bi bi-check-circle-fill text-[#499F2D] mt-0.5"></i> 
                        <div><strong>atividades:</strong> Agrupa o CNAE principal completo e um array contendo os códigos e descrições dos CNAEs secundários.</div>
                    </li>
                    <li class="flex items-start gap-2 border-b border-gray-100 pb-2">
                        <i class="bi bi-check-circle-fill text-[#499F2D] mt-0.5"></i> 
                        <div><strong>endereco:</strong> Localização detalhada da empresa, separando logradouro, número, complemento, CEP e informações do IBGE.</div>
                    </li>
                    <li class="flex items-start gap-2 border-b border-gray-100 pb-2">
                        <i class="bi bi-check-circle-fill text-[#499F2D] mt-0.5"></i> 
                        <div><strong>telefones:</strong> Telefones de contato disponíveis na base da Receita Federal (podendo ser <code>null</code>).</div>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="bi bi-check-circle-fill text-[#499F2D] mt-0.5"></i> 
                        <div><strong>qsa:</strong> Quadro societário detalhado, listando o nome dos sócios/administradores, suas qualificações e a data de entrada na sociedade.</div>
                    </li>
                </ul>

                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mt-8 mb-3"><i class="bi bi-exclamation-triangle"></i> Respostas de Erro</h4>
                <div class="space-y-2">
                    <div class="bg-red-50 border border-red-100 rounded-md p-3 text-sm">
                        <span class="font-bold text-red-700">HTTP 400 Bad Request</span><br>
                        <span class="text-red-600 font-mono text-xs">{"error": "CNPJ inválido"}</span>
                    </div>
                    <div class="bg-orange-50 border border-orange-100 rounded-md p-3 text-sm">
                        <span class="font-bold text-orange-700">HTTP 404 Not Found</span><br>
                        <span class="text-orange-600 font-mono text-xs">{"error": "CNPJ não encontrado"}</span>
                    </div>
                </div>
            </div>

            <!-- Payload Completo -->
            <div class="lg:col-span-8">
                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3"><i class="bi bi-code-slash"></i> Exemplo de Resposta (HTTP 200 OK)</h4>
                <div class="code-block rounded-xl p-5 shadow-inner max-h-[38rem] overflow-auto code-block-scroll">
<pre class="text-sm overflow-x-auto leading-relaxed"><code>{
  <span class="json-key">"informacoes"</span>: {
    <span class="json-key">"cnpj"</span>: <span class="json-string">"12345678000199"</span>,
    <span class="json-key">"razao_social"</span>: <span class="json-string">"TECHCORP SOLUCOES DIGITAIS LTDA"</span>,
    <span class="json-key">"nome_fantasia"</span>: <span class="json-string">"TECHCORP BRASIL"</span>,
    <span class="json-key">"natureza_juridica"</span>: <span class="json-string">"2062 - Sociedade Empresária Limitada"</span>,
    <span class="json-key">"capital_social"</span>: <span class="json-string">"R$ 500.000,00"</span>,
    <span class="json-key">"porte"</span>: <span class="json-string">"DEMAIS"</span>,
    <span class="json-key">"tipo_estabelecimento"</span>: <span class="json-string">"MATRIZ"</span>,
    <span class="json-key">"data_abertura"</span>: <span class="json-string">"15-08-2015"</span>,
    <span class="json-key">"situacao_cadastral"</span>: <span class="json-string">"ATIVA"</span>
  },
  <span class="json-key">"atividades"</span>: {
    <span class="json-key">"cnae_principal"</span>: <span class="json-string">"6201501 - Desenvolvimento de programas de computador customizados"</span>,
    <span class="json-key">"cnaes_secundarios"</span>: [
      {
        <span class="json-key">"codigo"</span>: <span class="json-string">"6204000"</span>,
        <span class="json-key">"descricao"</span>: <span class="json-string">"Consultoria em tecnologia da informação"</span>
      },
      {
        <span class="json-key">"codigo"</span>: <span class="json-string">"6311900"</span>,
        <span class="json-key">"descricao"</span>: <span class="json-string">"Tratamento de dados, provedores de serviços de aplicação..."</span>
      }
    ]
  },
  <span class="json-key">"endereco"</span>: {
    <span class="json-key">"tipo_logradouro"</span>: <span class="json-string">"AVENIDA"</span>,
    <span class="json-key">"logradouro"</span>: <span class="json-string">"PAULISTA"</span>,
    <span class="json-key">"numero"</span>: <span class="json-string">"1000"</span>,
    <span class="json-key">"complemento"</span>: <span class="json-string">"CONJ 152 ANDAR 15"</span>,
    <span class="json-key">"bairro"</span>: <span class="json-string">"BELA VISTA"</span>,
    <span class="json-key">"cep"</span>: <span class="json-string">"01310100"</span>,
    <span class="json-key">"uf"</span>: <span class="json-string">"SP"</span>,
    <span class="json-key">"municipio"</span>: <span class="json-string">"SAO PAULO"</span>
  },
  <span class="json-key">"telefones"</span>: {
    <span class="json-key">"telefone1"</span>: <span class="json-string">"(11) 30005555"</span>,
    <span class="json-key">"telefone2"</span>: <span class="json-null">null</span>
  },
  <span class="json-key">"qsa"</span>: [
    {
      <span class="json-key">"nome"</span>: <span class="json-string">"CARLOS EDUARDO DA SILVA"</span>,
      <span class="json-key">"qualificacao"</span>: <span class="json-string">"49 - Sócio-Administrador"</span>,
      <span class="json-key">"data_entrada"</span>: <span class="json-string">"15-08-2015"</span>
    }
  ]
}</code></pre>
                </div>
            </div>
        </div>
    </div>
</div>