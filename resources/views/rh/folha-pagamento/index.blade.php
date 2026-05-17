{{-- resources/views/rh/folha-pagamento/index.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Folhas de Pagamento</h1>
                <p class="mt-1 text-sm text-gray-600">
                    Competência: <span
                        class="font-semibold">{{ \Carbon\Carbon::createFromFormat('Y-m', $competencia)->format('m/Y') }}</span>
                </p>
            </div>
            <a href="{{ route('rh.folha-pagamento.create') }}"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                + Nova Folha
            </a>
        </div>

        {{-- Filtros --}}
        <div class="bg-white rounded-lg shadow mb-6 p-4">
            <form action="{{ route('rh.folha-pagamento.index') }}" method="GET" class="flex gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium text-gray-700">📅 Competência</label>
                    <input type="month" name="competencia" value="{{ $competencia }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">📌 Status</label>
                    <select name="status"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Todos</option>
                        <option value="aberta" {{ $status === 'aberta' ? 'selected' : '' }}>Aberta</option>
                        <option value="fechada" {{ $status === 'fechada' ? 'selected' : '' }}>Fechada</option>
                    </select>
                </div>
                <div>
                    <button type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm">
                        🔍 Filtrar
                    </button>
                </div>
                <div>
                    <a href="{{ route('rh.folha-pagamento.resumo-geral', ['competencia' => $competencia]) }}"
                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm inline-flex items-center">
                        📊 Resumo Geral
                    </a>
                </div>
            </form>
        </div>

        {{-- Tabela de Folhas --}}
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                📍 Local
                            </th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                📄 Contratação
                            </th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                👤 Funcionário
                            </th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                💼 Função
                            </th>
                            <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                💰 Salário Líquido
                            </th>
                            <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                💵 Salário Base
                            </th>
                            <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                💳 Dia 20 Vale
                            </th>
                            <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                📉 INSS
                            </th>
                            <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                🎫 Vale Extra
                            </th>
                            <th
                                class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider text-nowrap">
                                ❌ Faltas
                            </th>
                            <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ❌ DSR Faltas
                            </th>
                            <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                🔄 Arred. Desc.
                            </th>
                            <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                🔄 Arred. Prov.
                            </th>
                            <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                🎁 Gratificação
                            </th>
                            <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ⏰ DSR Hora Extra
                            </th>
                            <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                👪 Sal. Família + HR Extra
                            </th>
                            <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                📅 5º Dia Útil
                            </th>
                            <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                📌 Status
                            </th>
                            <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ⚙️ Ações
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($folhas as $folha)
                            @php
                                // Busca valores dos lançamentos
                                $lancamentos = $folha->lancamentos;

                                $getValor = function ($tipo) use ($lancamentos) {
                                    return $lancamentos->where('tipo', $tipo)->sum('valor_total');
                                };

                                $salarioBase = $getValor('salario_base') ?: $folha->salario_base;
                                $salarioFamilia = $getValor('salario_familia');
                                $horaExtraNormal = $getValor('hora_extra_normal');
                                $horaExtraSabado = $getValor('hora_extra_sabado');
                                $horaExtraFeriado = $getValor('hora_extra_feriado');
                                $totalHorasExtras = $horaExtraNormal + $horaExtraSabado + $horaExtraFeriado;
                                $dsrHoraExtra = $getValor('dsr_hora_extra');
                                $gratificacao = $getValor('gratificacao');
                                $arredProvento = $getValor('arredondamento') > 0 ? $getValor('arredondamento') : 0;
                                $inss = $getValor('inss');
                                $valeDia20 = $getValor('vale_dia_20');
                                $valeExtra = $getValor('vale_extra');
                                $faltas = $getValor('falta');
                                $dsrFaltas = $getValor('dsr_falta');
                                $arredDesconto = $getValor('arredondamento') < 0 ? abs($getValor('arredondamento')) : 0;

                                $totalProventos =
                                    $salarioBase +
                                    $totalHorasExtras +
                                    $dsrHoraExtra +
                                    $salarioFamilia +
                                    $gratificacao +
                                    $arredProvento;
                                $totalDescontos =
                                    $inss + $valeDia20 + $valeExtra + $faltas + $dsrFaltas + $arredDesconto;
                                $salarioLiquido = $totalProventos - $totalDescontos;
                            @endphp
                            <tr class="hover:bg-gray-50 {{ $folha->status === 'fechada' ? 'bg-gray-50' : '' }}">
                                {{-- Local de Trabalho --}}
                                <td class="px-3 py-3 whitespace-nowrap">
                                    <span class="text-xs">{{ $folha->funcionario->local_trabalho ?? '-' }}</span>
                                </td>

                                {{-- Contratação --}}
                                <td class="px-3 py-3 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-700 uppercase">
                                        {{ $folha->funcionario->tipo_contratacao ?? 'CLT' }}
                                    </span>
                                </td>

                                {{-- Funcionário --}}
                                <td class="px-3 py-3 whitespace-nowrap">
                                    <a href="{{ route('rh.folha-pagamento.show', $folha->id) }}"
                                        class="text-indigo-600 hover:text-indigo-900 font-medium">
                                        {{ $folha->funcionario->nome_completo }}
                                    </a>
                                </td>

                                {{-- Função --}}
                                <td class="px-3 py-3 whitespace-nowrap text-xs text-gray-500">
                                    {{ $folha->funcionario->cargo?->titulo ?? '-' }}
                                </td>

                                {{-- Salário Líquido --}}
                                <td class="px-3 py-3 whitespace-nowrap text-right font-bold text-blue-700">
                                    R$ {{ number_format($salarioLiquido, 2, ',', '.') }}
                                </td>

                                {{-- Salário Base --}}
                                <td class="px-3 py-3 whitespace-nowrap text-right">
                                    R$ {{ number_format($salarioBase, 2, ',', '.') }}
                                </td>

                                {{-- Dia 20 Vale --}}
                                <td
                                    class="px-3 py-3 whitespace-nowrap text-right {{ $valeDia20 > 0 ? 'text-red-600' : 'text-gray-400' }}">
                                    {{ $valeDia20 > 0 ? 'R$ ' . number_format($valeDia20, 2, ',', '.') : '-' }}
                                </td>

                                {{-- INSS --}}
                                <td class="px-3 py-3 whitespace-nowrap text-right text-red-600">
                                    R$ {{ number_format($inss, 2, ',', '.') }}
                                </td>

                                {{-- Vale Extra --}}
                                <td
                                    class="px-3 py-3 whitespace-nowrap text-right {{ $valeExtra > 0 ? 'text-red-600' : 'text-gray-400' }}">
                                    {{ $valeExtra > 0 ? 'R$ ' . number_format($valeExtra, 2, ',', '.') : '-' }}
                                </td>

                                {{-- Faltas --}}
                                <td
                                    class="px-3 py-3 whitespace-nowrap text-right {{ $faltas > 0 ? 'text-red-600 font-medium' : 'text-gray-400' }}">
                                    {{ $faltas > 0 ? 'R$ ' . number_format($faltas, 2, ',', '.') : '-' }}
                                </td>

                                {{-- DSR Faltas --}}
                                <td
                                    class="px-3 py-3 whitespace-nowrap text-right {{ $dsrFaltas > 0 ? 'text-red-600' : 'text-gray-400' }}">
                                    {{ $dsrFaltas > 0 ? 'R$ ' . number_format($dsrFaltas, 2, ',', '.') : '-' }}
                                </td>

                                {{-- Arred. Desc. --}}
                                <td
                                    class="px-3 py-3 whitespace-nowrap text-right {{ $arredDesconto != 0 ? 'text-red-600' : 'text-gray-400' }}">
                                    {{ $arredDesconto != 0 ? 'R$ ' . number_format($arredDesconto, 2, ',', '.') : '-' }}
                                </td>

                                {{-- Arred. Prov. --}}
                                <td
                                    class="px-3 py-3 whitespace-nowrap text-right {{ $arredProvento != 0 ? 'text-green-600' : 'text-gray-400' }}">
                                    {{ $arredProvento != 0 ? 'R$ ' . number_format($arredProvento, 2, ',', '.') : '-' }}
                                </td>

                                {{-- Gratificação --}}
                                <td
                                    class="px-3 py-3 whitespace-nowrap text-right {{ $gratificacao > 0 ? 'text-green-600' : 'text-gray-400' }}">
                                    {{ $gratificacao > 0 ? 'R$ ' . number_format($gratificacao, 2, ',', '.') : '-' }}
                                </td>

                                {{-- DSR Hora Extra --}}
                                <td
                                    class="px-3 py-3 whitespace-nowrap text-right {{ $dsrHoraExtra > 0 ? 'text-indigo-600 font-medium' : 'text-gray-400' }}">
                                    {{ $dsrHoraExtra > 0 ? 'R$ ' . number_format($dsrHoraExtra, 2, ',', '.') : '-' }}
                                </td>

                                {{-- Sal. Família + HR Extra --}}
                                <td
                                    class="px-3 py-3 whitespace-nowrap text-right {{ $salarioFamilia + $totalHorasExtras > 0 ? 'text-green-600' : 'text-gray-400' }}">
                                    {{ $salarioFamilia + $totalHorasExtras > 0 ? 'R$ ' . number_format($salarioFamilia + $totalHorasExtras, 2, ',', '.') : '-' }}
                                </td>

                                {{-- 5º Dia Útil --}}
                                <td class="px-3 py-3 whitespace-nowrap text-center">
                                    <span class="text-xs px-2 py-1 bg-blue-50 text-blue-700 rounded-full">
                                        {{ $folha->quinto_dia_util ? \Carbon\Carbon::parse($folha->quinto_dia_util)->format('d/m') : '-' }}
                                    </span>
                                </td>

                                {{-- Status --}}
                                <td class="px-3 py-3 whitespace-nowrap text-center">
                                    <span
                                        class="px-2 py-1 text-xs rounded-full {{ $folha->status === 'aberta' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                        {{ ucfirst($folha->status) }}
                                    </span>
                                </td>

                                {{-- Ações --}}
                                <td class="px-3 py-3 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('rh.folha-pagamento.show', $folha->id) }}"
                                            class="text-blue-600 hover:text-blue-900" title="Visualizar">
                                            👁️
                                        </a>
                                        <a href="{{ route('rh.folha-pagamento.edit', $folha->id) }}"
                                            class="text-indigo-600 hover:text-indigo-900" title="Editar">
                                            ✏️
                                        </a>
                                        <a href="{{ route('rh.folha-pagamento.pdf', $folha->id) }}" target="_blank"
                                            class="text-red-600 hover:text-red-900" title="PDF">
                                            📄
                                        </a>
                                        <!-- perguntar se quer excluir -->

                                        <form action="{{ route('rh.folha-pagamento.destroy', $folha->id) }}" method="POST"
                                            class="text-red-600 hover:text-red-900">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Excluir" class="text-red-600 hover:text-red-900"
                                                onclick="return confirm('Confirma a exclusão desta folha?')">
                                                🗑
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="19" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <svg class="h-12 w-12 text-gray-300 mb-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <p class="text-lg font-medium">Nenhuma folha encontrada</p>
                                        <p class="text-sm mt-1">Crie uma nova folha para esta competência</p>
                                        <a href="{{ route('rh.folha-pagamento.create') }}"
                                            class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm">
                                            + Nova Folha
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                    {{-- Rodapé com Totais --}}
                    @if ($folhas->count() > 0)
                        <tfoot class="bg-gray-100 font-bold text-sm">
                            <tr>
                                <td colspan="4" class="px-3 py-3 text-right text-gray-700">
                                    TOTAL ({{ $totais->total_funcionarios ?? 0 }} funcionários)
                                </td>
                                <td class="px-3 py-3 text-right text-blue-800">
                                    R$ {{ number_format($totais->total_salario_liquido ?? 0, 2, ',', '.') }}
                                </td>
                                <td class="px-3 py-3 text-right">
                                    R$ {{ number_format($totais->total_salario_base ?? 0, 2, ',', '.') }}
                                </td>
                                <td class="px-3 py-3 text-right text-red-700">
                                    R$ {{ number_format($totais->total_vale_dia_20 ?? 0, 2, ',', '.') }}
                                </td>
                                <td class="px-3 py-3 text-right text-red-700 text-nowrap">
                                    R$ {{ number_format($totais->total_desconto_inss ?? 0, 2, ',', '.') }}
                                </td>
                                <td class="px-3 py-3 text-right text-red-700">
                                    R$ {{ number_format($totais->total_vale_extra ?? 0, 2, ',', '.') }}
                                </td>
                                <td class="px-3 py-3 text-right text-red-700 text-nowrap">
                                    R$ {{ number_format($totais->total_faltas ?? 0, 2, ',', '.') }}
                                </td>
                                <td class="px-3 py-3 text-right text-red-700">
                                    R$ {{ number_format($totais->total_dsr_faltas ?? 0, 2, ',', '.') }}
                                </td>
                                <td class="px-3 py-3 text-right text-red-700">
                                    R$ {{ number_format($totais->total_arred_desconto ?? 0, 2, ',', '.') }}
                                </td>
                                <td class="px-3 py-3 text-right text-green-700">
                                    R$ {{ number_format($totais->total_arred_provento ?? 0, 2, ',', '.') }}
                                </td>
                                <td class="px-3 py-3 text-right text-green-700">
                                    R$ {{ number_format($totais->total_gratificacao ?? 0, 2, ',', '.') }}
                                </td>
                                <td class="px-3 py-3 text-right text-indigo-700">
                                    R$ {{ number_format($totais->total_dsr_hora_extra ?? 0, 2, ',', '.') }}
                                </td>
                                <td class="px-3 py-3 text-right text-green-700">
                                    R$
                                    {{ number_format(($totais->total_sal_familia_hr_extra ?? 0) + ($totais->total_horas_extras ?? 0), 2, ',', '.') }}
                                </td>
                                <td class="px-3 py-3 text-center">
                                    {{ $totais->quinto_dia_util ? \Carbon\Carbon::parse($totais->quinto_dia_util)->format('d/m/Y') : '-' }}
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>

            {{-- Paginação --}}
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $folhas->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
@endsection
