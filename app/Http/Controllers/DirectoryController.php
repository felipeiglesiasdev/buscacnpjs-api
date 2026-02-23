<?php

namespace App\Http\Controllers;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DirectoryController extends Controller
{
    // =========================================================
    // ANOS FIXOS (VOCÊ PEDIU 2025,2024,2023)
    // =========================================================
    private array $anos = [2023, 2024, 2025];

    // =========================================================
    // ENDPOINT: GET /api/v1/ufs/{uf}/municipios
    // LISTA MUNICÍPIOS DO ESTADO + STATS
    // =========================================================
    public function municipiosPorUf(string $uf)
    {
        // =========================================================
        // NORMALIZAR UF
        // =========================================================
        $uf = strtoupper(trim($uf));

        if (strlen($uf) !== 2) {
            return response()->json(['error' => 'UF inválida'], 400);
        }

        // =========================================================
        // CACHE (3 MESES)
        // =========================================================
        $cacheKey = "dir:ufs:{$uf}:municipios:v1";

        return Cache::remember($cacheKey, now()->addMonths(3), function () use ($uf, $cacheKey) {

            // =========================================================
            // QUERY 1: STATS PRINCIPAIS POR MUNICÍPIO (NA UF)
            // =========================================================
            $base = DB::connection('cnpj')
                ->table('estabelecimentos_geral as e')
                ->where('e.uf', $uf)
                ->groupBy('e.municipio')
                ->selectRaw('e.municipio as municipio_codigo')
                ->selectRaw('COUNT(*) as total_estabelecimentos')
                ->selectRaw('SUM(CASE WHEN e.situacao_cadastral = 2 THEN 1 ELSE 0 END) as total_ativas')
                ->selectRaw('SUM(CASE WHEN e.situacao_cadastral = 8 THEN 1 ELSE 0 END) as total_baixadas')
                ->get()
                ->keyBy('municipio_codigo');

            // =========================================================
            // QUERY 2: NOMES DOS MUNICÍPIOS
            // =========================================================
            $municipiosNomes = DB::connection('cnpj')
                ->table('municipios')
                ->whereIn('codigo', $base->keys()->all())
                ->select('codigo', 'descricao')
                ->get()
                ->keyBy('codigo');

            // =========================================================
            // QUERY 3: ABERTAS 2023/2024/2025 (POR MUNICÍPIO)
            // =========================================================
            $abertasRaw = DB::connection('cnpj')
                ->table('estabelecimentos_geral as e')
                ->where('e.uf', $uf)
                ->whereNotNull('e.data_inicio_atividade')
                ->whereIn(DB::raw('YEAR(e.data_inicio_atividade)'), $this->anos)
                ->groupBy('e.municipio', DB::raw('YEAR(e.data_inicio_atividade)'))
                ->selectRaw('e.municipio as municipio_codigo')
                ->selectRaw('YEAR(e.data_inicio_atividade) as ano')
                ->selectRaw('COUNT(*) as total')
                ->get();

            // =========================================================
            // QUERY 4: FECHADAS (BAIXADAS) 2023/2024/2025 (POR MUNICÍPIO)
            // =========================================================
            $fechadasRaw = DB::connection('cnpj')
                ->table('estabelecimentos_geral as e')
                ->where('e.uf', $uf)
                ->where('e.situacao_cadastral', 8)
                ->whereNotNull('e.data_situacao_cadastral')
                ->whereIn(DB::raw('YEAR(e.data_situacao_cadastral)'), $this->anos)
                ->groupBy('e.municipio', DB::raw('YEAR(e.data_situacao_cadastral)'))
                ->selectRaw('e.municipio as municipio_codigo')
                ->selectRaw('YEAR(e.data_situacao_cadastral) as ano')
                ->selectRaw('COUNT(*) as total')
                ->get();

            // =========================================================
            // ORGANIZAR ABERTAS/FECHADAS EM MAPAS
            // =========================================================
            $mapAbertas = [];
            foreach ($abertasRaw as $r) {
                $m = (string)$r->municipio_codigo;
                if (!isset($mapAbertas[$m])) {
                    $mapAbertas[$m] = ['2023' => 0, '2024' => 0, '2025' => 0];
                }
                $mapAbertas[$m][(string)$r->ano] = (int)$r->total;
            }

            $mapFechadas = [];
            foreach ($fechadasRaw as $r) {
                $m = (string)$r->municipio_codigo;
                if (!isset($mapFechadas[$m])) {
                    $mapFechadas[$m] = ['2023' => 0, '2024' => 0, '2025' => 0];
                }
                $mapFechadas[$m][(string)$r->ano] = (int)$r->total;
            }

            // =========================================================
            // MONTAR LISTA FINAL
            // =========================================================
            $data = [];

            foreach ($base as $codigo => $row) {

                $nome = isset($municipiosNomes[$codigo]) ? $municipiosNomes[$codigo]->descricao : null;

                $data[] = [
                    'municipio' => [
                        'codigo' => (int)$codigo,
                        'nome' => $nome,
                        'uf' => $uf,
                    ],
                    'stats' => [
                        'total_estabelecimentos' => (int)$row->total_estabelecimentos,
                        'total_ativas' => (int)$row->total_ativas,
                        'total_baixadas' => (int)$row->total_baixadas,
                    ],
                    'inovacoes' => [
                        'abertas_ultimos_3_anos' => $mapAbertas[(string)$codigo] ?? ['2023' => 0, '2024' => 0, '2025' => 0],
                        'fechadas_ultimos_3_anos' => $mapFechadas[(string)$codigo] ?? ['2023' => 0, '2024' => 0, '2025' => 0],
                    ],
                ];
            }

            // ORDENAR POR TOTAL_ATIVAS (DESC)
            usort($data, function ($a, $b) {
                return ($b['stats']['total_ativas'] <=> $a['stats']['total_ativas']);
            });

            return response()->json([
                'consulta' => [
                    'endpoint' => 'ufs/{uf}/municipios',
                    'uf' => $uf,
                    'anos' => $this->anos,
                    'ok' => true,
                    'cache' => [
                        'enabled' => true,
                        'key' => $cacheKey,
                        'ttl' => '3 meses',
                    ],
                ],
                'data' => $data,
            ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        });
    }

    // =========================================================
    // RESOLVER MUNICIPIO SLUG -> CODIGO + NOME (CACHE 3 MESES)
    // =========================================================
    private function resolverMunicipioPorSlug(string $uf, string $municipioSlug): ?array
    {
        $uf = strtoupper(trim($uf));
        $municipioSlug = strtolower(trim($municipioSlug));

        // CACHE DO MAPA SLUG -> MUNICIPIO (POR UF)
        $cacheKey = "dir:mapa_municipios_slug:{$uf}:v1";

        $map = Cache::remember($cacheKey, now()->addMonths(3), function () use ($uf) {

            // PEGAR SÓ OS MUNICÍPIOS QUE EXISTEM NA UF (VIA ESTABELECIMENTOS)
            $rows = DB::connection('cnpj')
                ->table('estabelecimentos_geral as e')
                ->join('municipios as m', 'm.codigo', '=', 'e.municipio')
                ->where('e.uf', $uf)
                ->groupBy('e.municipio', 'm.descricao')
                ->selectRaw('e.municipio as codigo')
                ->selectRaw('m.descricao as nome')
                ->get();

            // MONTAR MAPA: slug => {codigo, nome}
            $out = [];

            foreach ($rows as $r) {
                $slug = Str::slug($r->nome); // "Juiz de Fora" -> "juiz-de-fora"
                $out[$slug] = [
                    'codigo' => (int)$r->codigo,
                    'nome' => $r->nome,
                    'slug' => $slug,
                ];
            }

            return $out;
        });

        return $map[$municipioSlug] ?? null;
    }

    // =========================================================
    // ENDPOINT: GET /api/v1/{uf}/{municipio_slug}/empresas
    // EX: /api/v1/mg/juiz-de-fora/empresas
    // =========================================================
    public function empresasPorMunicipioSlug(string $uf, string $municipio_slug)
    {
        // =========================================================
        // NORMALIZAR
        // =========================================================
        $uf = strtoupper(trim($uf));
        $municipio_slug = strtolower(trim($municipio_slug));

        if (strlen($uf) !== 2) {
            return response()->json(['error' => 'UF inválida'], 400);
        }

        // =========================================================
        // RESOLVER MUNICIPIO (SLUG -> CODIGO + NOME)
        // =========================================================
        $municipio = $this->resolverMunicipioPorSlug($uf, $municipio_slug);

        if (!$municipio) {
            return response()->json(['error' => 'Município não encontrado para esta UF'], 404);
        }

        $municipioCodigo = (int)$municipio['codigo'];

        // =========================================================
        // PAGINAÇÃO
        // =========================================================
        $perPage = (int) request()->query('per_page', 500);
        if ($perPage < 1) $perPage = 500;
        if ($perPage > 5000) $perPage = 5000;

        // =========================================================
        // QUERY BASE (SÓ ATIVAS + ORDEM POR CNPJ)
        // =========================================================
        $query = DB::connection('cnpj')
            ->table('estabelecimentos_geral as e')
            ->join('empresas as emp', 'emp.cnpj_basico', '=', 'e.cnpj_basico')
            ->where('e.uf', $uf)
            ->where('e.municipio', $municipioCodigo)
            ->where('e.situacao_cadastral', 2)
            ->selectRaw("CONCAT(e.cnpj_basico, e.cnpj_ordem, e.cnpj_dv) as cnpj")
            ->addSelect([
                'emp.razao_social',
                'e.nome_fantasia',
                'e.data_inicio_atividade',
                'emp.capital_social',
                'e.uf',
                'e.municipio',
                'e.situacao_cadastral',
                'e.cnpj_basico',
                'e.cnpj_ordem',
                'e.cnpj_dv',
            ]);

        // =========================================================
        // PAGINATE (SIMPLE = MAIS LEVE, NÃO CONTA TOTAL)
        // =========================================================
        $paginator = $query->simplePaginate($perPage);

        // =========================================================
        // FORMATAR ITENS DA PÁGINA
        // =========================================================
        $data = collect($paginator->items())->map(function ($r) use ($municipio) {

            return [
                'cnpj' => $r->cnpj,
                'razao_social' => $r->razao_social,
                'nome_fantasia' => $r->nome_fantasia,
                'data_abertura' => $r->data_inicio_atividade
                    ? date('d-m-Y', strtotime($r->data_inicio_atividade))
                    : null,
                'capital_social' => $r->capital_social !== null
                    ? 'R$ ' . number_format((float)$r->capital_social, 2, ',', '.')
                    : null,
                'localizacao' => [
                    'uf' => $r->uf,
                    'municipio' => [
                        'codigo' => (int)$r->municipio,
                        'nome' => $municipio['nome'],
                        'slug' => $municipio['slug'],
                    ],
                ],
                'situacao_cadastral' => [
                    'codigo' => 2,
                    'descricao' => 'ATIVA',
                ],
            ];
        })->values()->all();

        return response()->json([
            'consulta' => [
                'endpoint' => '{uf}/{municipio_slug}/empresas',
                'uf' => $uf,
                'municipio' => $municipio,
                'ok' => true,
                'filtros' => [
                    'situacao_cadastral' => 2,
                ],
                'paginacao' => [
                    'per_page' => $perPage,
                    'current_page' => $paginator->currentPage(),
                    'next_page_url' => $paginator->nextPageUrl(),
                    'prev_page_url' => $paginator->previousPageUrl(),
                ],
            ],
            'data' => $data,
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

}