<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Folha de Pagamento</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #1f2937;
            background: #fff;
            padding: 20px 30px;
        }

        /* ─── HEADER ─────────────────────────────────────── */
        .header {
            border-bottom: 3px solid #4338ca;
            padding-bottom: 14px;
            margin-bottom: 18px;
        }

        .header-top {
            display: flex;
            /* dompdf usa table para layout */
            width: 100%;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            vertical-align: middle;
            padding: 0;
        }

        .empresa-nome {
            font-size: 18px;
            font-weight: bold;
            color: #4338ca;
        }

        .empresa-sub {
            font-size: 10px;
            color: #6b7280;
            margin-top: 2px;
        }

        .doc-title {
            text-align: right;
        }

        .doc-title h2 {
            font-size: 15px;
            font-weight: bold;
            color: #1f2937;
        }

        .doc-title p {
            font-size: 10px;
            color: #6b7280;
            margin-top: 2px;
        }

        /* ─── BADGE STATUS ───────────────────────────────── */
        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: bold;
        }

        .badge-pendente {
            background: #fef9c3;
            color: #854d0e;
        }

        .badge-processado {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-pago {
            background: #dcfce7;
            color: #166534;
        }

        /* ─── SEÇÃO ──────────────────────────────────────── */
        .section {
            margin-bottom: 16px;
        }

        .section-title {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 5px 10px;
            margin-bottom: 0;
        }

        .section-title-green {
            background: #dcfce7;
            color: #166534;
        }

        .section-title-red {
            background: #fee2e2;
            color: #991b1b;
        }

        .section-title-blue {
            background: #e0e7ff;
            color: #3730a3;
        }

        .section-title-gray {
            background: #f3f4f6;
            color: #374151;
        }

        /* ─── IDENTIFICAÇÃO ──────────────────────────────── */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #e5e7eb;
        }

        .info-table td {
            padding: 7px 10px;
            border: 1px solid #e5e7eb;
            font-size: 11px;
        }

        .info-table .label {
            background: #f9fafb;
            font-weight: bold;
            color: #6b7280;
            font-size: 10px;
            text-transform: uppercase;
            width: 22%;
        }

        /* ─── TABELA PROVENTOS / DESCONTOS ───────────────── */
        .cols-table {
            width: 100%;
            border-collapse: collapse;
        }

        .cols-table td,
        .cols-table th {
            padding: 0;
            vertical-align: top;
        }

        .col-half {
            width: 49%;
        }

        .col-space {
            width: 2%;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #e5e7eb;
        }

        .items-table tr td {
            padding: 6px 10px;
            font-size: 11px;
            border-bottom: 1px solid #f3f4f6;
        }

        .items-table .td-label {
            color: #374151;
        }

        .items-table .td-valor {
            text-align: right;
            color: #111827;
            font-weight: 600;
        }

        .items-table .td-total {
            font-weight: bold;
            font-size: 12px;
            border-top: 2px solid;
        }

        .td-total-green {
            background: #f0fdf4;
            color: #166534;
            border-color: #4ade80;
        }

        .td-total-red {
            background: #fff1f2;
            color: #991b1b;
            border-color: #f87171;
        }

        /* ─── LÍQUIDO ────────────────────────────────────── */
        .liquido-box {
            background: #4338ca;
            color: #fff;
            padding: 14px 20px;
            margin-top: 16px;
            border-radius: 6px;
        }

        .liquido-table {
            width: 100%;
            border-collapse: collapse;
        }

        .liquido-table td {
            padding: 0;
            vertical-align: middle;
        }

        .liquido-label {
            font-size: 11px;
            color: #c7d2fe;
            text-transform: uppercase;
            font-weight: bold;
        }

        .liquido-sub {
            font-size: 10px;
            color: #a5b4fc;
            margin-top: 2px;
        }

        .liquido-valor {
            text-align: right;
            font-size: 24px;
            font-weight: bold;
            color: #ffffff;
        }

        /* ─── OBSERVAÇÃO ─────────────────────────────────── */
        .obs-box {
            border: 1px solid #e5e7eb;
            padding: 10px;
            font-size: 11px;
            color: #374151;
            background: #f9fafb;
            margin-top: 14px;
            border-radius: 4px;
        }

        .obs-label {
            font-weight: bold;
            color: #6b7280;
            text-transform: uppercase;
            font-size: 9px;
            margin-bottom: 4px;
        }

        /* ─── ASSINATURAS ────────────────────────────────── */
        .assinaturas {
            margin-top: 40px;
        }

        .ass-table {
            width: 100%;
            border-collapse: collapse;
        }

        .ass-table td {
            text-align: center;
            padding: 0 20px;
            width: 33%;
        }

        .ass-line {
            border-top: 1px solid #374151;
            padding-top: 6px;
            margin-top: 40px;
            font-size: 10px;
            color: #374151;
        }

        .ass-sub {
            font-size: 9px;
            color: #9ca3af;
            margin-top: 2px;
        }

        /* ─── FOOTER ─────────────────────────────────────── */
        .footer {
            margin-top: 20px;
            border-top: 1px solid #e5e7eb;
            padding-top: 8px;
            font-size: 9px;
            color: #9ca3af;
        }

        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }

        .footer-table td {
            padding: 0;
        }

        .footer-right {
            text-align: right;
        }

        /* ─── UTILITÁRIOS ────────────────────────────────── */
        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .fw-bold {
            font-weight: bold;
        }

        .mt-10 {
            margin-top: 10px;
        }

        .mt-16 {
            margin-top: 16px;
        }
    </style>
