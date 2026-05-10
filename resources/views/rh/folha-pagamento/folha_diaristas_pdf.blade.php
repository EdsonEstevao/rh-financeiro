<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Relatório de Diaristas - Folha</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 6px;
            text-align: left
        }
    </style>
</head>

<body>
    <h2>Folha de Pagamento - Diaristas</h2>
    <p>Período: {{ \Carbon\Carbon::parse($inicio)->format('d/m/Y') }} a
        {{ \Carbon\Carbon::parse($fim)->format('d/m/Y') }}</p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Funcionário</th>
                <th>Dias Trabalhados</th>
                <th>Valor Total (R$)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($relatorio as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item['nome'] }}</td>
                    <td>{{ $item['dias_trabalhados'] }}</td>
                    <td>{{ number_format($item['valor_total'], 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
