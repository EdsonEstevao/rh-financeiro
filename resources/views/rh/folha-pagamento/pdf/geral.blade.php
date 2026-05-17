<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Folha Geral — {{ $competencia }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9px;
            color: #1f2937;
            background: #fff;
            padding: 16px 20px;
        }

        /* ─── HEADER ─────────────────────────────────────── */
        .header {
            border-bottom: 3px solid #4338ca;
            padding-bottom: 10px;
            margin-bottom: 14px;
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
            font-size: 16px;
            font-weight: bold;
            color: #4338ca;
        }

        .empresa-sub {
            font-size: 9px;
            color: #6b7280;
            margin-top: 1px;
        }

        .doc-title {
            text-align: right;
        }

        .doc-title h2 {
            font-size: 13px;
            font-weight: bold;
        }

        .doc-title p {
            font-size: 9px;
            color: #6b7280;
            margin-top: 2px;
        }

        /* ─── CARDS RESUMO ───────────────────────────────── */
        .resumo-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 6px 0;
            margin-bottom: 14px;
        }

        .resumo-card {
            padding: 8px 10px;
            border-radius: 4px;
            text-align: center;
            width: 25%;
        }

        .card-blue {
            background: #e0e7ff;
            border: 1px solid #a5b4fc;
        }

        .card-green {
            background: #dcfce7;
            border: 1px solid #86efac;
        }

        .card-red {
            background: #fee2e2;
            border: 1px solid #fca5a5;
        }

        .card-indigo {
            background: #4338ca;
            border: 1px solid #4338ca;
        }

        .card-label {
            font-size: 8px;
            text-transform: uppercase;
            font-weight: bold;
        }

        .card-blue .card-label {
            color: #3730a3;
        }

        .card-green .card-label {
            color: #166534;
        }

        .card-red .card-label {
            color: #991b1b;
        }

        .card-indigo .card-label {
            color: #c7d2fe;
        }

        .card-valor {
            font-size: 13px;
            font-weight: bold;
            margin-top: 3px;
        }

        .card-blue .card-valor {
            color: #3730a3;
        }

        .card-green .card-valor {
            color: #166534;
        }

        .card-red .card-valor {
            color: #991b1b;
        }

        .card-indigo .card-valor {
            color: #ffffff;
        }

        /* ─── TABELA PRINCIPAL ───────────────────────────── */
        .main-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #e5e7eb;
        }

        .main-table thead tr th {
            background: #4338ca;
            color: #ffffff;
            padding: 6px 5px;
            text-align: right;
            font-size: 8px;
            text-transform: uppercase;
            white-space: nowrap;
            border: 1px solid #4f46e5;
        }

        .main-table thead tr th:first-child {
            text-align: left;
        }

        .main-table thead tr th.th-green {
            color: #86efac;
        }

        .main-table thead tr th.th-red {
            color: #fca5a5;
        }

        .main-table thead tr th.th-yellow {
            color: #fde047;
        }

        .main-table tbody tr td {
            padding: 5px 5px;
            font-size: 9px;
            border-bottom: 1px solid #f3f4f6;
            border-right: 1px solid #f3f4f6;
            text-align: right;
            white-space: nowrap;
        }

        .main-table tbody tr td:first-child {
            text-align: left;
        }

        .main-table tbody tr:nth-child(even) td {
            background: #f9fafb;
        }

        .main-table tbody tr:hover td {
            background: #eff6ff;
        }

        .td-nome {
            font-weight: bold;
            color: #111827;
            min-width: 120px;
        }

        .td-verde {
            color: #166534;
            font-weight: bold;
        }

        .td-red {
            color: #991b1b;
            font-weight: bold;
        }

        .td-indigo {
            color: #3730a3;
            font-weight: bold;
        }

        /* ─── BADGE ──────────────────────────────────────── */
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 7px;
            font-weight: bold;
        }

        .badge-pago {
            background: #dcfce7;
            color: #166534;
        }

        .badge-processado {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-pendente {
            background: #fef9c3;
            color: #854d0e;
        }

        /* ─── TFOOT ──────────────────────────────────────── */
        .main-table tfoot tr td {
            padding: 7px 5px;
            font-size: 9px;
            font-weight: bold;
            border-top: 2px solid #4338ca;
            text-align: right;
            white-space: nowrap;
        }

        .main-table tfoot tr td:first-child {
            text-align: left;
            background: #4338ca;
            color: #fff;
        }

        .main-table tfoot .tf-normal {
            background: #f0f4ff;
            color: #1f2937;
        }

        .main-table tfoot .tf-green {
            background: #f0fdf4;
            color: #166534;
        }

        .main-table tfoot .tf-red {
            background: #fff1f2;
            color: #991b1b;
        }

        .main-table tfoot .tf-indigo {
            background: #4338ca;
            color: #fde047;
            font-size: 11px;
        }

        /* ─── FOOTER ─────────────────────────────────────── */
        .footer {
            margin-top: 16px;
            border-top: 1px solid #e5e7eb;
            padding-top: 6px;
            font-size: 8px;
            color: #9ca3af;
        }

        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }

        .footer-table td {
            padding: 0;
        }

        .text-right {
            text-align: right;
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
                    <div class="empresa-sub">Sistema de Recursos Humanos — Relatório Geral</div>
                </td>
                <td class="doc-title">
                    <h2>FOLHA DE PAGAMENTO GERAL</h2>
                    <p>
                        Competência:
                        <strong>
                            {{ \Carbon\Carbon::createFromFormat('Y-m', $competencia)->translatedFormat('F \d\e Y') }}
                        </strong>
                    </p>
                    <p>Gerado em {{ now()->format('d/m/Y H:i') }}</p>
                </td>
            </tr>
        </table>
    </div>

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- CARDS RESUMO                                            --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <table class="resumo-table">
        <tr>
            <td class="resumo-card card-blue">
                <div class="card-label">Funcionários</div>
                <div class="card-valor">{{ $totais->total_funcionarios }}</div>
            </td>
            <td class="resumo-card card-green">
                <div class="card-label">Total Proventos</div>
                <div class="card-valor">
                    R$ {{ number_format($totais->total_proventos, 2, ',', '.') }}
                </div>
            </td>
            <td class="resumo-card card-red">
                <div class="card-label">Total Descontos</div>
                <div class="card-valor">
                    R$ {{ number_format($totais->total_descontos, 2, ',', '.') }}
                </div>
            </td>
            <td class="resumo-card card-indigo">
                <div class="card-label">Total Líquido</div>
                <div class="card-valor">
                    R$ {{ number_format($totais->total_salario_liquido, 2, ',', '.') }}
                </div>
            </td>
        </tr>
    </table>

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- TABELA PRINCIPAL                                        --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <table class="main-table">
        <thead>
            <tr>
                <th>Funcionário</th>
                <th>Status</th>
                <th>Sal. Base</th>
                <th>Grat. Fer.</th>
                <th>DSR Hr</th>
                <th>Sal. Fam.</th>
                <th>Arred. P.</th>
                <th class="th-green">Total Prov.</th>
                <th>INSS</th>
                <th>Vale 20</th>
                <th>Vale Ext.</th>
                <th>Faltas</th>
                <th>DSR Falt.</th>
                <th>Arred. D.</th>
                <th class="th-red">Total Desc.</th>
                <th class="th-yellow">Líquido</th>
            </tr>
        </thead>
        <tbody>
            @forelse($folhas as $folha)
                <tr>
                    <td class="td-nome">{{ $folha->funcionario->nome_completo ?? '—' }}</td>
                    <td style="text-align:center;">
                        @php
                            $bClass = match ($folha->status) {
                                'pago' => 'badge-pago',
                                'processado' => 'badge-processado',
                                default => 'badge-pendente',
                            };
                            $bLabel = match ($folha->status) {
                                'pago' => 'Pago',
                                'processado' => 'Proc.',
                                default => 'Pend.',
                            };
                        @endphp
                        <span class="badge {{ $bClass }}">{{ $bLabel }}</span>
                    </td>
                    <td>R$ {{ number_format($folha->salario_base, 2, ',', '.') }}</td>
                    <td>R$ {{ number_format($folha->gratificacao_feriado ?? 0, 2, ',', '.') }}</td>
                    <td>R$ {{ number_format($folha->horas_extras_totais ?? 0, 2, ',', '.') }}</td>
                    <td>R$ {{ number_format($folha->salario_familia_hr_extra ?? 0, 2, ',', '.') }}</td>
                    <td>R$ {{ number_format($folha->arredondamento_provento ?? 0, 2, ',', '.') }}</td>
                    <td class="td-verde">
                        R$ {{ number_format($folha->total_proventos, 2, ',', '.') }}
                    </td>
                    <td>R$ {{ number_format($folha->desconto_inss ?? 0, 2, ',', '.') }}</td>
                    <td>R$ {{ number_format($folha->vale_dia_20 ?? 0, 2, ',', '.') }}</td>
                    <td>R$ {{ number_format($folha->vale_extra ?? 0, 2, ',', '.') }}</td>
                    <td>R$ {{ number_format($folha->faltas_valor ?? 0, 2, ',', '.') }}</td>
                    <td>R$ {{ number_format($folha->dsr_faltas ?? 0, 2, ',', '.') }}</td>
                    <td>R$ {{ number_format($folha->arredondamento_desconto ?? 0, 2, ',', '.') }}</td>
                    <td class="td-red">
                        R$ {{ number_format($folha->total_descontos, 2, ',', '.') }}
                    </td>
                    <td class="td-indigo">
                        R$ {{ number_format($folha->salario_liquido, 2, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="16" style="text-align:center; padding:20px; color:#9ca3af;">
                        Nenhuma folha encontrada para esta competência.
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td>TOTAIS ({{ $totais->total_funcionarios }} func.)</td>
                <td class="tf-normal"></td>
                <td class="tf-normal">R$ {{ number_format($totais->total_salario_base ?? 0, 2, ',', '.') }}</td>
                <td class="tf-normal">—</td>
                <td class="tf-normal">—</td>
                <td class="tf-normal">—</td>
                <td class="tf-normal">—</td>
                <td class="tf-green">R$ {{ number_format($totais->total_proventos ?? 0, 2, ',', '.') }}</td>
                <td class="tf-normal">R$ {{ number_format($totais->total_desconto_inss ?? 0, 2, ',', '.') }}</td>
                <td class="tf-normal">—</td>
                <td class="tf-normal">—</td>
                <td class="tf-normal">—</td>
                <td class="tf-normal">—</td>
                <td class="tf-normal">—</td>
                <td class="tf-red">R$ {{ number_format($totais->total_descontos ?? 0, 2, ',', '.') }}</td>
                <td class="tf-indigo">R$ {{ number_format($totais->total_salario_liquido ?? 0, 2, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

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
                <td class="text-right">
                    {{ config('app.name') }} — Documento confidencial. Uso interno.
                </td>
            </tr>
        </table>
    </div>

</body>

</html>
