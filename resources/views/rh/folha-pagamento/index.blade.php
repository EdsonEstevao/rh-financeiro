<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Folha de Pagamento') }}
            </h2>
            <div class="space-x-2">
                <a href="{{ route('rh.folha-pagamento.resumo') }}"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Resumo da Folha
                </a>
                <button onclick="window.print()"
                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Imprimir
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            {{-- Filtros --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Departamento</label>
                            <select name="departamento_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="">Todos</option>
                                @foreach ($departamentos as $dept)
                                    <option value="{{ $dept->id }}"
                                        {{ request('departamento_id') == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Cargo</label>
                            <select name="cargo_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="">Todos</option>
                                @foreach ($cargos as $cargo)
                                    <option value="{{ $cargo->id }}"
                                        {{ request('cargo_id') == $cargo->id ? 'selected' : '' }}>
                                        {{ $cargo->titulo }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Local de Trabalho</label>
                            <input type="text" name="local_trabalho" value="{{ request('local_trabalho') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                placeholder="Digite o local...">
                        </div>

                        <div class="flex items-end space-x-2">
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Filtrar
                            </button>
                            <a href="{{ route('rh.folha-pagamento.index') }}"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Limpar
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Totalizadores --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600">{{ $totalizadores['total_funcionarios'] }}
                            </div>
                            <div class="text-sm text-gray-600">Funcionários</div>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-green-600">
                                {{ number_format($totalizadores['soma_bruto'], 2, ',', '.') }}</div>
                            <div class="text-sm text-gray-600">Folha Bruta (R$)</div>
                        </div>
                        <div class="bg-red-50 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-red-600">
                                {{ number_format($totalizadores['soma_descontos'], 2, ',', '.') }}</div>
                            <div class="text-sm text-gray-600">Total Descontos (R$)</div>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-purple-600">
                                {{ number_format($totalizadores['soma_liquido'], 2, ',', '.') }}</div>
                            <div class="text-sm text-gray-600">Folha Líquida (R$)</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabela Principal --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-2 py-3 text-left font-medium text-gray-500 uppercase tracking-wider border">
                                    Local de Trabalho</th>
                                <th
                                    class="px-2 py-3 text-left font-medium text-gray-500 uppercase tracking-wider border">
                                    Contratação</th>
                                <th
                                    class="px-2 py-3 text-left font-medium text-gray-500 uppercase tracking-wider border">
                                    Funcionários</th>
                                <th
                                    class="px-2 py-3 text-left font-medium text-gray-500 uppercase tracking-wider border">
                                    Função</th>
                                <th
                                    class="px-2 py-3 text-left font-medium text-gray-500 uppercase tracking-wider border">
                                    Salário Real</th>
                                <th
                                    class="px-2 py-3 text-left font-medium text-gray-500 uppercase tracking-wider border">
                                    Salário</th>
                                <th
                                    class="px-2 py-3 text-left font-medium text-gray-500 uppercase tracking-wider border">
                                    DIA 20 VALE</th>
                                <th
                                    class="px-2 py-3 text-left font-medium text-gray-500 uppercase tracking-wider border">
                                    Desconto INSS 8%</th>
                                <th
                                    class="px-2 py-3 text-left font-medium text-gray-500 uppercase tracking-wider border">
                                    Vale Extra</th>
                                <th
                                    class="px-2 py-3 text-left font-medium text-gray-500 uppercase tracking-wider border">
                                    Faltas</th>
                                <th
                                    class="px-2 py-3 text-left font-medium text-gray-500 uppercase tracking-wider border">
                                    D.S.R</th>
                                <th
                                    class="px-2 py-3 text-left font-medium text-gray-500 uppercase tracking-wider border">
                                    Arred. Desc. Faltas</th>
                                <th
                                    class="px-2 py-3 text-left font-medium text-gray-500 uppercase tracking-wider border">
                                    Arred. Folha</th>
                                <th
                                    class="px-2 py-3 text-left font-medium text-gray-500 uppercase tracking-wider border">
                                    Gratificação/Provento</th>
                                <th
                                    class="px-2 py-3 text-left font-medium text-gray-500 uppercase tracking-wider border">
                                    DSR Hora Feriado</th>
                                <th
                                    class="px-2 py-3 text-left font-medium text-gray-500 uppercase tracking-wider border">
                                    Salário Família Extra</th>
                                <th
                                    class="px-2 py-3 text-left font-medium text-gray-500 uppercase tracking-wider border">
                                    + Hr Extra</th>
                                <th
                                    class="px-2 py-3 text-left font-medium text-gray-500 uppercase tracking-wider border">
                                    6º Dia Útil do Mês</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($funcionarios as $funcionario)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-2 py-2 border text-xs">
                                        {{ $funcionario->local_trabalho ?? '-' }}
                                    </td>
                                    <td class="px-2 py-2 border text-xs">
                                        <span
                                            class="px-2 py-1 text-xs rounded-full
                                            {{ $funcionario->tipo_contratacao === 'clt' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                            {{ strtoupper($funcionario->tipo_contratacao) }}
                                        </span>
                                    </td>
                                    <td class="px-2 py-2 border text-xs">
                                        <div class="font-medium">{{ $funcionario->nome_completo }}</div>
                                        <div class="text-gray-500">{{ $funcionario->departamento->nome ?? '' }}</div>
                                    </td>
                                    <td class="px-2 py-2 border text-xs">
                                        {{ $funcionario->cargo->titulo ?? '-' }}
                                    </td>
                                    <td class="px-2 py-2 border text-xs font-semibold">
                                        {{ $funcionario->formatarMoeda($funcionario->salario_bruto) }}
                                    </td>
                                    <td class="px-2 py-2 border text-xs">
                                        {{ $funcionario->formatarMoeda($funcionario->salario_base) }}
                                    </td>
                                    <td class="px-2 py-2 border text-xs">
                                        {{ $funcionario->formatarMoeda($funcionario->valor_vale_alimentacao) }}
                                    </td>
                                    <td class="px-2 py-2 border text-xs text-red-600">
                                        {{ $funcionario->formatarMoeda($funcionario->desconto_inss_8_porcento) }}
                                    </td>
                                    <td class="px-2 py-2 border text-xs">
                                        {{ $funcionario->formatarMoeda($funcionario->vale_extra) }}
                                    </td>
                                    <td
                                        class="px-2 py-2 border text-xs {{ $funcionario->faltas > 0 ? 'text-red-600' : '' }}">
                                        {{ $funcionario->faltas }}
                                    </td>
                                    <td class="px-2 py-2 border text-xs text-red-600">
                                        {{ $funcionario->formatarMoeda($funcionario->dsr_faltas) }}
                                    </td>
                                    <td class="px-2 py-2 border text-xs text-red-600">
                                        {{ $funcionario->formatarMoeda($funcionario->desconto_faltas) }}
                                    </td>
                                    <td class="px-2 py-2 border text-xs font-semibold text-green-600">
                                        {{ $funcionario->formatarMoeda($funcionario->salario_liquido) }}
                                    </td>
                                    <td class="px-2 py-2 border text-xs text-green-600">
                                        {{ $funcionario->formatarMoeda($funcionario->gratificacao_provento) }}
                                    </td>
                                    <td class="px-2 py-2 border text-xs text-green-600">
                                        {{ $funcionario->formatarMoeda($funcionario->dsr_hora_extra) }}
                                    </td>
                                    <td class="px-2 py-2 border text-xs text-green-600">
                                        {{ $funcionario->formatarMoeda($funcionario->salario_familia) }}
                                    </td>
                                    <td class="px-2 py-2 border text-xs text-green-600">
                                        {{ $funcionario->formatarMoeda($funcionario->hora_extra) }}
                                    </td>
                                    <td class="px-2 py-2 border text-xs text-center">
                                        <span
                                            class="px-2 py-1 text-xs rounded-full
                                            {{ $funcionario->sexto_dia_util_mes ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $funcionario->sexto_dia_util_status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="18" class="px-6 py-4 text-center text-gray-500">
                                        Nenhum funcionário encontrado.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Paginação --}}
                @if ($funcionarios->hasPages())
                    <div class="px-6 py-3 border-t">
                        {{ $funcionarios->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- CSS para impressão --}}
    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            table {
                font-size: 10px !important;
            }

            th,
            td {
                padding: 2px !important;
            }
        }
    </style>
</x-app-layout>
