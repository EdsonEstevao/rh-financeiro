<?php

namespace App\Http\Controllers\RH;

use Carbon\Carbon;

use App\Services\RH\{CalculoTrabalhistaService, FolhaLancamentoService, FolhaPagamentoService};
use App\Http\Controllers\Controller;
use App\Models\Domain\RH\{FolhaPagamento, Funcionario};
use Barryvdh\DomPDF\Facade\Pdf;

class ContraChequeController extends Controller
{
    //
    public function __construct(
        private CalculoTrabalhistaService $calculadora
    ) {}

    /**
     * Gera o PDF do contracheque
     */
    public function gerarPdf(Funcionario $funcionario, string $competencia, FolhaPagamento $folhaPagamento)
    {
        // dd($funcionario, $competencia, $folhaPagamento->lancamentos);

        $lancamentos = $folhaPagamento->lancamentos;

        $dataCompetencia = Carbon::parse($competencia . '-01');

        $funcionario->load('documentos', 'contrato', 'departamento', 'beneficios');

        // Usa os serviços de cálculo diretamente
        $calculadora = app(CalculoTrabalhistaService::class);

        $contrato = $funcionario->contrato;
        $salarioBase = (float) $contrato->salario_base;

        // Valores de entrada
        // $horasFeriado = (float) request('horas_extras', 8);
        // $faltasDias = (float) request('faltas_dias', 0.5);
        // $valeDia20 = (float) request('adiantamento', 696.40);
        $horasFeriado = 0;
        $faltasDias = 0;
        $valeDia20 = 0;

        foreach($lancamentos as $lancamento) {
            if ($lancamento->categoria == 'provento') {
                if ($lancamento->tipo == 'hora_extra_feriado') {
                    $horasFeriado += (float) $lancamento->quantidade;
                }


            }

            if( $lancamento->categoria == 'desconto') {
                if ($lancamento->tipo == 'vale_dia_20') {
                    $valeDia20 += (float) $lancamento->valor_unitario;
                }
                if ($lancamento->tipo == 'falta') {
                    $faltasDias += (float) $lancamento->quantidade;
                }
            }
        }


        // Valor hora
        $valoresHora = $calculadora->calcularValorHora($funcionario);

        // Dias de trabalho do contrato
        $diasTrabalhoRaw = $contrato->dias_trabalho;
        if (is_string($diasTrabalhoRaw)) {
            $diasTrabalho = json_decode($diasTrabalhoRaw, true);
        } else {
            $diasTrabalho = $diasTrabalhoRaw ?? [1, 2, 3, 4, 5];
        }
        $diasTrabalho = array_map('intval', $diasTrabalho);

        $diasUteis = $calculadora->calcularDiasUteis($dataCompetencia, $diasTrabalho);
        $domingosFeriados = $calculadora->calcularDomingosEFeriados($dataCompetencia);

        // Horas Extras Feriado (100%)
        $totalHE = round($horasFeriado * $valoresHora['valor_hora_feriado'], 2);

        // DSR Hora Extra
        $dsrHE = 0;
        if ($totalHE > 0 && $diasUteis > 0) {
            $dsrHE = round(($totalHE / $diasUteis) * $domingosFeriados, 2);
        }

        // Faltas (30 dias)
        $faltasValor = 0;
        if ($faltasDias > 0) {
            $faltasValor = round(($salarioBase / 30) * $faltasDias, 2);
        }

        // Base INSS
        $baseInss = round($salarioBase + $totalHE + $dsrHE - $faltasValor, 2);

        // INSS
        $inss = $calculadora->calcularINSS($baseInss);
        $aliquotaInss = $baseInss > 0 ? round(($inss / $baseInss) * 100, 4) : 0;

        // Proventos brutos
        $proventosBruto = $salarioBase + $totalHE + $dsrHE;

        // Descontos brutos
        $descontosBruto = $inss + $faltasValor + $valeDia20;

        // Arredondamento
        $liquidoBruto = $proventosBruto - $descontosBruto;
        $arredondamentoProvento = 0;
        if ($liquidoBruto != floor($liquidoBruto)) {
            $arredondamentoProvento = round(ceil($liquidoBruto) - $liquidoBruto, 2);
        }

        // Totais finais
        $totalProventos = $proventosBruto + $arredondamentoProvento;
        $totalDescontos = $descontosBruto;
        $salarioLiquido = $totalProventos - $totalDescontos;

        // FGTS
        $baseFGTS = $salarioBase + $totalHE + $dsrHE - $faltasValor;
        $fgts = round($baseFGTS * 0.08, 2);

        $folha = [
            'salario_base'             => $salarioBase,
            'horas_extras_totais'      => $horasFeriado,
            'total_horas_extras'       => $totalHE,
            'dsr_he'                   => $dsrHE,
            'faltas_dias'              => $faltasDias,
            'faltas_valor'             => $faltasValor,
            'faltas_dsr'               => 0,  // 🆕
            'faltas_valor_dia'         => round($salarioBase / 30, 2),  // 🆕
            'inss'                     => $inss,
            'inss_aliquota'            => $aliquotaInss,
            'adiantamento_salarial'    => $valeDia20,
            'arredondamento_provento'  => $arredondamentoProvento,
            'arredondamento_desconto'  => 0,
            'total_proventos'          => $totalProventos,
            'total_descontos'          => $totalDescontos,
            'salario_liquido'          => $salarioLiquido,
            'dias_uteis'               => $diasUteis,
            'domingos_feriados'        => $domingosFeriados,
            'base_inss'                => $baseInss,
            'base_fgts'                => $baseFGTS,
            'fgts_valor'               => $fgts,
            'base_irrf'                => 0,
            'irrf_valor'               => 0,
            'salario_familia'          => 0,
            'vale_transporte'          => 0,
            'valor_hora_normal'        => $valoresHora['valor_hora_normal'],
            'valor_hora_extra'         => $valoresHora['valor_hora_extra'],
        ];

        // Buscar documentos para CPF, PIS, CTPS
        $documentos = $funcionario->documentos;
        $contrato = $funcionario->contrato;



        $dados = [
            'funcionario' => $funcionario,
            'folha' => $folha,
            'competencia' => $dataCompetencia->translatedFormat('F/Y'),
            'titulo' => 'Recibo de ' . $dataCompetencia->translatedFormat('F') . ' de ' . $dataCompetencia->year,
            'ccusto' => $funcionario->departamento?->nome ?? '26',
            'contrato' => $funcionario->contrato,
            'documentos' => $funcionario->documentos,
            'diasUteis' => $folha['dias_uteis'],
        ];




        $pdf = Pdf::loadView('rh.folha-pagamento.pdf.contracheque-pdf', $dados);

        // Configurações do papel
        $pdf->setPaper('A4', 'portrait');

        // Nome do arquivo
        $fileName = sprintf(
            'CCusto_%s_ReciboMensalNormal_%s.pdf',
            $dados['ccusto'],
            $dataCompetencia->format('mY')
        );

        // return $pdf->download($fileName);
        return $pdf->stream($fileName);
    }

