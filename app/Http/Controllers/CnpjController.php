<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class CnpjController extends Controller
{
    // =========================================================
    // FUNÇÃO PARA LIMPAR O CNPJ (REMOVER MÁSCARA)
    // =========================================================
    private function limparCnpj($cnpj)
    {
        return preg_replace('/\D/', '', (string)$cnpj);
    }

    // =========================================================
    // FUNÇÃO SEPARADA PARA VALIDAR FORMATO DO CNPJ
    // =========================================================
    private function validarCnpjFormato($cnpj)
    {
        // VERIFICAR SE POSSUI 14 DÍGITOS
        if (strlen($cnpj) !== 14) { return false; }
        // VERIFICAR SE CONTÉM APENAS NÚMEROS
        if (!ctype_digit($cnpj)) { return false; }
        return true;
    }

    // =========================================================
    // FUNÇÃO PARA SEPARAR AS PARTES DO CNPJ
    // =========================================================
    private function separarCnpj($cnpj)
    {
        return [
            'cnpj_basico' => substr($cnpj, 0, 8),
            'cnpj_ordem'  => substr($cnpj, 8, 4),
            'cnpj_dv'     => substr($cnpj, 12, 2),
        ];
    }

    // =========================================================
    // VALIDAR EXISTÊNCIA REAL (ESTABELECIMENTO + EMPRESA) EM 1 QUERY
    // E JÁ TRAZER DADOS BASE COM JOINS (NATUREZA, CNAE PRINCIPAL, MUNICÍPIO)
    // =========================================================
    private function buscarDadosBase($cnpj)
    {
        $partes = $this->separarCnpj($cnpj);

        // INNER JOIN EM EMPRESAS GARANTE QUE: SE RETORNOU, EXISTE ESTABELECIMENTO E EMPRESA
        // LEFT JOIN NAS TABELAS AUXILIARES EVITA "QUEBRAR" SE ALGUM CADASTRO ESTIVER INCOMPLETO
        return DB::connection('cnpj')
            ->table('estabelecimentos_geral as e')
            ->join('empresas as emp', 'emp.cnpj_basico', '=', 'e.cnpj_basico')
            ->leftJoin('naturezas_juridicas as nj', 'nj.codigo', '=', 'emp.natureza_juridica')
            ->leftJoin('cnaes as cnae_p', 'cnae_p.codigo', '=', 'e.cnae_fiscal_principal')
            ->leftJoin('municipios as m', 'm.codigo', '=', 'e.municipio')
            ->where('e.cnpj_basico', $partes['cnpj_basico'])
            ->where('e.cnpj_ordem', $partes['cnpj_ordem'])
            ->where('e.cnpj_dv', $partes['cnpj_dv'])
            ->select([
                // DADOS DO ESTABELECIMENTO
                'e.*',
                // DADOS DA EMPRESA (PREFIXO EMP_)
                'emp.razao_social as emp_razao_social',
                'emp.natureza_juridica as emp_natureza_juridica',
                'emp.qualificacao_responsavel as emp_qualificacao_responsavel',
                'emp.capital_social as emp_capital_social',
                'emp.porte_empresa as emp_porte_empresa',
                'emp.ente_federativo_responsavel as emp_ente_federativo_responsavel',
                // NATUREZA JURÍDICA (JÁ TRADUZIDA)
                'nj.codigo as nj_codigo',
                'nj.descricao as nj_descricao',
                // CNAE PRINCIPAL (JÁ TRADUZIDO)
                'cnae_p.codigo as cnae_p_codigo',
                'cnae_p.descricao as cnae_p_descricao',
                // MUNICÍPIO (JÁ TRADUZIDO)
                'm.descricao as municipio_descricao',
            ])
            ->first();
    }

    // =========================================================
    // BUSCAR CNAES SECUNDÁRIOS (1 QUERY)
    // =========================================================
    private function buscarCnaesSecundarios($campoCnaesSecundarios)
    {
        if (!$campoCnaesSecundarios) { return []; }
        // SEPARAR POR VÍRGULA, REMOVER ESPAÇOS E ITENS VAZIOS
        $codigos = array_values(array_filter(array_map('trim', explode(',', $campoCnaesSecundarios))));
        if (!$codigos) { return []; }
        return DB::connection('cnpj')
            ->table('cnaes')
            ->whereIn('codigo', $codigos)
            ->get()
            ->map(function ($cnae) {
                return [
                    'codigo' => $cnae->codigo,
                    'descricao' => $cnae->descricao,
                ];
            })
            ->values()
            ->all();
    }

    // =========================================================
    // BUSCAR SÓCIOS COM QUALIFICAÇÃO (1 QUERY)
    // =========================================================
    private function buscarSocios($cnpjBasico)
    {
        return DB::connection('cnpj')
            ->table('socios as s')
            ->leftJoin('qualificacoes as q', 'q.codigo', '=', 's.qualificacao_socio')
            ->where('s.cnpj_basico', $cnpjBasico)
            ->select(
                's.nome_socio',
                's.data_entrada_sociedade',
                'q.codigo as qual_codigo',
                'q.descricao as qual_descricao'
            )
            ->get()
            ->map(function ($socio) {
                return [
                    'nome' => $socio->nome_socio,
                    'qualificacao' => $socio->qual_codigo
                        ? $socio->qual_codigo . ' - ' . $socio->qual_descricao
                        : null,
                    'data_entrada' => $socio->data_entrada_sociedade
                        ? date('d-m-Y', strtotime($socio->data_entrada_sociedade))
                        : null,
                ];
            })
            ->values()
            ->all();
    }

    // =========================================================
    // FUNÇÃO PARA FORMATAR VALOR MONETÁRIO EM PADRÃO BRASILEIRO
    // =========================================================
    private function formatarMoeda($valor)
    {
        if ($valor === null) return null;
        return 'R$ ' . number_format((float)$valor, 2, ',', '.');
    }

    // =========================================================
    // FUNÇÃO RESPONSÁVEL POR MONTAR O PAYLOAD COMPLETO
    // (COM BASE NO OBJETO BASE JÁ VINDO DE JOINs)
    // =========================================================
    private function montarPayloadCompleto($base)
    {
        // ==============================
        // TRADUZIR PORTE
        // ==============================
        $porteMap = [
            0 => 'NÃO INFORMADO',
            1 => 'MICRO EMPRESA',
            3 => 'EMPRESA DE PEQUENO PORTE',
            5 => 'DEMAIS'
        ];
        $porte = $porteMap[$base->emp_porte_empresa] ?? 'NÃO INFORMADO';

        // ==============================
        // TRADUZIR MATRIZ / FILIAL
        // ==============================
        $tipoMap = [
            1 => 'MATRIZ',
            2 => 'FILIAL'
        ];
        $tipoEstabelecimento = $tipoMap[$base->identificador_matriz_filial] ?? null;

        // ==============================
        // TRADUZIR SITUAÇÃO CADASTRAL
        // ==============================
        $situacaoMap = [
            1 => 'NULA',
            2 => 'ATIVA',
            3 => 'SUSPENSA',
            4 => 'INAPTA',
            8 => 'BAIXADA'
        ];
        $situacao = $situacaoMap[$base->situacao_cadastral] ?? null;

        // ==============================
        // FORMATAR DATA DE ABERTURA
        // ==============================
        $dataAbertura = $base->data_inicio_atividade
            ? date('d-m-Y', strtotime($base->data_inicio_atividade))
            : null;

        // ==============================
        // CONCATENAR TELEFONES
        // ==============================
        $telefone1 = $base->telefone1
            ? '(' . $base->ddd1 . ') ' . $base->telefone1
            : null;

        $telefone2 = $base->telefone2
            ? '(' . $base->ddd2 . ') ' . $base->telefone2
            : null;

        // ==============================
        // CNAES SECUNDÁRIOS (1 QUERY)
        // ==============================
        $cnaesSecundarios = $this->buscarCnaesSecundarios($base->cnae_fiscal_secundaria);

        // ==============================
        // SÓCIOS (1 QUERY)
        // ==============================
        $socios = $this->buscarSocios($base->cnpj_basico);

        // ==============================
        // MONTAR NATUREZA JURÍDICA (JÁ VEIO DO JOIN)
        // ==============================
        $naturezaJuridica = $base->nj_codigo
            ? $base->nj_codigo . ' - ' . $base->nj_descricao
            : null;

        // ==============================
        // MONTAR CNAE PRINCIPAL (JÁ VEIO DO JOIN)
        // ==============================
        $cnaePrincipal = $base->cnae_p_codigo
            ? $base->cnae_p_codigo . ' - ' . $base->cnae_p_descricao
            : null;

        // ==============================
        // MONTAR MUNICÍPIO (JÁ VEIO DO JOIN)
        // ==============================
        $municipio = $base->municipio_descricao ?? null;

        return [
            'informacoes' => [
                'cnpj' => $base->cnpj_basico . $base->cnpj_ordem . $base->cnpj_dv,
                'razao_social' => $base->emp_razao_social,
                'nome_fantasia' => $base->nome_fantasia,
                'natureza_juridica' => $naturezaJuridica,
                'capital_social' => $this->formatarMoeda($base->emp_capital_social),
                'porte' => $porte,
                'tipo_estabelecimento' => $tipoEstabelecimento,
                'data_abertura' => $dataAbertura,
                'situacao_cadastral' => $situacao,
            ],

            'atividades' => [
                'cnae_principal' => $cnaePrincipal,
                'cnaes_secundarios' => $cnaesSecundarios,
            ],

            'endereco' => [
                'tipo_logradouro' => $base->tipo_logradouro,
                'logradouro' => $base->logradouro,
                'numero' => $base->numero,
                'complemento' => $base->complemento,
                'bairro' => $base->bairro,
                'cep' => $base->cep,
                'uf' => $base->uf,
                'municipio' => $municipio,
            ],

            'telefones' => [
                'telefone1' => $telefone1,
                'telefone2' => $telefone2,
            ],

            'qsa' => $socios,
        ];
    }

    // =========================================================
    // ENDPOINT PRINCIPAL: GET /api/v1/cnpj/{cnpj}
    // =========================================================
    public function show($cnpj)
    {
        // LIMPAR CNPJ (REMOVER MÁSCARA)
        $cnpj = $this->limparCnpj($cnpj);
        // VALIDAR FORMATO
        if (!$this->validarCnpjFormato($cnpj)) {
            return response()->json(['error' => 'CNPJ inválido'], 400);
        }
        // BUSCAR DADOS BASE (VALIDA EXISTÊNCIA E JÁ TRAZ PARTE DOS DADOS)
        $base = $this->buscarDadosBase($cnpj);
        // SE NÃO EXISTE (OU NÃO TEM EMPRESA VINCULADA), 404
        if (!$base) {
            return response()->json(['error' => 'CNPJ não encontrado'], 404);
        }
        // MONTAR PAYLOAD COMPLETO
        $payload = $this->montarPayloadCompleto($base);
        // RETORNAR JSON
        // OBS: JSON_PRETTY_PRINT É BONITO PARA DEBUG, MAS AUMENTA O TAMANHO DA RESPOSTA
        return response()->json($payload, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}