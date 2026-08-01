<?php

namespace App\Services\RH;

use Illuminate\Support\{Carbon, Collection};
use Illuminate\Support\Facades\{Auth, Log};

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
        $folha->lancamentos()->delete();

        $funcionario = $folha->funcionario;
        $competencia = Carbon::parse($folha->competencia);
        $valorHora = $this->calculadora->calcularValorHora($funcionario);

         // 🆕 Verifica se deve aplicar INSS         
        $aplicaINSS = $this->calculadora->deveAplicarINSS($funcionario);
        $aplicaFGTS = $this->calculadora->deveAplicarFGTS($funcionario);


        // 🆕 AQUI - normalize $diasTrabalho antes de usar
        $diasTrabalhoRaw = $funcionario->contrato?->dias_trabalho;

        // Debug
        Log::info('Dias trabalho raw:', ['raw' => $diasTrabalhoRaw, 'type' => gettype($diasTrabalhoRaw)]);

        if (is_string($diasTrabalhoRaw)) {
            $diasTrabalho = json_decode($diasTrabalhoRaw, true);
        } elseif (is_array($diasTrabalhoRaw)) {
            $diasTrabalho = $diasTrabalhoRaw;
        } else {
            $diasTrabalho = [1, 2, 3, 4, 5];
        }
        $diasTrabalho = array_map('intval', $diasTrabalho);

        $lancamentos = collect();

        // 1. Salário Base (PROVENTO)
        $lancamentos->push($this->criarLancamento($folha, [
            'categoria' => FolhaLancamento::CATEGORIA_PROVENTO,
            'tipo' => FolhaLancamento::TIPO_SALARIO_BASE,
            'descricao' => 'Salário Base',
            'quantidade' => 1,
            'valor_unitario' => $funcionario->contrato->salario_base,
            'percentual_acrescimo' => 0,
            'valor_total' => $funcionario->contrato->salario_base,
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
                // 'valor_total' =>  floor($dados['horas_extras_totais'] * $valorHora['valor_hora_extra'] * 100) / 100, // 🆕 truncado para 2 casas decimais
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
                // 'valor_total' =>  floor($dados['horas_sabado'] * $valorHora['valor_hora_extra'] * 100) / 100, // 🆕 truncado para 2 casas decimais
            ]));
        }
        $valorHEFeriado = 0; // Inicializa a variável para evitar erro de variável indefinida
        // 4. Horas Extras Feriado (PROVENTO)
        if (!empty($dados['horas_feriado']) && $dados['horas_feriado'] > 0) {
            $valorHEFeriado = round($dados['horas_feriado'] * $valorHora['valor_hora_feriado'], 2);
            $lancamentos->push($this->criarLancamento($folha, [
                'categoria' => FolhaLancamento::CATEGORIA_PROVENTO,
                'tipo' => FolhaLancamento::TIPO_HORA_EXTRA_FERIADO,
                'descricao' => 'Horas Extras Feriado (100%)',
                'quantidade' => $dados['horas_feriado'],
                'valor_unitario' => round($valorHora['valor_hora_normal'], 2),
                'percentual_acrescimo' => 100,
                'valor_total' => $valorHEFeriado,
            ]));
        }

        // 5. DSR Hora Extra (PROVENTO) - 🆕 CORRIGIDO
        // 🆕 Calcula o valor total das horas extras em R$
        // 5. DSR Hora Extra - totalHEValor com floor
            $totalHEValor = 0;
            // if (!empty($dados['horas_extras_totais'])) $totalHEValor += floor($dados['horas_extras_totais'] * $valorHora['valor_hora_extra'] * 100) / 100;
            // if (!empty($dados['horas_sabado'])) $totalHEValor += floor($dados['horas_sabado'] * $valorHora['valor_hora_extra'] * 100) / 100;
            // if (!empty($dados['horas_feriado'])) $totalHEValor += $valorHEFeriado;  // 🆕 usa o valor já calculado





        if (!empty($dados['horas_extras_totais'])) $totalHEValor += round($dados['horas_extras_totais'] * $valorHora['valor_hora_extra'], 2);
        if (!empty($dados['horas_sabado'])) $totalHEValor += round($dados['horas_sabado'] * $valorHora['valor_hora_extra'], 2);
        if (!empty($dados['horas_feriado'])) $totalHEValor += round($dados['horas_feriado'] * $valorHora['valor_hora_feriado'], 2);
        // ✅ CORRIGIDO
        // if (!empty($dados['horas_feriado'])) $totalHEValor += floor($dados['horas_feriado'] * $valorHora['valor_hora_feriado'] * 100) / 100;

        // 🆕 DSR: (Valor total HE ÷ dias úteis) × domingos/feriados
        $diasUteis = $this->calculadora->calcularDiasUteis($competencia, $diasTrabalho);
        $domingosFeriados = $this->calculadora->calcularDomingosEFeriados($competencia);
        $dsr = 0;
        if ($totalHEValor > 0 && $diasUteis > 0) {
            $dsr = floor(($totalHEValor / $diasUteis) * $domingosFeriados * 100) / 100;
        }

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
                'valor_unitario' => $this->calculadora->getValorSalarioFamilia(),
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

        // 8. Faltas (DESCONTO) - 🆕 CORRIGIDO (sem DSR)
        $faltasValor = 0;
        if (!empty($dados['faltas_dias']) && $dados['faltas_dias'] > 0) {
            $salarioBase = (float) $funcionario->contrato->salario_base;
            $faltasDias = (float) $dados['faltas_dias'];
            $faltasValor = round(($salarioBase / 30) * $faltasDias, 2);
            
            $lancamentos->push($this->criarLancamento($folha, [
                'categoria' => FolhaLancamento::CATEGORIA_DESCONTO,
                'tipo' => FolhaLancamento::TIPO_FALTA,
                'descricao' => 'Faltas',
                'quantidade' => $dados['faltas_dias'],
                'valor_unitario' => round($salarioBase / 30, 2),
                'percentual_acrescimo' => 0,
                'valor_total' => $faltasValor,
            ]));
        }

        // 🆕 Calcula total de proventos e descontos parciais para base INSS
        $totalProventosParcial = $lancamentos->where('categoria', FolhaLancamento::CATEGORIA_PROVENTO)->sum('valor_total');
        $totalDescontosParcial = $lancamentos->where('categoria', FolhaLancamento::CATEGORIA_DESCONTO)->sum('valor_total');

        // 🆕 Base INSS = Salário + HE + DSR - Faltas
        // $baseInss = $funcionario->salario_base + $totalHEValor + $dsr - $faltasValor;
        Log::info('Componentes Base INSS:', [
            'salario_base' => $funcionario->contrato->salario_base,
            'totalHEValor' => $totalHEValor,
            'dsr' => $dsr,
            'faltasValor' => $faltasValor,
            'soma' => $funcionario->contrato->salario_base + $totalHEValor + $dsr - $faltasValor,
        ]);
        // 🆕 Base INSS truncada
        // $baseInss = floor(($funcionario->salario_base + $totalHEValor + $dsr - $faltasValor) * 100) / 100;
        // 🆕 Base INSS - garantir float
        $salarioBase = (float) $funcionario->contrato->salario_base;
        $baseInss = round($salarioBase + $totalHEValor + $dsr - $faltasValor, 2);
        
        
        Log::info('Base INSS:', [
            'salario' => $salarioBase,
            'HE' => $totalHEValor,
            'DSR' => $dsr,
            'faltas' => $faltasValor,
            'base' => $baseInss
        ]);
        

        // 9. INSS (DESCONTO) - 🆕 CORRIGIDO (base correta)
        // $inss = $this->calculadora->calcularINSS($baseInss);
        // if ($inss > 0) {
        //     $aliquota = $baseInss > 0 ? round(($inss / $baseInss) * 100, 4) : 0;
        //     $lancamentos->push($this->criarLancamento($folha, [
        //         'categoria' => FolhaLancamento::CATEGORIA_DESCONTO,
        //         'tipo' => FolhaLancamento::TIPO_INSS,
        //         'descricao' => "INSS ({$aliquota}%)",
        //         'quantidade' => 1,
        //         'valor_unitario' => $inss,
        //         'percentual_acrescimo' => 0,
        //         'valor_total' => $inss,
        //     ]));
        // }
         // 9. INSS (DESCONTO)
        if ($aplicaINSS) {
            $baseInss = round($funcionario->salario_base + $totalHEValor + $dsr - $faltasValor, 2);
            $inss = $this->calculadora->calcularINSS($baseInss);
            
            if ($inss > 0) {
                $aliquota = $baseInss > 0 ? round(($inss / $baseInss) * 100, 4) : 0;
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

        // 12. Arredondamentos - 🆕 CORRIGIDO
        $totalProventos = $lancamentos->where('categoria', FolhaLancamento::CATEGORIA_PROVENTO)->sum('valor_total');
        $totalDescontos = $lancamentos->where('categoria', FolhaLancamento::CATEGORIA_DESCONTO)->sum('valor_total');

        $liquidoBruto = $totalProventos - $totalDescontos;

        // Se o líquido não é inteiro, arredonda para cima
        if ($liquidoBruto != floor($liquidoBruto)) {
            $liquidoArredondado = ceil($liquidoBruto);
            $arredondamentoProvento = round($liquidoArredondado - $liquidoBruto, 2);

            if($arredondamentoProvento > 0) {

                $lancamentos->push($this->criarLancamento($folha, [
                    'categoria' => FolhaLancamento::CATEGORIA_PROVENTO,
                    'tipo' => FolhaLancamento::TIPO_ARREDONDAMENTO,
                    'descricao' => 'Arredondamento Provento',
                    'quantidade' => 1,
                    'valor_unitario' => $arredondamentoProvento,
                    'percentual_acrescimo' => 0,
                    'valor_total' => $arredondamentoProvento,
                    'tota_proventos_parcial' => $totalProventosParcial,
                    'total_descontos_parcial' => $totalDescontosParcial,
                    ]));
            }
        }

        Log::info('DSR Cálculo:', [
            'totalHEValor' => $totalHEValor,
            'diasUteis' => $diasUteis,
            'domingosFeriados' => $domingosFeriados,
            'formula' => "($totalHEValor / $diasUteis) * $domingosFeriados",
            'resultado' => round(($totalHEValor / $diasUteis) * $domingosFeriados, 2),
        ]);

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