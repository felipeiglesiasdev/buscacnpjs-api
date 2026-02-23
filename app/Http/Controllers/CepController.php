<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class CepController extends Controller
{
    // =========================================================
    // MAPAS DE TRADUÇÃO
    // =========================================================
    private array $situacaoMap = [
        1 => 'NULA',
        2 => 'ATIVA',
        3 => 'SUSPENSA',
        4 => 'INAPTA',
        8 => 'BAIXADA',
    ];

    private array $porteMap = [
        0 => 'NÃO INFORMADO',
        1 => 'MICRO EMPRESA',
        3 => 'EMPRESA DE PEQUENO PORTE',
        5 => 'DEMAIS',
    ];

    // =========================================================
    // FUNÇÃO PARA LIMPAR O CEP (REMOVER QUALQUER COISA QUE NÃO SEJA NÚMERO)
    // =========================================================
    private function limparCep($cep): string
    {
        return preg_replace('/\D/', '', (string)$cep);
    }

    // =========================================================
    // FUNÇÃO PARA VALIDAR FORMATO DO CEP
    // =========================================================
    private function validarCepFormato(string $cep): bool
    {
        // VERIFICAR SE POSSUI 8 DÍGITOS
        if (strlen($cep) !== 8) { return false; }

        // VERIFICAR SE CONTÉM APENAS NÚMEROS
        if (!ctype_digit($cep)) { return false; }

        return true;
    }

    // =========================================================
    // FUNÇÃO PARA FORMATAR DATA (D-M-Y)
    // =========================================================
    private function formatarData(?string $data): ?string
    {
        if (!$data) return null;
        return date('d-m-Y', strtotime($data));
    }

    // =========================================================
    // FUNÇÃO PARA TRADUZIR SITUAÇÃO CADASTRAL
    // =========================================================
    private function traduzirSituacao(?int $codigo): ?string
    {
        if ($codigo === null) return null;
        return $this->situacaoMap[$codigo] ?? 'DESCONHECIDA';
    }

    // =========================================================
    // FUNÇÃO PARA TRADUZIR PORTE
    // =========================================================
    private function traduzirPorte(?int $codigo): string
    {
        if ($codigo === null) return 'NÃO INFORMADO';
        return $this->porteMap[$codigo] ?? 'NÃO INFORMADO';
    }

    // =========================================================
    // FUNÇÃO PARA MONTAR CNPJ COMPLETO A PARTIR DO ESTABELECIMENTO
    // =========================================================
    private function montarCnpjCompleto($row): string
    {
        return (string)($row->cnpj_basico . $row->cnpj_ordem . $row->cnpj_dv);
    }

    // =========================================================
    // ENDPOINT PRINCIPAL: GET /api/v1/cep/{cep}
    // OBJETIVO:
    // - RETORNAR UF E MUNICÍPIO(S) ASSOCIADOS AO CEP
    // - RETORNAR QUANTIDADE DE EMPRESAS/ESTABELECIMENTOS ATIVOS NO CEP
    // - RETORNAR OUTROS STATS
    // - RETORNAR LISTA COMPLETA DE CNPJS EM CADA BLOCO/STAT (SEM LIMITES)
    // OBS:
    // - NÃO RETORNAR ENDEREÇO COMPLETO
    // =========================================================
    public function show($cep)
    {
        // LIMPAR CEP
        $cep = $this->limparCep($cep);

        // VALIDAR FORMATO
        if (!$this->validarCepFormato($cep)) {
            return response()->json(['error' => 'CEP inválido'], 400);
        }

        // =========================================================
        // QUERY 1: STATS GERAIS DO CEP
        // =========================================================
        $geral = DB::connection('cnpj')
            ->table('estabelecimentos_geral as e')
            ->where('e.cep', $cep)
            ->selectRaw('COUNT(*) as total_estabelecimentos')
            ->selectRaw('SUM(CASE WHEN e.situacao_cadastral = 2 THEN 1 ELSE 0 END) as total_ativos')
            ->selectRaw('SUM(CASE WHEN e.situacao_cadastral <> 2 THEN 1 ELSE 0 END) as total_nao_ativos')
            ->selectRaw('SUM(CASE WHEN e.identificador_matriz_filial = 1 THEN 1 ELSE 0 END) as total_matrizes')
            ->selectRaw('SUM(CASE WHEN e.identificador_matriz_filial = 2 THEN 1 ELSE 0 END) as total_filiais')
            ->selectRaw("COUNT(DISTINCT CONCAT(e.cnpj_basico, e.cnpj_ordem, e.cnpj_dv)) as cnpjs_distintos")
            ->selectRaw('COUNT(DISTINCT e.cnpj_basico) as empresas_distintas')
            ->first();

        // SE NÃO ACHOU NADA, 404
        if (!$geral || (int)$geral->total_estabelecimentos === 0) {
            return response()->json(['error' => 'CEP não encontrado'], 404);
        }

        // =========================================================
        // QUERY 2: LOCALIDADES (UF + MUNICÍPIO) DO CEP (COM STATS)
        // =========================================================
        $localidadesRaw = DB::connection('cnpj')
            ->table('estabelecimentos_geral as e')
            ->leftJoin('municipios as m', 'm.codigo', '=', 'e.municipio')
            ->where('e.cep', $cep)
            ->groupBy('e.uf', 'e.municipio', 'm.descricao')
            ->select([
                'e.uf',
                'e.municipio as municipio_codigo',
                'm.descricao as municipio_descricao',
            ])
            ->selectRaw('COUNT(*) as total_estabelecimentos')
            ->selectRaw('SUM(CASE WHEN e.situacao_cadastral = 2 THEN 1 ELSE 0 END) as ativos')
            ->selectRaw('SUM(CASE WHEN e.identificador_matriz_filial = 1 THEN 1 ELSE 0 END) as matrizes')
            ->selectRaw('SUM(CASE WHEN e.identificador_matriz_filial = 2 THEN 1 ELSE 0 END) as filiais')
            ->orderBy('e.uf')
            ->orderBy('m.descricao')
            ->get();

        // =========================================================
        // QUERY 3: BREAKDOWN POR SITUAÇÃO CADASTRAL (COM STATS)
        // =========================================================
        $situacoesRaw = DB::connection('cnpj')
            ->table('estabelecimentos_geral as e')
            ->where('e.cep', $cep)
            ->groupBy('e.situacao_cadastral')
            ->select('e.situacao_cadastral')
            ->selectRaw('COUNT(*) as total')
            ->orderBy('e.situacao_cadastral')
            ->get();

        // =========================================================
        // QUERY 4: PORTE (APENAS ATIVOS) (COM STATS)
        // =========================================================
        $portesAtivosRaw = DB::connection('cnpj')
            ->table('estabelecimentos_geral as e')
            ->join('empresas as emp', 'emp.cnpj_basico', '=', 'e.cnpj_basico')
            ->where('e.cep', $cep)
            ->where('e.situacao_cadastral', 2)
            ->groupBy('emp.porte_empresa')
            ->select('emp.porte_empresa')
            ->selectRaw('COUNT(*) as total')
            ->orderByDesc('total')
            ->get();

        // =========================================================
        // QUERY 5: TOP 10 CNAES PRINCIPAIS (APENAS ATIVOS) (COM STATS)
        // =========================================================
        $topCnaesAtivosRaw = DB::connection('cnpj')
            ->table('estabelecimentos_geral as e')
            ->leftJoin('cnaes as c', 'c.codigo', '=', 'e.cnae_fiscal_principal')
            ->where('e.cep', $cep)
            ->where('e.situacao_cadastral', 2)
            ->groupBy('e.cnae_fiscal_principal', 'c.descricao')
            ->select([
                'e.cnae_fiscal_principal as codigo',
                'c.descricao as descricao',
            ])
            ->selectRaw('COUNT(*) as total')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        // =========================================================
        // QUERY 6: RANGE DE DATAS DE INÍCIO (APENAS ATIVOS)
        // =========================================================
        $rangeDatasAtivos = DB::connection('cnpj')
            ->table('estabelecimentos_geral as e')
            ->where('e.cep', $cep)
            ->where('e.situacao_cadastral', 2)
            ->selectRaw('MIN(e.data_inicio_atividade) as mais_antiga')
            ->selectRaw('MAX(e.data_inicio_atividade) as mais_recente')
            ->first();

        // =========================================================
        // A PARTIR DAQUI: LISTAS COMPLETAS DE CNPJS POR "GRUPO"
        // =========================================================

        // =========================================================
        // QUERY 7: LISTA COMPLETA DE CNPJS ATIVOS POR LOCALIDADE (UF+MUNICÍPIO)
        // =========================================================
        $cnpjsAtivosPorLocalidade = [];
        $rowsLocalidadeAtivos = DB::connection('cnpj')
            ->table('estabelecimentos_geral as e')
            ->where('e.cep', $cep)
            ->where('e.situacao_cadastral', 2)
            ->select(['e.uf', 'e.municipio', 'e.cnpj_basico', 'e.cnpj_ordem', 'e.cnpj_dv'])
            ->orderBy('e.uf')
            ->orderBy('e.municipio')
            ->orderBy('e.cnpj_basico')
            ->orderBy('e.cnpj_ordem')
            ->orderBy('e.cnpj_dv')
            ->get();

        foreach ($rowsLocalidadeAtivos as $r) {
            $key = $r->uf . '|' . (string)$r->municipio;
            $cnpjsAtivosPorLocalidade[$key][] = $this->montarCnpjCompleto($r);
        }

        // =========================================================
        // QUERY 8: LISTA COMPLETA DE CNPJS POR SITUAÇÃO CADASTRAL
        // =========================================================
        $cnpjsPorSituacao = [];
        $rowsSituacao = DB::connection('cnpj')
            ->table('estabelecimentos_geral as e')
            ->where('e.cep', $cep)
            ->select(['e.situacao_cadastral', 'e.cnpj_basico', 'e.cnpj_ordem', 'e.cnpj_dv'])
            ->orderBy('e.situacao_cadastral')
            ->orderBy('e.cnpj_basico')
            ->orderBy('e.cnpj_ordem')
            ->orderBy('e.cnpj_dv')
            ->get();

        foreach ($rowsSituacao as $r) {
            $key = (int)$r->situacao_cadastral;
            $cnpjsPorSituacao[$key][] = $this->montarCnpjCompleto($r);
        }

        // =========================================================
        // QUERY 9: LISTA COMPLETA DE CNPJS ATIVOS POR PORTE
        // =========================================================
        $cnpjsAtivosPorPorte = [];
        $rowsPorteAtivos = DB::connection('cnpj')
            ->table('estabelecimentos_geral as e')
            ->join('empresas as emp', 'emp.cnpj_basico', '=', 'e.cnpj_basico')
            ->where('e.cep', $cep)
            ->where('e.situacao_cadastral', 2)
            ->select(['emp.porte_empresa', 'e.cnpj_basico', 'e.cnpj_ordem', 'e.cnpj_dv'])
            ->orderBy('emp.porte_empresa')
            ->orderBy('e.cnpj_basico')
            ->orderBy('e.cnpj_ordem')
            ->orderBy('e.cnpj_dv')
            ->get();

        foreach ($rowsPorteAtivos as $r) {
            $key = $r->porte_empresa === null ? null : (int)$r->porte_empresa;
            $cnpjsAtivosPorPorte[$key][] = $this->montarCnpjCompleto($r);
        }

        // =========================================================
        // QUERY 10: LISTA COMPLETA DE CNPJS ATIVOS POR CNAE (SOMENTE PARA OS TOP 10 CNAES)
        // =========================================================
        $topCnaesCodigos = $topCnaesAtivosRaw->pluck('codigo')->filter()->values()->all();

        $cnpjsAtivosPorCnae = [];
        if (!empty($topCnaesCodigos)) {
            $rowsCnaeAtivos = DB::connection('cnpj')
                ->table('estabelecimentos_geral as e')
                ->where('e.cep', $cep)
                ->where('e.situacao_cadastral', 2)
                ->whereIn('e.cnae_fiscal_principal', $topCnaesCodigos)
                ->select(['e.cnae_fiscal_principal', 'e.cnpj_basico', 'e.cnpj_ordem', 'e.cnpj_dv'])
                ->orderBy('e.cnae_fiscal_principal')
                ->orderBy('e.cnpj_basico')
                ->orderBy('e.cnpj_ordem')
                ->orderBy('e.cnpj_dv')
                ->get();

            foreach ($rowsCnaeAtivos as $r) {
                $key = (string)$r->cnae_fiscal_principal;
                $cnpjsAtivosPorCnae[$key][] = $this->montarCnpjCompleto($r);
            }
        }

        // =========================================================
        // MONTAR LOCALIDADES (INJETAR LISTA COMPLETA DE CNPJS ATIVOS)
        // =========================================================
        $localidades = $localidadesRaw
            ->map(function ($row) use ($cnpjsAtivosPorLocalidade) {

                $key = $row->uf . '|' . (string)$row->municipio_codigo;

                return [
                    'uf' => $row->uf,
                    'municipio' => [
                        'codigo' => $row->municipio_codigo,
                        'descricao' => $row->municipio_descricao,
                    ],
                    'stats' => [
                        'total_estabelecimentos' => (int)$row->total_estabelecimentos,
                        'ativos' => (int)$row->ativos,
                        'matrizes' => (int)$row->matrizes,
                        'filiais' => (int)$row->filiais,
                    ],
                    // LISTA COMPLETA DE CNPJS ATIVOS DAQUELA UF+MUNICÍPIO
                    'cnpjs_ativos' => $cnpjsAtivosPorLocalidade[$key] ?? [],
                ];
            })
            ->values()
            ->all();

        // =========================================================
        // MONTAR SITUAÇÕES (INJETAR LISTA COMPLETA DE CNPJS)
        // =========================================================
        $situacoes = $situacoesRaw
            ->map(function ($row) use ($cnpjsPorSituacao) {

                $codigo = (int)$row->situacao_cadastral;

                return [
                    'codigo' => $codigo,
                    'descricao' => $this->traduzirSituacao($codigo),
                    'total' => (int)$row->total,
                    // LISTA COMPLETA DE CNPJS DAQUELA SITUAÇÃO
                    'cnpjs' => $cnpjsPorSituacao[$codigo] ?? [],
                ];
            })
            ->values()
            ->all();

        // =========================================================
        // MONTAR PORTES (INJETAR LISTA COMPLETA DE CNPJS ATIVOS)
        // =========================================================
        $portesAtivos = $portesAtivosRaw
            ->map(function ($row) use ($cnpjsAtivosPorPorte) {

                $codigo = $row->porte_empresa !== null ? (int)$row->porte_empresa : null;

                return [
                    'codigo' => $codigo,
                    'descricao' => $this->traduzirPorte($codigo),
                    'total' => (int)$row->total,
                    // LISTA COMPLETA DE CNPJS ATIVOS DAQUELE PORTE
                    'cnpjs_ativos' => $cnpjsAtivosPorPorte[$codigo] ?? [],
                ];
            })
            ->values()
            ->all();

        // =========================================================
        // MONTAR TOP CNAES (INJETAR LISTA COMPLETA DE CNPJS ATIVOS PARA CADA TOP CNAE)
        // =========================================================
        $topCnaesAtivos = $topCnaesAtivosRaw
            ->map(function ($row) use ($cnpjsAtivosPorCnae) {

                $codigo = (string)$row->codigo;

                return [
                    'codigo' => $codigo,
                    'descricao' => $row->descricao,
                    'total' => (int)$row->total,
                    // LISTA COMPLETA DE CNPJS ATIVOS DAQUELE CNAE (SOMENTE TOP 10 CNAES)
                    'cnpjs_ativos' => $cnpjsAtivosPorCnae[$codigo] ?? [],
                ];
            })
            ->values()
            ->all();

        // =========================================================
        // MONTAR RESPOSTA FINAL
        // =========================================================
        $payload = [
            'consulta' => [
                'cep' => $cep,
                'endpoint' => 'cep',
                'ok' => true,
                // INFORMAR QUE AS LISTAS VÊM COMPLETAS
                'cnpjs_completos' => true,
            ],
            'localidades' => $localidades,
            'stats' => [
                'total_estabelecimentos' => (int)$geral->total_estabelecimentos,
                'total_ativos' => (int)$geral->total_ativos,
                'total_nao_ativos' => (int)$geral->total_nao_ativos,
                'total_matrizes' => (int)$geral->total_matrizes,
                'total_filiais' => (int)$geral->total_filiais,
                'cnpjs_distintos' => (int)$geral->cnpjs_distintos,
                'empresas_distintas' => (int)$geral->empresas_distintas,
                'datas_inicio_atividade_ativos' => [
                    'mais_antiga' => $this->formatarData($rangeDatasAtivos->mais_antiga ?? null),
                    'mais_recente' => $this->formatarData($rangeDatasAtivos->mais_recente ?? null),
                ],
            ],
            'breakdowns' => [
                'situacao_cadastral' => $situacoes,
                'porte_empresarial_ativos' => $portesAtivos,
                'top_cnaes_principais_ativos' => $topCnaesAtivos,
            ],
        ];

        // RETORNAR JSON
        return response()->json($payload, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    // =========================================================
    // FUNÇÃO PARA FORMATAR VALOR MONETÁRIO EM PADRÃO BRASILEIRO
    // =========================================================
    private function formatarMoeda($valor)
    {
        if ($valor === null) return null;
        return 'R$ ' . number_format((float)$valor, 2, ',', '.');
    }

    public function estabelecimentos($cep)
    {
        // LIMPAR CEP
        $cep = $this->limparCep($cep);

        // VALIDAR FORMATO
        if (!$this->validarCepFormato($cep)) {
            return response()->json(['error' => 'CEP inválido'], 400);
        }

        // DEFINIR PAGINAÇÃO COM LIMITES (EVITA ABUSO)
        $perPage = (int) request('per_page', 50);
        if ($perPage < 1) { $perPage = 1; }
        if ($perPage > 200) { $perPage = 200; }

        // ATIVOS POR PADRÃO
        $somenteAtivos = request()->boolean('ativos', true);

        // QUERY BASE
        $query = DB::connection('cnpj')
            ->table('estabelecimentos_geral as e')
            ->join('empresas as emp', 'emp.cnpj_basico', '=', 'e.cnpj_basico')
            ->leftJoin('municipios as m', 'm.codigo', '=', 'e.municipio')
            ->where('e.cep', $cep)
            ->select([
                // CAMPOS DO ESTABELECIMENTO
                'e.cnpj_basico',
                'e.cnpj_ordem',
                'e.cnpj_dv',
                'e.uf',
                'e.situacao_cadastral',
                'e.data_inicio_atividade',

                // CAMPOS DA EMPRESA
                'emp.razao_social',
                'emp.capital_social',

                // MUNICÍPIO
                'm.descricao as municipio_descricao',
            ])
            // ORDENAR PARA TER RESULTADO ESTÁVEL ENTRE PÁGINAS
            ->orderBy('emp.razao_social')
            ->orderBy('e.cnpj_basico')
            ->orderBy('e.cnpj_ordem')
            ->orderBy('e.cnpj_dv');

        // FILTRAR ATIVOS SE SOLICITADO
        if ($somenteAtivos) {
            $query->where('e.situacao_cadastral', 2);
        }

        // PAGINAR
        $page = $query->paginate($perPage);

        // TRANSFORMAR ITENS PARA FORMATO FINAL
        $data = collect($page->items())->map(function ($row) {
            return [
                'cnpj' => $row->cnpj_basico . $row->cnpj_ordem . $row->cnpj_dv,
                'razao_social' => $row->razao_social,
                'data_abertura' => $row->data_inicio_atividade
                    ? date('d-m-Y', strtotime($row->data_inicio_atividade))
                    : null,
                'capital_social' => $this->formatarMoeda($row->capital_social),
                'uf' => $row->uf,
                'municipio' => $row->municipio_descricao,
                'situacao_cadastral' => [
                    'codigo' => (int)$row->situacao_cadastral,
                    'descricao' => $this->traduzirSituacao((int)$row->situacao_cadastral),
                ],
            ];
        })->values()->all();

        // RESPONDER PADRÃO
        return response()->json([
            'consulta' => [
                'cep' => $cep,
                'endpoint' => 'cep_estabelecimentos',
                'ativos' => (bool)$somenteAtivos,
            ],
            'meta' => [
                'page' => $page->currentPage(),
                'per_page' => $page->perPage(),
                'total' => $page->total(),
                'last_page' => $page->lastPage(),
            ],
            'data' => $data,
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}