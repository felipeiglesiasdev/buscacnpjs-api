<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class UfsGeralController extends Controller
{
    // =========================================================
    // ANOS FIXOS (CONFORME VOCÊ PEDIU)
    // =========================================================
    private array $anos = [2023, 2024, 2025];

    // =========================================================
    // MAPA DE SITUAÇÃO CADASTRAL
    // =========================================================
    private array $situacaoMap = [
        1 => 'NULA',
        2 => 'ATIVA',
        3 => 'SUSPENSA',
        4 => 'INAPTA',
        8 => 'BAIXADA',
    ];

    // =========================================================
    // FUNÇÃO PARA CALCULAR PERCENTUAL
    // =========================================================
    private function pct(int $parte, int $total): float
    {
        if ($total <= 0) return 0.0;
        return round(($parte / $total) * 100, 2);
    }

    // =========================================================
    // ENDPOINT: GET /api/v1/ufs
    // RETORNA:
    // - STATS POR UF
    // - TOTAL MUNICÍPIOS
    // - ABERTAS/FECHADAS (2023-2025)
    // - DISTRIBUIÇÃO SITUAÇÃO (NÚMERO + %)
    // - CACHE 3 MESES
    // =========================================================
    public function index()
    {
        // =========================================================
        // CACHE DE 3 MESES
        // =========================================================
        $cacheKey = 'ufs:geral:v2:sem_natureza';

        return Cache::remember($cacheKey, now()->addMonths(3), function () use ($cacheKey) {

            // =========================================================
            // QUERY 1: STATS PRINCIPAIS POR UF (ESTABELECIMENTOS)
            // =========================================================
            $statsUf = DB::connection('cnpj')
                ->table('estabelecimentos_geral as e')
                ->groupBy('e.uf')
                ->selectRaw('e.uf as uf')
                ->selectRaw('COUNT(*) as total_estabelecimentos')
                ->selectRaw('SUM(CASE WHEN e.situacao_cadastral = 2 THEN 1 ELSE 0 END) as total_ativas')
                ->selectRaw('SUM(CASE WHEN e.situacao_cadastral = 8 THEN 1 ELSE 0 END) as total_baixadas')
                ->orderBy('uf')
                ->get();

            // =========================================================
            // QUERY 2: TOTAL DE MUNICÍPIOS POR UF (DISTINCT MUNICIPIO)
            // =========================================================
            $municipiosUf = DB::connection('cnpj')
                ->table('estabelecimentos_geral as e')
                ->groupBy('e.uf')
                ->selectRaw('e.uf as uf')
                ->selectRaw('COUNT(DISTINCT e.municipio) as total_municipios')
                ->get()
                ->keyBy('uf');

            // =========================================================
            // QUERY 3: ABERTAS POR UF NOS ANOS 2023/2024/2025
            // =========================================================
            $abertas = DB::connection('cnpj')
                ->table('estabelecimentos_geral as e')
                ->whereIn(DB::raw('YEAR(e.data_inicio_atividade)'), $this->anos)
                ->groupBy('e.uf', DB::raw('YEAR(e.data_inicio_atividade)'))
                ->selectRaw('e.uf as uf')
                ->selectRaw('YEAR(e.data_inicio_atividade) as ano')
                ->selectRaw('COUNT(*) as total')
                ->get();

            // =========================================================
            // QUERY 4: FECHADAS (BAIXADAS) POR UF NOS ANOS 2023/2024/2025
            // =========================================================
            $fechadas = DB::connection('cnpj')
                ->table('estabelecimentos_geral as e')
                ->where('e.situacao_cadastral', 8)
                ->whereIn(DB::raw('YEAR(e.data_situacao_cadastral)'), $this->anos)
                ->groupBy('e.uf', DB::raw('YEAR(e.data_situacao_cadastral)'))
                ->selectRaw('e.uf as uf')
                ->selectRaw('YEAR(e.data_situacao_cadastral) as ano')
                ->selectRaw('COUNT(*) as total')
                ->get();

            // =========================================================
            // QUERY 5: DISTRIBUIÇÃO DE SITUAÇÃO CADASTRAL POR UF
            // =========================================================
            $distSituacaoRaw = DB::connection('cnpj')
                ->table('estabelecimentos_geral as e')
                ->groupBy('e.uf', 'e.situacao_cadastral')
                ->selectRaw('e.uf as uf, e.situacao_cadastral, COUNT(*) as total')
                ->orderBy('uf')
                ->orderBy('e.situacao_cadastral')
                ->get();

            // =========================================================
            // ORGANIZAR MAPAS: UF -> {2023,2024,2025}
            // =========================================================
            $mapAbertas = [];
            foreach ($abertas as $r) {
                $uf = (string)$r->uf;
                if (!isset($mapAbertas[$uf])) {
                    $mapAbertas[$uf] = ['2023' => 0, '2024' => 0, '2025' => 0];
                }
                $mapAbertas[$uf][(string)$r->ano] = (int)$r->total;
            }

            $mapFechadas = [];
            foreach ($fechadas as $r) {
                $uf = (string)$r->uf;
                if (!isset($mapFechadas[$uf])) {
                    $mapFechadas[$uf] = ['2023' => 0, '2024' => 0, '2025' => 0];
                }
                $mapFechadas[$uf][(string)$r->ano] = (int)$r->total;
            }

            // =========================================================
            // ORGANIZAR SITUAÇÕES: UF => ARRAY
            // =========================================================
            $distSituacaoPorUf = [];
            foreach ($distSituacaoRaw as $r) {
                $uf = (string)$r->uf;
                if (!isset($distSituacaoPorUf[$uf])) $distSituacaoPorUf[$uf] = [];

                $codigo = (int)$r->situacao_cadastral;

                $distSituacaoPorUf[$uf][] = [
                    'codigo' => $codigo,
                    'descricao' => $this->situacaoMap[$codigo] ?? 'DESCONHECIDA',
                    'total' => (int)$r->total,
                    'percentual' => 0.0,
                ];
            }

            // =========================================================
            // MONTAR RESPOSTA FINAL (CALCULANDO PERCENTUAIS)
            // =========================================================
            $data = $statsUf->map(function ($row) use (
                $mapAbertas,
                $mapFechadas,
                $municipiosUf,
                $distSituacaoPorUf
            ) {

                $uf = (string)$row->uf;
                $totalEstabs = (int)$row->total_estabelecimentos;

                // CALCULAR PERCENTUAIS DA DISTRIBUIÇÃO DE SITUAÇÃO (BASE: ESTABELECIMENTOS)
                $situacoes = $distSituacaoPorUf[$uf] ?? [];
                foreach ($situacoes as &$s) {
                    $s['percentual'] = $this->pct((int)$s['total'], $totalEstabs);
                }
                unset($s);

                return [
                    'uf' => $uf,
                    'stats' => [
                        'total_estabelecimentos' => $totalEstabs,
                        'total_ativas' => (int)$row->total_ativas,
                        'total_baixadas' => (int)$row->total_baixadas,
                        'total_municipios' => isset($municipiosUf[$uf]) ? (int)$municipiosUf[$uf]->total_municipios : 0,
                    ],
                    'inovacoes' => [
                        'abertas_ultimos_3_anos' => $mapAbertas[$uf] ?? ['2023' => 0, '2024' => 0, '2025' => 0],
                        'fechadas_ultimos_3_anos' => $mapFechadas[$uf] ?? ['2023' => 0, '2024' => 0, '2025' => 0],
                    ],
                    'distribuicoes' => [
                        'situacao_cadastral' => [
                            'base' => 'estabelecimentos',
                            'total_base' => $totalEstabs,
                            'itens' => $situacoes,
                        ],
                    ],
                ];
            })->values()->all();

            return response()->json([
                'consulta' => [
                    'endpoint' => 'ufs',
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
}