</head>

<body>

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- HEADER                                                  --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <div class="header">
        <table class="header-table">
            <tr>
                <td>
                    <div class="empresa-nome">{{ config('app.name', 'RH & Financeiro') }}</div>
                    <div class="empresa-sub">Sistema de Recursos Humanos</div>
                </td>
                <td class="doc-title">
                    <h2>HOLERITE / RECIBO DE PAGAMENTO</h2>
                    <p>
                        Competência:
                        <strong>
                            {{ \Carbon\Carbon::parse($folhaPagamento->competencia)->translatedFormat('F \d\e Y') }}
                        </strong>
                    </p>
                    <p style="margin-top:4px;">
                        @php
                            $badge = match ($folhaPagamento->status) {
                                'pago' => 'badge-pago',
                                'processado' => 'badge-processado',
                                default => 'badge-pendente',
                            };
                            $slabel = match ($folhaPagamento->status) {
                                'pago' => 'PAGO',
                                'processado' => 'PROCESSADO',
                                default => 'PENDENTE',
                            };
                        @endphp
                        <span class="badge {{ $badge }}">{{ $slabel }}</span>
                    </p>
                </td>
            </tr>
        </table>
    </div>

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- IDENTIFICAÇÃO                                           --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <div class="section">
        <div class="section-title section-title-gray">Dados do Funcionário</div>
        <table class="info-table">
            <tr>
                <td class="label">Funcionário</td>
                <td><strong>{{ $folhaPagamento->funcionario->nome_completo ?? '—' }}</strong></td>
                <td class="label">Cargo</td>
                <td>{{ $folhaPagamento->funcionario->cargo->titulo ?? '—' }}</td>
            </tr>
            <tr>
                <td class="label">CPF</td>
                <td>{{ $folhaPagamento->funcionario->cpf ?? '—' }}</td>
                <td class="label">Admissão</td>
                <td>
                    {{ $folhaPagamento->funcionario->data_admissao
                        ? \Carbon\Carbon::parse($folhaPagamento->funcionario->data_admissao)->format('d/m/Y')
                        : '—' }}
                </td>
            </tr>
            <tr>
                <td class="label">Departamento</td>
                <td>{{ $folhaPagamento->funcionario->departamento->nome ?? '—' }}</td>
                <td class="label">Quinto Dia Útil</td>
                <td>
                    {{ $folhaPagamento->quinto_dia_util
                        ? \Carbon\Carbon::parse($folhaPagamento->quinto_dia_util)->format('d/m/Y')
                        : '—' }}
                </td>
            </tr>
        </table>
    </div>

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- PROVENTOS + DESCONTOS (lado a lado)                     --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <table class="cols-table">
        <tr>

            {{-- PROVENTOS --}}
            <td class="col-half">
                <div class="section-title section-title-green">Proventos</div>
                <table class="items-table">
                    @php
                        $proventos = [
                            'Salário Base' => $folhaPagamento->salario_base,
                            'Gratificação Feriado' => $folhaPagamento->gratificacao_feriado,
                            'DSR Hora Extra' => $folhaPagamento->horas_extras_totais,
                            'Salário Família / Hr Extra' => $folhaPagamento->salario_familia_hr_extra,
                            'Arredondamento (Provento)' => $folhaPagamento->arredondamento_provento,
                        ];
                    @endphp
                    @foreach ($proventos as $item => $valor)
                        <tr>
                            <td class="td-label">{{ $item }}</td>
                            <td class="td-valor">R$ {{ number_format($valor ?? 0, 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td class="td-total td-total-green td-label">TOTAL PROVENTOS</td>
                        <td class="td-total td-total-green td-valor">
                            R$ {{ number_format($folhaPagamento->total_proventos, 2, ',', '.') }}
                        </td>
                    </tr>
                </table>
            </td>

            <td class="col-space"></td>

            {{-- DESCONTOS --}}
            <td class="col-half">
                <div class="section-title section-title-red">Descontos</div>
                <table class="items-table">
                    @php
                        $descontos = [
                            'Desconto INSS' => $folhaPagamento->desconto_inss,
                            'Vale Dia 20' => $folhaPagamento->vale_dia_20,
                            'Vale Extra' => $folhaPagamento->vale_extra,
                            'Faltas (Valor)' => $folhaPagamento->faltas_valor,
                            'DSR Faltas' => $folhaPagamento->dsr_faltas,
                            'Arredondamento (Desconto)' => $folhaPagamento->arredondamento_desconto,
                        ];
                    @endphp
                    @foreach ($descontos as $item => $valor)
                        <tr>
                            <td class="td-label">{{ $item }}</td>
                            <td class="td-valor">R$ {{ number_format($valor ?? 0, 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td class="td-total td-total-red td-label">TOTAL DESCONTOS</td>
                        <td class="td-total td-total-red td-valor">
                            R$ {{ number_format($folhaPagamento->total_descontos, 2, ',', '.') }}
                        </td>
                    </tr>
                </table>
            </td>

        </tr>
    </table>

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- SALÁRIO LÍQUIDO                                         --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <div class="liquido-box">
        <table class="liquido-table">
            <tr>
                <td>
                    <div class="liquido-label">Salário Líquido a Receber</div>
                    <div class="liquido-sub">
                        Referente a
                        {{ \Carbon\Carbon::parse($folhaPagamento->competencia)->translatedFormat('F \d\e Y') }}
                    </div>
                </td>
                <td class="liquido-valor">
                    R$ {{ number_format($folhaPagamento->salario_liquido, 2, ',', '.') }}
                </td>
            </tr>
        </table>
    </div>

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- OBSERVAÇÃO                                              --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    @if ($folhaPagamento->observacao)
        <div class="obs-box mt-16">
            <div class="obs-label">Observação</div>
            <div>{{ $folhaPagamento->observacao }}</div>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- ASSINATURAS                                             --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <div class="assinaturas">
        <table class="ass-table">
            <tr>
                <td>
                    <div class="ass-line">
                        {{ $folhaPagamento->funcionario->nome ?? '—' }}
                    </div>
                    <div class="ass-sub">Assinatura do Funcionário</div>
                </td>
                <td>
                    <div class="ass-line">Responsável RH</div>
                    <div class="ass-sub">Assinatura do RH</div>
                </td>
                <td>
                    <div class="ass-line">Diretor / Gerente</div>
                    <div class="ass-sub">Aprovação</div>
                </td>
            </tr>
        </table>
    </div>

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- FOOTER                                                  --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <div class="footer">
        <table class="footer-table">
            <tr>
                <td>
                    Gerado em {{ now()->format('d/m/Y \à\s H:i') }}
                    por {{ auth()->user()->name ?? 'Sistema' }}
                </td>
                <td class="footer-right">
                    {{ config('app.name') }} — Documento confidencial
                </td>
            </tr>
        </table>
    </div>

</body>

</html>
