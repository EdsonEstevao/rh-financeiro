<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>{{ $titulo }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', monospace;
            font-size: 11px;
            color: #000;
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }

        /* Cabeçalho */
        .header {
            text-align: center;
            margin-bottom: 5px;
            padding-bottom: 5px;
            border-bottom: 1px dashed #000;
        }

        .header h2 {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .header .subtitle {
            font-size: 10px;
        }

        /* Dados do funcionário */
        .dados-funcionario {
            font-size: 9px;
            margin-bottom: 8px;
            line-height: 1.4;
        }

        .dados-funcionario span {
            margin-right: 15px;
        }

        /* Tabela principal */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }

        table th {
            background-color: #e0e0e0;
            border: 1px solid #000;
            padding: 3px 5px;
            text-align: center;
            font-size: 9px;
            font-weight: bold;
        }

        table td {
            border: 1px solid #000;
            padding: 3px 5px;
            font-size: 10px;
        }

        .text-left {
            text-align: left;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .col-codigo {
            width: 8%;
        }

        .col-descricao {
            width: 42%;
        }

        .col-referencia {
            width: 15%;
        }

        .col-proventos {
            width: 17.5%;
        }

        .col-descontos {
            width: 17.5%;
        }

        /* Totais */
        .totais {
            font-weight: bold;
            background-color: #f5f5f5;
        }

        /* Salário Líquido */
        .salario-liquido {
            margin-top: 8px;
            padding: 5px;
            border: 2px solid #000;
            text-align: center;
            font-size: 12px;
            font-weight: bold;
        }

        /* Bases de cálculo */
        .bases-calculo {
            margin-top: 10px;
            font-size: 9px;
            border: 1px solid #000;
            padding: 5px;
        }

        .bases-calculo table {
            border: none;
        }

        .bases-calculo table td {
            border: none;
            padding: 1px 8px;
        }

        .bases-calculo .label {
            font-weight: bold;
        }

        /* Rodapé */
        .footer {
            margin-top: 15px;
            font-size: 8px;
            text-align: center;
            border-top: 1px solid #000;
            padding-top: 5px;
        }
    </style>
</head>

<body>
    <div class="container">

        {{-- Cabeçalho --}}
        <div class="header">
            <h2>{{ $titulo }}</h2>
            <div class="subtitle">CCusto {{ $ccusto }} - Recibo Mensal Normal - {{ $competencia }}</div>
        </div>

        {{-- Dados do Funcionário --}}
        <div class="dados-funcionario">
            <span><strong>Admissão:</strong> {{ $contrato?->data_admissao?->format('d/m/Y') }}</span>
            <span><strong>CPF:</strong> {{ $documentos?->cpf }}</span>
            <span><strong>PIS:</strong> {{ $documentos?->pis_pasep }}</span>
            <span><strong>CTPS:</strong> {{ $documentos?->ctps_numero }} {{ $documentos?->ctps_serie }}</span>
        </div>

        {{-- Tabela de Proventos e Descontos --}}
        <table>
            <thead>
                <tr>
                    <th class="col-codigo">CÓDIGO</th>
                    <th class="col-descricao">DESCRIÇÕES</th>
                    <th class="col-referencia">REFERÊNCIAS</th>
                    <th class="col-proventos">PROVENTOS</th>
                    <th class="col-descontos">DESCONTOS</th>
                </tr>
            </thead>
            <tbody>
                {{-- Salário Mensalista --}}
                <tr>
                    <td class="text-center"></td>
                    <td class="text-left">Salário Mensalista</td>
                    <td class="text-center">30,00</td>
                    <td class="text-right">{{ number_format($folha['salario_base'], 2, ',', '.') }}</td>
                    <td class="text-right"></td>
                </tr>

                {{-- Horas Extras 100% --}}
                <tr>
                    <td class="text-center">411</td>
                    <td class="text-left">Horas Extras 100%</td>
                    <td class="text-center">{{ number_format($folha['horas_extras_totais'], 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($folha['total_horas_extras'], 2, ',', '.') }}</td>
                    <td class="text-right"></td>
                </tr>

                {{-- D.S.R. Horas Extras --}}
                <tr>
                    <td class="text-center">543</td>
                    <td class="text-left">D.S.R. Horas Extras</td>
                    <td class="text-center">{{ number_format($folha['dias_uteis'], 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($folha['dsr_he'], 2, ',', '.') }}</td>
                    <td class="text-right"></td>
                </tr>

                {{-- Arredondamento Provento (se houver) --}}
                @if ($folha['arredondamento_provento'] > 0)
                    <tr>
                        <td class="text-center">9002</td>
                        <td class="text-left">Arredondamento Provento Folha</td>
                        <td class="text-center"></td>
                        <td class="text-right">{{ number_format($folha['arredondamento_provento'], 2, ',', '.') }}</td>
                        <td class="text-right"></td>
                    </tr>
                @endif

                {{-- Faltas não Justificadas --}}
                @if ($folha['faltas_valor'] > 0)
                    <tr>
                        <td class="text-center">216</td>
                        <td class="text-left">Faltas não Justificadas Dias</td>
                        <td class="text-center">{{ number_format($folha['faltas_dias'], 2, ',', '.') }}</td>
                        <td class="text-right"></td>
                        <td class="text-right">{{ number_format($folha['faltas_valor'], 2, ',', '.') }}</td>
                    </tr>
                @endif

                {{-- D.S.R. Faltas (se houver) --}}
                @if (($folha['faltas_dsr'] ?? 0) > 0)
                    <tr>
                        <td class="text-center">217</td>
                        <td class="text-left">D.S.R. Faltas</td>
                        <td class="text-center"></td>
                        <td class="text-right"></td>
                        <td class="text-right">{{ number_format($folha['faltas_dsr'], 2, ',', '.') }}</td>
                    </tr>
                @endif

                {{-- Adiantamento Salarial --}}
                @if ($folha['adiantamento_salarial'] > 0)
                    <tr>
                        <td class="text-center">901</td>
                        <td class="text-left">Desconto Adiantamento salarial</td>
                        <td class="text-center"></td>
                        <td class="text-right"></td>
                        <td class="text-right">{{ number_format($folha['adiantamento_salarial'], 2, ',', '.') }}</td>
                    </tr>
                @endif

                {{-- Vale Transporte --}}
                @if ($folha['vale_transporte'] > 0)
                    <tr>
                        <td class="text-center">902</td>
                        <td class="text-left">Vale Transporte</td>
                        <td class="text-center"></td>
                        <td class="text-right"></td>
                        <td class="text-right">{{ number_format($folha['vale_transporte'], 2, ',', '.') }}</td>
                    </tr>
                @endif

                {{-- I.N.S.S. --}}
                @if ($folha['inss'] > 0)
                    <tr>
                        <td class="text-center">9101</td>
                        <td class="text-left">I.N.S.S.</td>
                        <td class="text-center">{{ number_format($folha['inss_aliquota'], 4, ',', '.') }}</td>
                        <td class="text-right"></td>
                        <td class="text-right">{{ number_format($folha['inss'], 2, ',', '.') }}</td>
                    </tr>
                @endif

                {{-- I.R.R.F. (se houver) --}}
                @if ($folha['irrf_valor'] > 0)
                    <tr>
                        <td class="text-center">9201</td>
                        <td class="text-left">I.R.R.F.</td>
                        <td class="text-center"></td>
                        <td class="text-right"></td>
                        <td class="text-right">{{ number_format($folha['irrf_valor'], 2, ',', '.') }}</td>
                    </tr>
                @endif

                {{-- Arredondamento Desconto (se houver) --}}
                @if ($folha['arredondamento_desconto'] > 0)
                    <tr>
                        <td class="text-center">9002</td>
                        <td class="text-left">Arredondamento Desconto Folha</td>
                        <td class="text-center"></td>
                        <td class="text-right"></td>
                        <td class="text-right">{{ number_format($folha['arredondamento_desconto'], 2, ',', '.') }}</td>
                    </tr>
                @endif

                {{-- Totais --}}
                <tr class="totais">
                    <td colspan="3" class="text-right"><strong>Totais</strong></td>
                    <td class="text-right"><strong>{{ number_format($folha['total_proventos'], 2, ',', '.') }}</strong>
                    </td>
                    <td class="text-right"><strong>{{ number_format($folha['total_descontos'], 2, ',', '.') }}</strong>
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- Salário Líquido --}}
        <div class="salario-liquido">
            SALÁRIO LÍQUIDO &nbsp;&nbsp;&nbsp; R$ {{ number_format($folha['salario_liquido'], 2, ',', '.') }}
        </div>

        {{-- Bases de Cálculo --}}
        <div class="bases-calculo">
            <table>
                <tr>
                    <td class="label">Salário base</td>
                    <td class="label">Base INSS</td>
                    <td class="label">Base FGTS</td>
                    <td class="label">Valor FGTS</td>
                    <td class="label">Base IRRF</td>
                </tr>
                <tr>
                    <td>{{ number_format($folha['salario_base'], 2, ',', '.') }}</td>
                    <td>{{ number_format($folha['base_inss'], 2, ',', '.') }}</td>
                    <td>{{ number_format($folha['base_fgts'], 2, ',', '.') }}</td>
                    <td>{{ number_format($folha['fgts_valor'], 2, ',', '.') }}</td>
                    <td>{{ number_format($folha['base_irrf'], 2, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        {{-- Rodapé --}}
        <div class="footer">
            Documento gerado em {{ now()->format('d/m/Y H:i:s') }} - {{ config('app.name') }}
        </div>

    </div>
</body>

</html>
