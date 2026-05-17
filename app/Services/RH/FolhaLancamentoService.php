<?php

namespace App\Services\RH;

use Illuminate\Support\{Carbon, Collection};
use Illuminate\Support\Facades\Auth;

use App\Models\Domain\RH\{FolhaLancamento, FolhaPagamento};

class FolhaLancamentoService
{
    private CalculoTrabalhistaService $calculadora;

    public function __construct()
    {
        $this->calculadora = new CalculoTrabalhistaService();
    }

    /**
     * Gera todos os lançamentos para uma folha
     */
    public function gerarLancamentos(FolhaPagamento $folha, array $dados): Collection
    {
        // Remove lançamentos antigos
        $folha->lancamentos()->delete();

        $funcionario = $folha->funcionario;
        $competencia = Carbon::parse($folha->competencia);
        $valorHora = $this->calculadora->calcularValorHora($funcionario);

        $lancamentos = collect();

        // 1. Salário Base (PROVENTO)
        $lancamentos->push($this->criarLancamento($folha, [
            'categoria' => FolhaLancamento::CATEGORIA_PROVENTO,
            'tipo' => FolhaLancamento::TIPO_SALARIO_BASE,
            'descricao' => 'Salário Base',
            'quantidade' => 1,
            'valor_unitario' => $funcionario->salario_base,
            'percentual_acrescimo' => 0,
            'valor_total' => $funcionario->salario_base,
        ]));

        // 2. Horas Extras Normais (PROVENTO)
        if (!empty($dados['horas_extras_totais']) && $dados['horas_extras_totais'] > 0) {
            $lancamentos->push($this->criarLancamento($folha, [
                'categoria' => FolhaLancamento::CATEGORIA_PROVENTO,
                'tipo' => FolhaLancamento::TIPO_HORA_EXTRA_NORMAL,
                'descricao' => 'Horas Extras (50%)',
                'quantidade' => $dados['horas_extras_totais'],
                'valor_unitario' => $valorHora['valor_hora_normal'],
                'percentual_acrescimo' => 50,
                'valor_total' => round($dados['horas_extras_totais'] * $valorHora['valor_hora_extra'], 2),
            ]));
        }

        // 3. Horas Extras Sábado (PROVENTO)
        if (!empty($dados['horas_sabado']) && $dados['horas_sabado'] > 0) {
            $lancamentos->push($this->criarLancamento($folha, [
                'categoria' => FolhaLancamento::CATEGORIA_PROVENTO,
                'tipo' => FolhaLancamento::TIPO_HORA_EXTRA_SABADO,
                'descricao' => 'Horas Extras Sábado (50%)',
                'quantidade' => $dados['horas_sabado'],
                'valor_unitario' => $valorHora['valor_hora_normal'],
                'percentual_acrescimo' => 50,
                'valor_total' => round($dados['horas_sabado'] * $valorHora['valor_hora_extra'], 2),
            ]));
        }

        // 4. Horas Extras Feriado (PROVENTO)
        if (!empty($dados['horas_feriado']) && $dados['horas_feriado'] > 0) {
            $lancamentos->push($this->criarLancamento($folha, [
                'categoria' => FolhaLancamento::CATEGORIA_PROVENTO,
                'tipo' => FolhaLancamento::TIPO_HORA_EXTRA_FERIADO,
                'descricao' => 'Horas Extras Feriado (100%)',
                'quantidade' => $dados['horas_feriado'],
                'valor_unitario' => $valorHora['valor_hora_normal'],
                'percentual_acrescimo' => 100,
                'valor_total' => round($dados['horas_feriado'] * $valorHora['valor_hora_feriado'], 2),
            ]));
        }

        // 5. DSR Hora Extra (PROVENTO)
        $totalHoras = ($dados['horas_extras_totais'] ?? 0) + ($dados['horas_sabado'] ?? 0) + ($dados['horas_feriado'] ?? 0);
        $dsr = $this->calculadora->calcularDSR($totalHoras, $valorHora['valor_hora_extra'], $competencia);
        if ($dsr > 0) {
            $lancamentos->push($this->criarLancamento($folha, [
                'categoria' => FolhaLancamento::CATEGORIA_PROVENTO,
                'tipo' => FolhaLancamento::TIPO_DSR_HORA_EXTRA,
                'descricao' => 'DSR sobre Horas Extras',
                'quantidade' => 1,
                'valor_unitario' => $dsr,
                'percentual_acrescimo' => 0,
                'valor_total' => $dsr,
            ]));
        }

        // 6. Salário Família (PROVENTO)
        $salarioFamilia = $this->calculadora->calcularSalarioFamilia($funcionario);
        if ($salarioFamilia > 0) {
            $lancamentos->push($this->criarLancamento($folha, [
                'categoria' => FolhaLancamento::CATEGORIA_PROVENTO,
                'tipo' => FolhaLancamento::TIPO_SALARIO_FAMILIA,
                'descricao' => 'Salário Família',
                'quantidade' => $funcionario->qtd_dependentes_salario_familia ?? 0,
                'valor_unitario' => 62.04,
                'percentual_acrescimo' => 0,
                'valor_total' => $salarioFamilia,
            ]));
        }

        // 7. Gratificação (PROVENTO)
        if (!empty($dados['gratificacao_feriado']) && $dados['gratificacao_feriado'] > 0) {
            $lancamentos->push($this->criarLancamento($folha, [
                'categoria' => FolhaLancamento::CATEGORIA_PROVENTO,
                'tipo' => FolhaLancamento::TIPO_GRATIFICACAO,
                'descricao' => 'Gratificação',
                'quantidade' => 1,
                'valor_unitario' => $dados['gratificacao_feriado'],
                'percentual_acrescimo' => 0,
                'valor_total' => $dados['gratificacao_feriado'],
            ]));
        }

        // 8. INSS (DESCONTO)
        $inss = $this->calculadora->calcularINSS($funcionario->salario_base);
        if ($inss > 0) {
            $aliquota = $this->calculadora->getAliquotaEfetivaINSS($funcionario->salario_base);
            $lancamentos->push($this->criarLancamento($folha, [
                'categoria' => FolhaLancamento::CATEGORIA_DESCONTO,
                'tipo' => FolhaLancamento::TIPO_INSS,
                'descricao' => "INSS ({$aliquota}%)",
                'quantidade' => 1,
                'valor_unitario' => $inss,
                'percentual_acrescimo' => 0,
                'valor_total' => $inss,
            ]));
        }

        // 9. Faltas (DESCONTO)
        if (!empty($dados['faltas_dias']) && $dados['faltas_dias'] > 0) {
            $faltas = $this->calculadora->calcularFaltas(
                $dados['faltas_dias'],
                $funcionario->salario_base,
                $competencia
            );

            if ($faltas['valor'] > 0) {
                $lancamentos->push($this->criarLancamento($folha, [
                    'categoria' => FolhaLancamento::CATEGORIA_DESCONTO,
                    'tipo' => FolhaLancamento::TIPO_FALTA,
                    'descricao' => 'Faltas',
                    'quantidade' => $dados['faltas_dias'],
                    'valor_unitario' => $faltas['valor'] / $dados['faltas_dias'],
                    'percentual_acrescimo' => 0,
                    'valor_total' => $faltas['valor'],
                ]));
            }

            if ($faltas['dsr'] > 0) {
                $lancamentos->push($this->criarLancamento($folha, [
                    'categoria' => FolhaLancamento::CATEGORIA_DESCONTO,
                    'tipo' => FolhaLancamento::TIPO_DSR_FALTA,
                    'descricao' => 'DSR sobre Faltas',
                    'quantidade' => 1,
                    'valor_unitario' => $faltas['dsr'],
                    'percentual_acrescimo' => 0,
                    'valor_total' => $faltas['dsr'],
                ]));
            }
        }

        // 10. Vale Dia 20 (DESCONTO)
        if (!empty($dados['vale_dia_20']) && $dados['vale_dia_20'] > 0) {
            $lancamentos->push($this->criarLancamento($folha, [
                'categoria' => FolhaLancamento::CATEGORIA_DESCONTO,
                'tipo' => FolhaLancamento::TIPO_VALE_DIA_20,
                'descricao' => 'Vale Dia 20',
                'quantidade' => 1,
                'valor_unitario' => $dados['vale_dia_20'],
                'percentual_acrescimo' => 0,
                'valor_total' => $dados['vale_dia_20'],
            ]));
        }

        // 11. Vale Extra (DESCONTO)
        if (!empty($dados['vale_extra']) && $dados['vale_extra'] > 0) {
            $lancamentos->push($this->criarLancamento($folha, [
                'categoria' => FolhaLancamento::CATEGORIA_DESCONTO,
                'tipo' => FolhaLancamento::TIPO_VALE_EXTRA,
                'descricao' => 'Vale Extra',
                'quantidade' => 1,
                'valor_unitario' => $dados['vale_extra'],
                'percentual_acrescimo' => 0,
                'valor_total' => $dados['vale_extra'],
            ]));
        }

        // 12. Arredondamentos
        $totalProventos = $lancamentos->where('categoria', FolhaLancamento::CATEGORIA_PROVENTO)->sum('valor_total');
        $totalDescontos = $lancamentos->where('categoria', FolhaLancamento::CATEGORIA_DESCONTO)->sum('valor_total');
        $arredondamentos = $this->calculadora->calcularArredondamentos($totalProventos, $totalDescontos);

        if ($arredondamentos['provento'] > 0) {
            $lancamentos->push($this->criarLancamento($folha, [
                'categoria' => FolhaLancamento::CATEGORIA_PROVENTO,
                'tipo' => FolhaLancamento::TIPO_ARREDONDAMENTO,
                'descricao' => 'Arredondamento Provento',
                'quantidade' => 1,
                'valor_unitario' => $arredondamentos['provento'],
                'percentual_acrescimo' => 0,
                'valor_total' => $arredondamentos['provento'],
            ]));
        }

        if ($arredondamentos['desconto'] > 0) {
            $lancamentos->push($this->criarLancamento($folha, [
                'categoria' => FolhaLancamento::CATEGORIA_DESCONTO,
                'tipo' => FolhaLancamento::TIPO_ARREDONDAMENTO,
                'descricao' => 'Arredondamento Desconto',
                'quantidade' => 1,
                'valor_unitario' => $arredondamentos['desconto'],
                'percentual_acrescimo' => 0,
                'valor_total' => $arredondamentos['desconto'],
            ]));
        }

        return $lancamentos;
    }

