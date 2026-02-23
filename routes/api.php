<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CnpjController;
use App\Http\Controllers\CepController;
use App\Http\Controllers\UfsGeralController;
use App\Http\Controllers\DirectoryController;
use App\Http\Controllers\SearchController;

// =========================================================
// ROTAS DA API (VERSÃO 1)
// BASE: /api/v1
// =========================================================
Route::prefix('v1')->group(function () {

    // =========================================================
    // HEALTHCHECK / TESTE RÁPIDO DA API
    // RETORNA UM JSON SIMPLES PARA VALIDAR QUE A API ESTÁ ONLINE
    // GET: /api/v1/teste
    // =========================================================
    Route::get('/teste', function () {
        return response()->json(['ok' => true]);
    });

    // =========================================================
    // ENDPOINT SEARCH (BUSCA AVANÇADA COM FILTROS OPCIONAIS)
    // FILTROS POSSÍVEIS (EXEMPLOS):
    // - uf=SP
    // - municipio=juiz-de-fora (SLUG) OU municipio=3136702 (CÓDIGO)
    // - cep=01311000
    // - situacao=2 OU situacao=8 OU situacao=all
    // - porte=1|3|5|0
    // - natureza=2135
    // - cnae_principal=0000000
    // - cnae_secundario=0000000 (PODE REPETIR)
    // - abertura_de=YYYY-MM-DD
    // - abertura_ate=YYYY-MM-DD
    // - q=texto (RAZÃO SOCIAL / NOME FANTASIA)
    // - per_page=500
    // - page=1
    // GET: /api/v1/search
    // =========================================================
    Route::get('/search', [SearchController::class, 'index']);

    // =========================================================
    // ENDPOINT 1: CONSULTA COMPLETA DE CNPJ (UM ÚNICO CNPJ)
    // RETORNA DADOS DE EMPRESA + ESTABELECIMENTO + CNAES + QSA
    // GET: /api/v1/cnpj/{cnpj}
    // EX: /api/v1/cnpj/00000000000191
    // =========================================================
    Route::get('/cnpj/{cnpj}', [CnpjController::class, 'show']);

    // =========================================================
    // ENDPOINT 2: CONSULTA DE CEP (STATS + UF/MUNICÍPIO DO CEP)
    // RETORNA:
    // - UF E MUNICÍPIO DO CEP
    // - CONTAGENS/STATS (ATIVAS, BAIXADAS, ETC.)
    // GET: /api/v1/cep/{cep}
    // EX: /api/v1/cep/01311000
    // =========================================================
    Route::get('/cep/{cep}', [CepController::class, 'show']);

    // =========================================================
    // ENDPOINT 2.1: LISTAGEM DE ESTABELECIMENTOS POR CEP (PAGINADO)
    // RETORNA LISTA DE EMPRESAS/ESTABELECIMENTOS DO CEP, PAGINADO
    // QUERYSTRING:
    // - per_page=500
    // - page=1
    // GET: /api/v1/cep/{cep}/estabelecimentos
    // =========================================================
    Route::get('/cep/{cep}/estabelecimentos', [CepController::class, 'estabelecimentos']);

    // =========================================================
    // DIRECTORY: LISTAGEM DE UFS (COM STATS E INOVAÇÕES 2023/2024/2025)
    // EXEMPLO DE CONTEÚDO:
    // - TOTAL DE ESTABELECIMENTOS
    // - TOTAL DE ATIVAS/BAIXADAS POR UF
    // - TOTAL DE MUNICÍPIOS
    // - ABERTAS/FECHADAS NOS ÚLTIMOS 3 ANOS (2023–2025)
    // GET: /api/v1/ufs
    // =========================================================
    Route::get('/ufs', [UfsGeralController::class, 'index']);

    // =========================================================
    // DIRECTORY: LISTAGEM DE MUNICÍPIOS DE UMA UF (COM STATS)
    // GET: /api/v1/ufs/{uf}/municipios
    // EX: /api/v1/ufs/MG/municipios
    // =========================================================
    Route::get('/ufs/{uf}/municipios', [DirectoryController::class, 'municipiosPorUf']);

    // =========================================================
    // DIRECTORY: LISTAGEM DE EMPRESAS POR UF + MUNICÍPIO (SLUG)
    // OBS:
    // - NORMALMENTE LISTA SOMENTE ATIVAS (CONFORME IMPLEMENTAÇÃO)
    // - ORDENADO POR CNPJ (INDEX-FRIENDLY)
    // - PAGINADO
    // GET: /api/v1/{uf}/{municipio_slug}/empresas
    // EX: /api/v1/mg/juiz-de-fora/empresas?per_page=500&page=1
    // RESTRIÇÕES (REGEX):
    // - uf: 2 letras
    // - municipio_slug: letras minúsculas, números e hífen
    // =========================================================
    Route::get('/{uf}/{municipio_slug}/empresas', [DirectoryController::class, 'empresasPorMunicipioSlug'])
        ->where([
            'uf' => '[A-Za-z]{2}',
            'municipio_slug' => '[a-z0-9\-]+',
        ]);

});