    /**
     * Visualizar no navegador antes de baixar
     */
    public function visualizar(Funcionario $funcionario, string $competencia, FolhaPagamento $folhaPagamento)
    {
       $dataCompetencia = Carbon::parse($competencia . '-01');
       $lancamentos = $folhaPagamento->lancamentos;

        $funcionario->load('documentos', 'contrato', 'departamento', 'beneficios');

        // Usa os serviços de cálculo diretamente
        $calculadora = app(CalculoTrabalhistaService::class);

        $contrato = $funcionario->contrato;
        $salarioBase = (float) $contrato->salario_base;

        // dd($funcionario, $competencia, $folhaPagamento->lancamentos);

        // Valores de entrada
        // $horasFeriado = (float) request('horas_extras', 8);
        // $faltasDias = (float) request('faltas_dias', 0.5);
        // $valeDia20 = (float) request('adiantamento', 696.40);
        $horasFeriado = 0.0;
        $faltasDias = 0.0;
        $valeDia20 = 0.0;

        foreach($lancamentos as $lancamento) {
            if ($lancamento->categoria == 'provento') {
                if ($lancamento->tipo == 'hora_extra_feriado') {
                    $horasFeriado += (float) $lancamento->quantidade;
                }


            }

            if( $lancamento->categoria == 'desconto') {
                if ($lancamento->tipo == 'vale_dia_20') {
                    $valeDia20 += (float) $lancamento->valor_unitario;
                }
                if ($lancamento->tipo == 'falta') {
                    $faltasDias += (float) $lancamento->quantidade;
                }
            }
        }

        // dd($horasFeriado, $faltasDias, $valeDia20);

        // Valor hora
        $valoresHora = $calculadora->calcularValorHora($funcionario);

        // Dias de trabalho do contrato
        $diasTrabalhoRaw = $contrato->dias_trabalho;
        if (is_string($diasTrabalhoRaw)) {
            $diasTrabalho = json_decode($diasTrabalhoRaw, true);
        } else {
            $diasTrabalho = $diasTrabalhoRaw ?? [1, 2, 3, 4, 5];
        }
        $diasTrabalho = array_map('intval', $diasTrabalho);

        $diasUteis = $calculadora->calcularDiasUteis($dataCompetencia, $diasTrabalho);
        $domingosFeriados = $calculadora->calcularDomingosEFeriados($dataCompetencia);

        // Horas Extras Feriado (100%)
        $totalHE = round($horasFeriado * $valoresHora['valor_hora_feriado'], 2);

        // DSR Hora Extra
        $dsrHE = 0;
        if ($totalHE > 0 && $diasUteis > 0) {
            $dsrHE = round(($totalHE / $diasUteis) * $domingosFeriados, 2);
        }

        // Faltas (30 dias)
        $faltasValor = 0;
        if ($faltasDias > 0) {
            $faltasValor = round(($salarioBase / 30) * $faltasDias, 2);
        }

        // Base INSS
        $baseInss = round($salarioBase + $totalHE + $dsrHE - $faltasValor, 2);

        // INSS
        $inss = $calculadora->calcularINSS($baseInss);
        $aliquotaInss = $baseInss > 0 ? round(($inss / $baseInss) * 100, 4) : 0;

        // Proventos brutos
        $proventosBruto = $salarioBase + $totalHE + $dsrHE;

        // Descontos brutos
        $descontosBruto = $inss + $faltasValor + $valeDia20;

        // Arredondamento
        $liquidoBruto = $proventosBruto - $descontosBruto;
        $arredondamentoProvento = 0;
        if ($liquidoBruto != floor($liquidoBruto)) {
            $arredondamentoProvento = round(ceil($liquidoBruto) - $liquidoBruto, 2);
        }

        // Totais finais
        $totalProventos = $proventosBruto + $arredondamentoProvento;
        $totalDescontos = $descontosBruto;
        $salarioLiquido = $totalProventos - $totalDescontos;

        // FGTS
        $baseFGTS = $salarioBase + $totalHE + $dsrHE - $faltasValor;
        $fgts = round($baseFGTS * 0.08, 2);

        $folha = [
            'salario_base'             => $salarioBase,
            'horas_extras_totais'      => $horasFeriado,
            'total_horas_extras'       => $totalHE,
            'dsr_he'                   => $dsrHE,
            'faltas_dias'              => $faltasDias,
            'faltas_valor'             => $faltasValor,
            'faltas_dsr'               => 0,  // 🆕
            'faltas_valor_dia'         => round($salarioBase / 30, 2),  // 🆕
            'inss'                     => $inss,
            'inss_aliquota'            => $aliquotaInss,
            'adiantamento_salarial'    => $valeDia20,
            'arredondamento_provento'  => $arredondamentoProvento,
            'arredondamento_desconto'  => 0,
            'total_proventos'          => $totalProventos,
            'total_descontos'          => $totalDescontos,
            'salario_liquido'          => $salarioLiquido,
            'dias_uteis'               => $diasUteis,
            'domingos_feriados'        => $domingosFeriados,
            'base_inss'                => $baseInss,
            'base_fgts'                => $baseFGTS,
            'fgts_valor'               => $fgts,
            'base_irrf'                => 0,
            'irrf_valor'               => 0,
            'salario_familia'          => 0,
            'vale_transporte'          => 0,
            'valor_hora_normal'        => $valoresHora['valor_hora_normal'],
            'valor_hora_extra'         => $valoresHora['valor_hora_extra'],
        ];

        return view('rh.folha-pagamento.pdf.contracheque', [
            'funcionario' => $funcionario,
            'folha' => $folha,
            'competencia' => $dataCompetencia->translatedFormat('F/Y'),
            'titulo' => 'Recibo de ' . $dataCompetencia->translatedFormat('F') . ' de ' . $dataCompetencia->year,
            'ccusto' => $funcionario->departamento?->nome ?? '26',
            'contrato' => $funcionario->contrato,
            'documentos' => $funcionario->documentos,
        ]);
    }
}