    /**
     * Cria um lançamento individual
     */
    private function criarLancamento(FolhaPagamento $folha, array $dados): FolhaLancamento
    {
        return FolhaLancamento::create(array_merge($dados, [
            'folha_pagamento_id' => $folha->id,
            'automatico' => true,
            'criado_por' => Auth::id(),
        ]));
    }

    /**
     * Obtém resumo dos lançamentos para exibição
     */
    public function getResumo(FolhaPagamento $folha): array
    {
        $lancamentos = $folha->lancamentos;

        return [
            'proventos' => $lancamentos->where('categoria', FolhaLancamento::CATEGORIA_PROVENTO)->values(),
            'descontos' => $lancamentos->where('categoria', FolhaLancamento::CATEGORIA_DESCONTO)->values(),
            'total_proventos' => $lancamentos->where('categoria', FolhaLancamento::CATEGORIA_PROVENTO)->sum('valor_total'),
            'total_descontos' => $lancamentos->where('categoria', FolhaLancamento::CATEGORIA_DESCONTO)->sum('valor_total'),
            'total_liquido' => $lancamentos->sum(function($l) {
                return $l->categoria === FolhaLancamento::CATEGORIA_PROVENTO
                    ? $l->valor_total
                    : -$l->valor_total;
            }),
        ];
    }
}