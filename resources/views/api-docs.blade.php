<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentação da API | Consulta CNPJ</title>

    <!-- Fonte Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (CDN para visualização rápida) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Configuração do Tailwind para a Fonte Poppins -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            900: '#14532d',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Prism.js para Syntax Highlighting do JSON -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css" rel="stylesheet" />

    <style>
        body { font-family: 'Poppins', sans-serif; }
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        
        .nav-link.active {
            background-color: #1e293b; /* Slate 800 */
            color: #10b981; /* Emerald 500 */
            border-left: 4px solid #10b981;
        }

        pre[class*="language-"] {
            border-radius: 0.5rem;
            margin: 1.5rem 0;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased flex h-screen overflow-hidden">

    <!-- Sidebar de Navegação -->
    <aside class="w-72 bg-gray-900 text-gray-300 flex-shrink-0 h-full overflow-y-auto flex flex-col transition-transform duration-300 z-20 absolute lg:relative transform -translate-x-full lg:translate-x-0" id="sidebar">
        <div class="p-6 bg-gray-950 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-white tracking-wide">API Dados<span class="text-brand-500">BR</span></h1>
                <p class="text-xs text-gray-400 mt-1">Versão 1.0.0</p>
            </div>
            <button class="lg:hidden text-gray-400 hover:text-white" onclick="toggleSidebar()">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <nav class="flex-1 py-4">
            <div class="px-6 mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Introdução</div>
            <a href="#intro" class="nav-link block px-6 py-2 hover:bg-gray-800 hover:text-white transition-colors">Visão Geral</a>
            <a href="#healthcheck" class="nav-link block px-6 py-2 hover:bg-gray-800 hover:text-white transition-colors">Healthcheck</a>

            <div class="px-6 mb-2 mt-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Consultas Core</div>
            <a href="#cnpj-show" class="nav-link block px-6 py-2 hover:bg-gray-800 hover:text-white transition-colors">Consulta de CNPJ</a>
            <a href="#search" class="nav-link block px-6 py-2 hover:bg-gray-800 hover:text-white transition-colors">Busca Avançada (Search)</a>

            <div class="px-6 mb-2 mt-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Localidade (CEP)</div>
            <a href="#cep-show" class="nav-link block px-6 py-2 hover:bg-gray-800 hover:text-white transition-colors">Estatísticas do CEP</a>
            <a href="#cep-estabelecimentos" class="nav-link block px-6 py-2 hover:bg-gray-800 hover:text-white transition-colors">Empresas no CEP</a>

            <div class="px-6 mb-2 mt-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Diretório (Geográfico)</div>
            <a href="#dir-ufs" class="nav-link block px-6 py-2 hover:bg-gray-800 hover:text-white transition-colors">Resumo por UFs</a>
            <a href="#dir-municipios" class="nav-link block px-6 py-2 hover:bg-gray-800 hover:text-white transition-colors">Municípios da UF</a>
            <a href="#dir-empresas" class="nav-link block px-6 py-2 hover:bg-gray-800 hover:text-white transition-colors">Empresas por Município</a>
        </nav>
    </aside>

    <!-- Overlay Mobile -->
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 z-10 hidden lg:hidden" id="mobile-overlay" onclick="toggleSidebar()"></div>

    <!-- Conteúdo Principal -->
    <main class="flex-1 h-full overflow-y-auto bg-white relative scroll-smooth" id="main-content">
        <!-- Header Mobile -->
        <div class="lg:hidden sticky top-0 bg-white border-b border-gray-200 p-4 flex items-center z-10 shadow-sm">
            <button onclick="toggleSidebar()" class="text-gray-600 hover:text-gray-900 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>
            <h1 class="ml-4 text-lg font-bold text-gray-900">Documentação API</h1>
        </div>

        <div class="max-w-5xl mx-auto p-8 lg:p-12 pb-32">
            
            <!-- INTRODUÇÃO -->
            <section id="intro" class="mb-16">
                <h1 class="text-4xl font-bold text-gray-900 mb-4">Bem-vindo à Documentação da API</h1>
                <p class="text-lg text-gray-600 mb-6">Esta API fornece acesso estruturado, rápido e robusto aos dados de empresas, estabelecimentos e estatísticas geográficas do Brasil baseados nos dados da Receita Federal.</p>
                
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-md mb-8">
                    <p class="text-blue-800 text-sm font-medium"><strong>Base URL:</strong> <code class="bg-blue-100 px-2 py-1 rounded text-blue-900 font-mono">https://seusite.com.br/api/v1</code></p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
                    <div class="border border-gray-200 rounded-lg p-6 shadow-sm">
                        <h3 class="font-bold text-gray-900 mb-2">Formato de Resposta</h3>
                        <p class="text-sm text-gray-600">Todas as respostas são enviadas no formato <code>application/json</code> utilizando formatação unescaped unicode e formatação limpa.</p>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-6 shadow-sm">
                        <h3 class="font-bold text-gray-900 mb-2">Paginação</h3>
                        <p class="text-sm text-gray-600">Rotas que retornam listas suportam os parâmetros de query <code>?page=1</code> e <code>?per_page=500</code>. O limite máximo seguro é 5000 itens por página em algumas rotas.</p>
                    </div>
                </div>
            </section>

            <hr class="border-gray-200 mb-16">

            <!-- HEALTHCHECK -->
            <section id="healthcheck" class="mb-16 pt-8">
                <div class="flex items-center gap-3 mb-4">
                    <span class="px-3 py-1 bg-green-100 text-green-700 font-bold text-xs rounded-full uppercase tracking-wider">GET</span>
                    <h2 class="text-2xl font-bold text-gray-900">Healthcheck</h2>
                </div>
                <p class="text-gray-600 mb-4">Endpoint simples para verificar se a API está online e respondendo adequadamente.</p>
                
                <div class="bg-gray-100 p-4 rounded-lg font-mono text-sm mb-6 overflow-x-auto text-gray-800 border border-gray-200">
                    /api/v1/teste
                </div>

<pre><code class="language-json">{
    "ok": true
}</code></pre>
            </section>

            <hr class="border-gray-200 mb-16">

            <!-- CNPJ SHOW -->
            <section id="cnpj-show" class="mb-16 pt-8">
                <div class="flex items-center gap-3 mb-4">
                    <span class="px-3 py-1 bg-green-100 text-green-700 font-bold text-xs rounded-full uppercase tracking-wider">GET</span>
                    <h2 class="text-2xl font-bold text-gray-900">Consulta de CNPJ Completa</h2>
                </div>
                <p class="text-gray-600 mb-4">Retorna todos os dados de uma empresa e de seu respectivo estabelecimento usando joins otimizados. Inclui informações base, atividades (CNAEs), endereço formatado, telefones e Quadro de Sócios e Administradores (QSA).</p>
                
                <div class="bg-gray-100 p-4 rounded-lg font-mono text-sm mb-6 text-gray-800 border border-gray-200">
                    /api/v1/cnpj/<span class="text-brand-600 font-bold">{cnpj}</span>
                </div>

                <h4 class="font-bold text-gray-900 mb-3 text-sm uppercase tracking-wider">Parâmetros de Rota</h4>
                <div class="overflow-x-auto mb-8">
                    <table class="w-full text-sm text-left text-gray-500 border border-gray-200 rounded-lg">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 border-b">Parâmetro</th>
                                <th class="px-6 py-3 border-b">Tipo</th>
                                <th class="px-6 py-3 border-b">Descrição</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="bg-white">
                                <td class="px-6 py-4 font-mono font-medium text-gray-900">cnpj</td>
                                <td class="px-6 py-4 text-brand-600">string</td>
                                <td class="px-6 py-4">O CNPJ que deseja consultar. Pode ser enviado com ou sem máscara (pontos, barra e traço serão ignorados internamente). Ex: <code>00000000000191</code></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

<pre><code class="language-json">{
    "informacoes": {
        "cnpj": "00000000000191",
        "razao_social": "BANCO DO BRASIL SA",
        "nome_fantasia": "DIRECAO GERAL",
        "natureza_juridica": "2038 - SOCIEDADE DE ECONOMIA MISTA",
        "capital_social": "R$ 90.000.000.000,00",
        "porte": "DEMAIS",
        "tipo_estabelecimento": "MATRIZ",
        "data_abertura": "01-08-1966",
        "situacao_cadastral": "ATIVA"
    },
    "atividades": {
        "cnae_principal": "6422100 - Bancos múltiplos, com carteira comercial",
        "cnaes_secundarios": [
            {
                "codigo": "6499999",
                "descricao": "Outras intermediações financeiras não especificadas anteriormente"
            }
        ]
    },
    "endereco": {
        "tipo_logradouro": "QUADRA",
        "logradouro": "SAUN QUADRA 5 LOTE B",
        "numero": "S/N",
        "complemento": "ANDAR 1 A 16 SALA 101 A 1601",
        "bairro": "ASA NORTE",
        "cep": "70040912",
        "uf": "DF",
        "municipio": "BRASILIA"
    },
    "telefones": {
        "telefone1": "(61) 34939002",
        "telefone2": null
    },
    "qsa": [
        {
            "nome": "NOME DO PRESIDENTE",
            "qualificacao": "16 - Presidente",
            "data_entrada": "01-01-2023"
        }
    ]
}</code></pre>
            </section>

            <hr class="border-gray-200 mb-16">

            <!-- SEARCH (BUSCA AVANÇADA) -->
            <section id="search" class="mb-16 pt-8">
                <div class="flex items-center gap-3 mb-4">
                    <span class="px-3 py-1 bg-green-100 text-green-700 font-bold text-xs rounded-full uppercase tracking-wider">GET</span>
                    <h2 class="text-2xl font-bold text-gray-900">Busca Avançada (Search)</h2>
                </div>
                <p class="text-gray-600 mb-4">Endpoint poderoso para cruzamento de dados. Permite aplicar múltiplos filtros simultâneos para encontrar listas específicas de empresas. Retorna dados paginados e ordenados pelo número do CNPJ.</p>
                
                <div class="bg-gray-100 p-4 rounded-lg font-mono text-sm mb-6 text-gray-800 border border-gray-200">
                    /api/v1/search<span class="text-gray-400">?uf=SP&situacao=2&porte=1</span>
                </div>

                <h4 class="font-bold text-gray-900 mb-3 text-sm uppercase tracking-wider">Parâmetros de Query (Filtros Opcionais)</h4>
                <div class="overflow-x-auto mb-8">
                    <table class="w-full text-sm text-left text-gray-500 border border-gray-200 rounded-lg">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 border-b">Parâmetro</th>
                                <th class="px-6 py-3 border-b">Valores Aceitos</th>
                                <th class="px-6 py-3 border-b">Descrição</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="bg-white border-b">
                                <td class="px-6 py-4 font-mono font-medium text-gray-900">uf</td>
                                <td class="px-6 py-4"><code>SP, RJ, MG...</code></td>
                                <td class="px-6 py-4">Sigla do estado com 2 letras.</td>
                            </tr>
                            <tr class="bg-gray-50 border-b">
                                <td class="px-6 py-4 font-mono font-medium text-gray-900">municipio</td>
                                <td class="px-6 py-4"><code>juiz-de-fora</code> ou <code>3136702</code></td>
                                <td class="px-6 py-4">Aceita o código IBGE/Tomador do município ou o slug do nome (requer o envio da <code>uf</code> junto caso use slug).</td>
                            </tr>
                            <tr class="bg-white border-b">
                                <td class="px-6 py-4 font-mono font-medium text-gray-900">cep</td>
                                <td class="px-6 py-4"><code>01311000</code></td>
                                <td class="px-6 py-4">Apenas os 8 dígitos numéricos.</td>
                            </tr>
                            <tr class="bg-gray-50 border-b">
                                <td class="px-6 py-4 font-mono font-medium text-gray-900">situacao</td>
                                <td class="px-6 py-4"><code>1, 2, 3, 4, 8, all, *</code></td>
                                <td class="px-6 py-4"><strong>Padrão: 2 (Ativa).</strong><br>1: Nula, 2: Ativa, 3: Suspensa, 4: Inapta, 8: Baixada. Para buscar ignorando a situação, envie <code>all</code>.</td>
                            </tr>
                            <tr class="bg-white border-b">
                                <td class="px-6 py-4 font-mono font-medium text-gray-900">porte</td>
                                <td class="px-6 py-4"><code>0, 1, 3, 5</code></td>
                                <td class="px-6 py-4">0: Não Informado, 1: ME, 3: EPP, 5: Demais.</td>
                            </tr>
                            <tr class="bg-gray-50 border-b">
                                <td class="px-6 py-4 font-mono font-medium text-gray-900">matriz_filial</td>
                                <td class="px-6 py-4"><code>1, 2</code></td>
                                <td class="px-6 py-4">1: Matriz, 2: Filial.</td>
                            </tr>
                            <tr class="bg-white border-b">
                                <td class="px-6 py-4 font-mono font-medium text-gray-900">cnae_principal</td>
                                <td class="px-6 py-4"><code>0000000</code></td>
                                <td class="px-6 py-4">Apenas os 7 dígitos do CNAE Principal.</td>
                            </tr>
                            <tr class="bg-gray-50 border-b">
                                <td class="px-6 py-4 font-mono font-medium text-gray-900">cnae_secundario</td>
                                <td class="px-6 py-4"><code>0000000</code></td>
                                <td class="px-6 py-4">Pode ser enviado múltiplas vezes na query string para buscar empresas que possuam qualquer um dos cnaes listados (Lógica OR). Ex: <code>?cnae_secundario=123&cnae_secundario=456</code></td>
                            </tr>
                            <tr class="bg-white border-b">
                                <td class="px-6 py-4 font-mono font-medium text-gray-900">q</td>
                                <td class="px-6 py-4"><code>texto</code></td>
                                <td class="px-6 py-4">Busca textual parcial no Nome Fantasia OU Razão Social. (Max 60 chars).</td>
                            </tr>
                            <tr class="bg-gray-50 border-b">
                                <td class="px-6 py-4 font-mono font-medium text-gray-900">abertura_de / abertura_ate</td>
                                <td class="px-6 py-4"><code>YYYY-MM-DD</code></td>
                                <td class="px-6 py-4">Filtra por intervalo de data de abertura do estabelecimento.</td>
                            </tr>
                            <tr class="bg-white">
                                <td class="px-6 py-4 font-mono font-medium text-gray-900">page / per_page</td>
                                <td class="px-6 py-4">Inteiros</td>
                                <td class="px-6 py-4">Paginação. <code>per_page</code> varia de 1 a 5000 (padrão 500).</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

<pre><code class="language-json">{
    "consulta": {
        "endpoint": "search",
        "ok": true,
        "filtros": {
            "uf": "SP",
            "municipio": null,
            "municipio_codigo_resolvido": null,
            "cep": null,
            "situacao": 2,
            "matriz_filial": null,
            "porte": 1,
            "natureza": null,
            "cnae_principal": null,
            "cnae_secundario": [],
            "abertura_de": null,
            "abertura_ate": null,
            "q": null
        },
        "paginacao": {
            "per_page": 500,
            "current_page": 1,
            "next_page_url": "http://seusite.com.br/api/v1/search?page=2",
            "prev_page_url": null
        },
        "ordenacao": "cnpj_asc"
    },
    "data": [
        {
            "cnpj": "00000000000191",
            "razao_social": "EMPRESA EXEMPLO LTDA",
            "nome_fantasia": "EXEMPLO ME",
            "data_abertura": "15-05-2020",
            "capital_social": "R$ 10.000,00",
            "localizacao": {
                "uf": "SP",
                "municipio": {
                    "codigo": 3550308,
                    "nome": "SAO PAULO"
                }
            },
            "situacao_cadastral": {
                "codigo": 2,
                "descricao": "ATIVA"
            },
            "porte": {
                "codigo": 1,
                "descricao": "MICRO EMPRESA"
            },
            "natureza_juridica_codigo": 2062,
            "cnae_principal": "6201501"
        }
    ]
}</code></pre>
            </section>

            <hr class="border-gray-200 mb-16">

            <!-- CEP SHOW (STATS) -->
            <section id="cep-show" class="mb-16 pt-8">
                <div class="flex items-center gap-3 mb-4">
                    <span class="px-3 py-1 bg-green-100 text-green-700 font-bold text-xs rounded-full uppercase tracking-wider">GET</span>
                    <h2 class="text-2xl font-bold text-gray-900">Estatísticas do CEP</h2>
                </div>
                <p class="text-gray-600 mb-4">Informa o município e estado pertencentes a um determinado CEP, além de gerar inteligência de mercado completa: contagem de estabelecimentos, distinção de matrizes e filiais, além do <i>breakdown</i> com as listas completas de CNPJs agrupadas por situação cadastral, porte e Top 10 CNAEs.</p>
                
                <div class="bg-gray-100 p-4 rounded-lg font-mono text-sm mb-6 text-gray-800 border border-gray-200">
                    /api/v1/cep/<span class="text-brand-600 font-bold">{cep}</span>
                </div>

<pre><code class="language-json">{
    "consulta": {
        "cep": "01311000",
        "endpoint": "cep",
        "ok": true,
        "cnpjs_completos": true
    },
    "localidades": [
        {
            "uf": "SP",
            "municipio": {
                "codigo": 3550308,
                "descricao": "SAO PAULO"
            },
            "stats": {
                "total_estabelecimentos": 1540,
                "ativos": 850,
                "matrizes": 1200,
                "filiais": 340
            },
            "cnpjs_ativos": [
                "00000000000191",
                "11111111000191"
            ]
        }
    ],
    "stats": {
        "total_estabelecimentos": 1540,
        "total_ativos": 850,
        "total_nao_ativos": 690,
        "total_matrizes": 1200,
        "total_filiais": 340,
        "cnpjs_distintos": 1540,
        "empresas_distintas": 1500,
        "datas_inicio_atividade_ativos": {
            "mais_antiga": "01-01-1950",
            "mais_recente": "10-10-2023"
        }
    },
    "breakdowns": {
        "situacao_cadastral": [
            {
                "codigo": 2,
                "descricao": "ATIVA",
                "total": 850,
                "cnpjs": ["..."]
            },
            {
                "codigo": 8,
                "descricao": "BAIXADA",
                "total": 600,
                "cnpjs": ["..."]
            }
        ],
        "porte_empresarial_ativos": [
            {
                "codigo": 5,
                "descricao": "DEMAIS",
                "total": 500,
                "cnpjs_ativos": ["..."]
            }
        ],
        "top_cnaes_principais_ativos": [
            {
                "codigo": "6920601",
                "descricao": "Atividades de contabilidade",
                "total": 45,
                "cnpjs_ativos": ["..."]
            }
        ]
    }
}</code></pre>
            </section>

            <hr class="border-gray-200 mb-16">

            <!-- CEP ESTABELECIMENTOS -->
            <section id="cep-estabelecimentos" class="mb-16 pt-8">
                <div class="flex items-center gap-3 mb-4">
                    <span class="px-3 py-1 bg-green-100 text-green-700 font-bold text-xs rounded-full uppercase tracking-wider">GET</span>
                    <h2 class="text-2xl font-bold text-gray-900">Empresas do CEP (Lista Paginada)</h2>
                </div>
                <p class="text-gray-600 mb-4">Ideal para exibir uma tabela contendo as empresas registradas em um determinado CEP.</p>
                
                <div class="bg-gray-100 p-4 rounded-lg font-mono text-sm mb-6 text-gray-800 border border-gray-200">
                    /api/v1/cep/<span class="text-brand-600 font-bold">{cep}</span>/estabelecimentos<span class="text-gray-400">?per_page=50&ativos=true</span>
                </div>

                <ul class="list-disc pl-5 mb-6 text-gray-600 text-sm">
                    <li><strong>ativos</strong> (booleano): Por padrão é <code>true</code>. Se enviar <code>false</code> (ou 0), trará também baixadas, inaptas, etc.</li>
                    <li><strong>per_page</strong>: Limite por página (máximo 200 para esta rota).</li>
                </ul>

<pre><code class="language-json">{
    "consulta": {
        "cep": "01311000",
        "endpoint": "cep_estabelecimentos",
        "ativos": true
    },
    "meta": {
        "page": 1,
        "per_page": 50,
        "total": 850,
        "last_page": 17
    },
    "data": [
        {
            "cnpj": "00000000000191",
            "razao_social": "EMPRESA DA PAULISTA LTDA",
            "data_abertura": "10-05-2015",
            "capital_social": "R$ 50.000,00",
            "uf": "SP",
            "municipio": "SAO PAULO",
            "situacao_cadastral": {
                "codigo": 2,
                "descricao": "ATIVA"
            }
        }
    ]
}</code></pre>
            </section>

            <hr class="border-gray-200 mb-16">

            <!-- DIRETORIO UFS -->
            <section id="dir-ufs" class="mb-16 pt-8">
                <div class="flex items-center gap-3 mb-4">
                    <span class="px-3 py-1 bg-green-100 text-green-700 font-bold text-xs rounded-full uppercase tracking-wider">GET</span>
                    <h2 class="text-2xl font-bold text-gray-900">Diretório: Resumo por UFs</h2>
                </div>
                <p class="text-gray-600 mb-4">Retorna estatísticas em nível estadual. Inclui inovações (empresas abertas e fechadas nos últimos 3 anos: 2023, 2024, 2025) e as distribuições de situação cadastral com cálculo de porcentagem relativo ao total. <i>Nota: Esta rota possui cache de 3 meses na API.</i></p>
                
                <div class="bg-gray-100 p-4 rounded-lg font-mono text-sm mb-6 text-gray-800 border border-gray-200">
                    /api/v1/ufs
                </div>

<pre><code class="language-json">{
    "consulta": {
        "endpoint": "ufs",
        "anos": [2023, 2024, 2025],
        "ok": true,
        "cache": {
            "enabled": true,
            "key": "ufs:geral:v2:sem_natureza",
            "ttl": "3 meses"
        }
    },
    "data": [
        {
            "uf": "MG",
            "stats": {
                "total_estabelecimentos": 5200000,
                "total_ativas": 2300000,
                "total_baixadas": 2500000,
                "total_municipios": 853
            },
            "inovacoes": {
                "abertas_ultimos_3_anos": {
                    "2023": 150000,
                    "2024": 165000,
                    "2025": 40000
                },
                "fechadas_ultimos_3_anos": {
                    "2023": 80000,
                    "2024": 95000,
                    "2025": 12000
                }
            },
            "distribuicoes": {
                "situacao_cadastral": {
                    "base": "estabelecimentos",
                    "total_base": 5200000,
                    "itens": [
                        {
                            "codigo": 2,
                            "descricao": "ATIVA",
                            "total": 2300000,
                            "percentual": 44.23
                        }
                    ]
                }
            }
        }
    ]
}</code></pre>
            </section>

            <hr class="border-gray-200 mb-16">

            <!-- DIRETORIO MUNICÍPIOS -->
            <section id="dir-municipios" class="mb-16 pt-8">
                <div class="flex items-center gap-3 mb-4">
                    <span class="px-3 py-1 bg-green-100 text-green-700 font-bold text-xs rounded-full uppercase tracking-wider">GET</span>
                    <h2 class="text-2xl font-bold text-gray-900">Diretório: Municípios da UF</h2>
                </div>
                <p class="text-gray-600 mb-4">Lista todos os municípios pertencentes a uma sigla de UF informada, ordenados pela quantidade de empresas ativas (do maior para o menor). Retorna contagens de aberturas e fechamentos por cidade. <i>Possui cache de 3 meses.</i></p>
                
                <div class="bg-gray-100 p-4 rounded-lg font-mono text-sm mb-6 text-gray-800 border border-gray-200">
                    /api/v1/ufs/<span class="text-brand-600 font-bold">{uf}</span>/municipios
                </div>

<pre><code class="language-json">{
    "consulta": {
        "endpoint": "ufs/{uf}/municipios",
        "uf": "MG",
        "anos": [2023, 2024, 2025],
        "ok": true
    },
    "data": [
        {
            "municipio": {
                "codigo": 3106200,
                "nome": "BELO HORIZONTE",
                "uf": "MG"
            },
            "stats": {
                "total_estabelecimentos": 800000,
                "total_ativas": 400000,
                "total_baixadas": 350000
            },
            "inovacoes": {
                "abertas_ultimos_3_anos": {
                    "2023": 35000,
                    "2024": 38000,
                    "2025": 9000
                },
                "fechadas_ultimos_3_anos": {
                    "2023": 15000,
                    "2024": 18000,
                    "2025": 3000
                }
            }
        },
        {
            "municipio": {
                "codigo": 3136702,
                "nome": "JUIZ DE FORA",
                "uf": "MG"
            },
            "stats": {
                "total_estabelecimentos": 150000,
                "total_ativas": 70000,
                "total_baixadas": 60000
            },
            "inovacoes": {
                "abertas_ultimos_3_anos": { "2023": 5000, "2024": 5200, "2025": 1000 },
                "fechadas_ultimos_3_anos": { "2023": 2000, "2024": 2500, "2025": 400 }
            }
        }
    ]
}</code></pre>
            </section>

            <hr class="border-gray-200 mb-16">

            <!-- DIRETORIO EMPRESAS POR MUNICIPIO SLUG -->
            <section id="dir-empresas" class="mb-16 pt-8">
                <div class="flex items-center gap-3 mb-4">
                    <span class="px-3 py-1 bg-green-100 text-green-700 font-bold text-xs rounded-full uppercase tracking-wider">GET</span>
                    <h2 class="text-2xl font-bold text-gray-900">Diretório: Empresas por Município</h2>
                </div>
                <p class="text-gray-600 mb-4">Uma forma amigável (SEO-friendly) de acessar a lista de empresas ativas de um município específico. A rota entende <i>slugs</i> (ex: juiz-de-fora) e resolve para o código correto da cidade. Utiliza paginação simples (rápida) devido ao volume de dados. Apenas retorna empresas <b>ATIVAS</b> nesta rota.</p>
                
                <div class="bg-gray-100 p-4 rounded-lg font-mono text-sm mb-6 text-gray-800 border border-gray-200">
                    /api/v1/<span class="text-brand-600 font-bold">{uf}</span>/<span class="text-brand-600 font-bold">{municipio_slug}</span>/empresas<span class="text-gray-400">?page=1&per_page=500</span>
                </div>

<pre><code class="language-json">{
    "consulta": {
        "endpoint": "{uf}/{municipio_slug}/empresas",
        "uf": "MG",
        "municipio": {
            "codigo": 3136702,
            "nome": "JUIZ DE FORA",
            "slug": "juiz-de-fora"
        },
        "ok": true,
        "filtros": {
            "situacao_cadastral": 2
        },
        "paginacao": {
            "per_page": 500,
            "current_page": 1,
            "next_page_url": "http://seusite.com.br/api/v1/MG/juiz-de-fora/empresas?page=2",
            "prev_page_url": null
        }
    },
    "data": [
        {
            "cnpj": "00000000000191",
            "razao_social": "EMPRESA DE JUIZ DE FORA LTDA",
            "nome_fantasia": "JF NEGOCIOS",
            "data_abertura": "20-01-2018",
            "capital_social": "R$ 100.000,00",
            "localizacao": {
                "uf": "MG",
                "municipio": {
                    "codigo": 3136702,
                    "nome": "JUIZ DE FORA",
                    "slug": "juiz-de-fora"
                }
            },
            "situacao_cadastral": {
                "codigo": 2,
                "descricao": "ATIVA"
            }
        }
    ]
}</code></pre>
            </section>

        </div>
    </main>

    <!-- Scripts para Interatividade -->
    <script>
        // Funcionalidade do Menu Mobile
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('mobile-overlay');

        function toggleSidebar() {
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        // Active Link Highlighting on Scroll
        const sections = document.querySelectorAll("section[id]");
        const navLinks = document.querySelectorAll(".nav-link");

        window.addEventListener("scroll", () => {
            let current = "";
            sections.forEach((section) => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.clientHeight;
                // Ajuste de offset para compensar padding/header
                if (pageYOffset >= (sectionTop - 150)) {
                    current = section.getAttribute("id");
                }
            });

            navLinks.forEach((a) => {
                a.classList.remove("active");
                if (a.getAttribute("href").includes(current) && current !== "") {
                    a.classList.add("active");
                }
            });
        });

        // Fechar menu mobile ao clicar em um link
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 1024) {
                    toggleSidebar();
                }
            });
        });
    </script>
    
    <!-- Prism.js para Colorir o JSON -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-json.min.js"></script>
</body>
</html>