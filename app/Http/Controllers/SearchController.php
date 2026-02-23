<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class SearchController extends Controller
{
    // =========================================================
    // MAPA SITUAÇÃO CADASTRAL
    // =========================================================
    private array $situacaoMap = [
        1 => 'NULA',
        2 => 'ATIVA',
        3 => 'SUSPENSA',
        4 => 'INAPTA',
        8 => 'BAIXADA',
    ];

    // =========================================================
    // MAPA PORTE (EMPRESAS)
    // =========================================================
    private array $porteMap = [
        0 => 'NÃO INFORMADO',
        1 => 'MICRO EMPRESA',
        3 => 'EMPRESA DE PEQUENO PORTE',
        5 => 'DEMAIS',
    ];

    // =========================================================
    // NORMALIZAR UF
    // =========================================================
    private function normalizarUf(?string $uf): ?string
    {
        if ($uf === null) return null;
        $uf = strtoupper(trim($uf));
        if (strlen($uf) !== 2) return null;
        return $uf;
    }

    // =========================================================
    // NORMALIZAR CEP (8 DÍGITOS)
    // =========================================================
    private function normalizarCep(?string $cep): ?string
    {
        if ($cep === null) return null;
        $cep = preg_replace('/\D/', '', $cep);
        if (strlen($cep) !== 8) return null;
        return $cep;
    }

    // =========================================================
    // NORMALIZAR CNAE (7 DÍGITOS ASCII)
    // =========================================================
    private function normalizarCnae(?string $cnae): ?string
    {
        if ($cnae === null) return null;
        $cnae = preg_replace('/\D/', '', $cnae);
        if (strlen($cnae) !== 7) return null;
        return $cnae;
    }

    // =========================================================
    // RESOLVER MUNICIPIO (ACEITA CÓDIGO OU SLUG) QUANDO UF EXISTE
    // =========================================================
    private function resolverMunicipioCodigo(?string $uf, ?string $municipio): ?int
    {
        if (!$municipio) return null;

        // SE VEIO NUMÉRICO, CONSIDERA COMO CÓDIGO
        $onlyDigits = preg_replace('/\D/', '', $municipio);
        if ($onlyDigits !== '' && strlen($onlyDigits) >= 4) {
            return (int)$onlyDigits;
        }

        // SE NÃO TEM UF, NÃO DÁ PRA RESOLVER SLUG COM SEGURANÇA
        if (!$uf) return null;

        // RESOLVER SLUG -> CODIGO (CACHE 3 MESES)
        $slug = Str::slug($municipio);
        $cacheKey = "dir:mapa_municipios_slug:{$uf}:v1";

        $map = Cache::remember($cacheKey, now()->addMonths(3), function () use ($uf) {

            $rows = DB::connection('cnpj')
                ->table('estabelecimentos_geral as e')
                ->join('municipios as m', 'm.codigo', '=', 'e.municipio')
                ->where('e.uf', $uf)
                ->groupBy('e.municipio', 'm.descricao')
                ->selectRaw('e.municipio as codigo')
                ->selectRaw('m.descricao as nome')
                ->get();

            $out = [];
            foreach ($rows as $r) {
                $s = Str::slug($r->nome);
                $out[$s] = (int)$r->codigo;
            }
            return $out;
        });

        return $map[$slug] ?? null;
    }

    // =========================================================
    // ENDPOINT: GET /api/v1/search
    // FILTROS (OPCIONAIS):
    // - uf=SP
    // - municipio=juiz-de-fora OU municipio=3136702 (CÓDIGO)
    // - cep=01311000
    // - situacao=2 (DEFAULT 2)
    // - matriz_filial=1 OU 2
    // - porte=1|3|5|0
    // - natureza=2135 (NATUREZA_JURIDICA)
    // - cnae_principal=0000000
    // - cnae_secundario=0000000 (PODE REPETIR: ?cnae_secundario=...&cnae_secundario=...)
    // - abertura_de=YYYY-MM-DD
    // - abertura_ate=YYYY-MM-DD
    // - q=texto (BUSCA SIMPLES EM RAZAO/NOME_FANTASIA)
    // - per_page=500 (MAX 5000)
    // - page=1
    // =========================================================
    public function index()
{
    // =========================================================
    // LER PARAMS
    // =========================================================
    $uf = $this->normalizarUf(request()->query('uf'));
    $municipioParam = request()->query('municipio');
    $municipioCodigo = $this->resolverMunicipioCodigo($uf, $municipioParam);

    $cep = $this->normalizarCep(request()->query('cep'));

    // =========================================================
    // SITUAÇÃO (DEFAULT 2, OU ALL/* PARA NÃO FILTRAR)
    // =========================================================
    $situacaoRaw = request()->query('situacao', '2');
    $situacaoRaw = is_string($situacaoRaw) ? strtolower(trim($situacaoRaw)) : (string)$situacaoRaw;

    $situacaoCodigo = null;      // =========================================================
                                // NULL = SEM FILTRO
                                // =========================================================
    $situacaoSemFiltro = false;

    if ($situacaoRaw === 'all' || $situacaoRaw === '*') {
        $situacaoSemFiltro = true;
    } elseif (is_numeric($situacaoRaw)) {
        $tmp = (int)$situacaoRaw;
        if (isset($this->situacaoMap[$tmp])) {
            $situacaoCodigo = $tmp;
        } else {
            // =========================================================
            // INVÁLIDO -> VOLTA PRO PADRÃO (ATIVA)
            // =========================================================
            $situacaoCodigo = 2;
        }
    } else {
        // =========================================================
        // NÃO VEIO NADA DECENTE -> PADRÃO (ATIVA)
        // =========================================================
        $situacaoCodigo = 2;
    }

    $matrizFilial = request()->query('matriz_filial');
    $matrizFilial = ($matrizFilial !== null && is_numeric($matrizFilial)) ? (int)$matrizFilial : null;
    if ($matrizFilial !== null && !in_array($matrizFilial, [1, 2], true)) {
        $matrizFilial = null;
    }

    $porte = request()->query('porte');
    $porte = ($porte !== null && is_numeric($porte)) ? (int)$porte : null;
    if ($porte !== null && !in_array($porte, [0, 1, 3, 5], true)) {
        $porte = null;
    }

    $natureza = request()->query('natureza');
    $natureza = ($natureza !== null && is_numeric($natureza)) ? (int)$natureza : null;

    $cnaePrincipal = $this->normalizarCnae(request()->query('cnae_principal'));

    $cnaesSec = request()->query('cnae_secundario', []);
    if (!is_array($cnaesSec)) $cnaesSec = [$cnaesSec];
    $cnaesSec = array_values(array_filter(array_map(fn($x) => $this->normalizarCnae((string)$x), $cnaesSec)));

    $aberturaDe = request()->query('abertura_de');
    $aberturaAte = request()->query('abertura_ate');

    $q = trim((string)request()->query('q', ''));

    $perPage = (int)request()->query('per_page', 500);
    if ($perPage < 1) $perPage = 500;
    if ($perPage > 5000) $perPage = 5000;

    // =========================================================
    // QUERY BASE
    // =========================================================
    $query = DB::connection('cnpj')
        ->table('estabelecimentos_geral as e')
        ->join('empresas as emp', 'emp.cnpj_basico', '=', 'e.cnpj_basico');

    // =========================================================
    // FILTROS
    // =========================================================
    if ($uf) $query->where('e.uf', $uf);

    if ($municipioCodigo !== null) $query->where('e.municipio', $municipioCodigo);

    if ($cep) $query->where('e.cep', $cep);

    // =========================================================
    // AQUI ESTÁ A MUDANÇA: SITUAÇÃO É OPCIONAL
    // =========================================================
    if (!$situacaoSemFiltro) {
        // SE NÃO FOR ALL/*, FILTRA PELA SITUAÇÃO ESCOLHIDA (DEFAULT ATIVA)
        $query->where('e.situacao_cadastral', $situacaoCodigo ?? 2);
    }

    if ($matrizFilial !== null) $query->where('e.identificador_matriz_filial', $matrizFilial);

    if ($porte !== null) $query->where('emp.porte_empresa', $porte);

    if ($natureza !== null) $query->where('emp.natureza_juridica', $natureza);

    if ($cnaePrincipal) $query->where('e.cnae_fiscal_principal', $cnaePrincipal);

    if (!empty($cnaesSec)) {
        $query->where(function ($qq) use ($cnaesSec) {
            foreach ($cnaesSec as $c) {
                $qq->orWhereRaw('FIND_IN_SET(?, e.cnae_fiscal_secundaria)', [$c]);
            }
        });
    }

    if ($aberturaDe) $query->whereDate('e.data_inicio_atividade', '>=', $aberturaDe);

    if ($aberturaAte) $query->whereDate('e.data_inicio_atividade', '<=', $aberturaAte);

    if ($q !== '') {
        $q2 = mb_substr($q, 0, 60);
        $query->where(function ($qq) use ($q2) {
            $qq->where('emp.razao_social', 'like', '%' . $q2 . '%')
               ->orWhere('e.nome_fantasia', 'like', '%' . $q2 . '%');
        });
    }

    // =========================================================
    // SELECT + ORDENAR POR CNPJ
    // =========================================================
    $query->selectRaw("CONCAT(e.cnpj_basico, e.cnpj_ordem, e.cnpj_dv) as cnpj")
        ->addSelect([
            'emp.razao_social',
            'e.nome_fantasia',
            'e.data_inicio_atividade',
            'emp.capital_social',
            'e.uf',
            'e.municipio',
            'e.situacao_cadastral',
            'emp.porte_empresa',
            'emp.natureza_juridica',
            'e.cnae_fiscal_principal',
        ])
        ->orderBy('e.cnpj_basico')
        ->orderBy('e.cnpj_ordem')
        ->orderBy('e.cnpj_dv');

    $paginator = $query->simplePaginate($perPage);

    // =========================================================
    // MUNICÍPIOS (MAP)
    // =========================================================
    $items = collect($paginator->items());
    $municipiosCodigos = $items->pluck('municipio')->filter()->unique()->values()->all();

    $municipiosMap = [];
    if (!empty($municipiosCodigos)) {
        $mRows = DB::connection('cnpj')
            ->table('municipios')
            ->whereIn('codigo', $municipiosCodigos)
            ->select('codigo', 'descricao')
            ->get();

        foreach ($mRows as $m) {
            $municipiosMap[(int)$m->codigo] = $m->descricao;
        }
    }

    // =========================================================
    // FORMATAR SAÍDA
    // =========================================================
    $data = $items->map(function ($r) use ($municipiosMap) {

        $situacaoCodigoItem = (int)$r->situacao_cadastral;
        $porteCodigo = $r->porte_empresa !== null ? (int)$r->porte_empresa : null;

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
                    'nome' => $municipiosMap[(int)$r->municipio] ?? null,
                ],
            ],
            'situacao_cadastral' => [
                'codigo' => $situacaoCodigoItem,
                'descricao' => $this->situacaoMap[$situacaoCodigoItem] ?? 'DESCONHECIDA',
            ],
            'porte' => [
                'codigo' => $porteCodigo,
                'descricao' => $porteCodigo !== null ? ($this->porteMap[$porteCodigo] ?? null) : null,
            ],
            'natureza_juridica_codigo' => $r->natureza_juridica !== null ? (int)$r->natureza_juridica : null,
            'cnae_principal' => $r->cnae_fiscal_principal,
        ];
    })->values()->all();

    // =========================================================
    // RETORNO
    // =========================================================
    return response()->json([
        'consulta' => [
            'endpoint' => 'search',
            'ok' => true,
            'filtros' => [
                'uf' => $uf,
                'municipio' => $municipioParam,
                'municipio_codigo_resolvido' => $municipioCodigo,
                'cep' => $cep,
                'situacao' => $situacaoSemFiltro ? 'all' : ($situacaoCodigo ?? 2),
                'matriz_filial' => $matrizFilial,
                'porte' => $porte,
                'natureza' => $natureza,
                'cnae_principal' => $cnaePrincipal,
                'cnae_secundario' => $cnaesSec,
                'abertura_de' => $aberturaDe,
                'abertura_ate' => $aberturaAte,
                'q' => $q !== '' ? $q : null,
            ],
            'paginacao' => [
                'per_page' => $perPage,
                'current_page' => $paginator->currentPage(),
                'next_page_url' => $paginator->nextPageUrl(),
                'prev_page_url' => $paginator->previousPageUrl(),
            ],
            'ordenacao' => 'cnpj_asc',
        ],
        'data' => $data,
    ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}
}