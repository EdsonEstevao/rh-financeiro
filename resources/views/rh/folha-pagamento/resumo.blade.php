@extends('layouts.app')

@section('content')

    <div class="px-4 py-6 mx-auto max-w-7xl sm:px-6 lg:px-8">
        {{-- No topo da view resumo --}}
        <div class="flex items-center gap-3 mb-6">
            <form method="GET" class="flex items-center gap-3">
                <input type="month" name="competencia" value="{{ $competencia ?? now()->format('Y-m') }}"
                    class="text-sm border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500">
                <button type="submit" class="px-4 py-2 text-sm text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">
                    🔍 Filtrar
                </button>
            </form>
        </div>

        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">💰 Resumo da Folha de Pagamento</h1>
                <p class="mt-1 text-sm text-gray-500">
                    Competência: <span
                        class="font-semibold">{{ request('competencia', now()->translatedFormat('F/Y')) }}</span>
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('rh.folha-pagamento.index') }}"
                    class="inline-flex items-center px-4 py-2 text-sm text-gray-700 transition-colors bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    📋 Todas as Folhas
                </a>
                <a href="{{ route('rh.folha-pagamento.resumo-geral') }}"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white transition-colors bg-indigo-600 rounded-lg hover:bg-indigo-700">
                    📊 Resumo Geral
                </a>
            </div>
        </div>

        {{-- Cards de Resumo --}}
        <div class="grid grid-cols-1 gap-4 mb-8 md:grid-cols-2 lg:grid-cols-4">

            {{-- Total Funcionários --}}
            <div
                class="p-5 transition-shadow bg-white border border-l-4 border-gray-100 shadow-sm rounded-xl hover:shadow-md border-l-indigo-400">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">👥 Funcionários</p>
                        <p class="mt-1 text-3xl font-bold text-gray-900">{{ $resumo['total_funcionarios'] }}</p>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-xl">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Folha Bruta --}}
            <div
                class="p-5 transition-shadow bg-white border border-l-4 border-gray-100 shadow-sm rounded-xl hover:shadow-md border-l-green-400">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">📈 Folha Bruta</p>
                        <p class="mt-1 text-2xl font-bold text-green-600">
                            R$ {{ number_format($resumo['folha_bruta'], 2, ',', '.') }}
                        </p>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-green-100 rounded-xl">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="w-full bg-gray-100 rounded-full h-1.5">
                        <div class="bg-green-500 h-1.5 rounded-full" style="width: 100%"></div>
                    </div>
                </div>
            </div>

            {{-- Total Descontos --}}
            <div
                class="p-5 transition-shadow bg-white border border-l-4 border-gray-100 shadow-sm rounded-xl hover:shadow-md border-l-red-400">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">📉 Descontos</p>
                        <p class="mt-1 text-2xl font-bold text-red-600">
                            R$ {{ number_format($resumo['total_descontos'], 2, ',', '.') }}
                        </p>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-red-100 rounded-xl">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                        </svg>
                    </div>
                </div>
                <div class="mt-3">
                    @php
                        $percentualDescontos =
                            $resumo['folha_bruta'] > 0
                                ? round(($resumo['total_descontos'] / $resumo['folha_bruta']) * 100)
                                : 0;
                    @endphp
                    <div class="flex justify-between mb-1 text-xs text-gray-400">
                        <span>{{ $percentualDescontos }}% da folha bruta</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-1.5">
                        <div class="bg-red-500 h-1.5 rounded-full" style="width: {{ $percentualDescontos }}%"></div>
                    </div>
                </div>
            </div>

            {{-- Folha Líquida --}}
            <div
                class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow {{ $resumo['folha_liquida'] > 0 ? 'border-l-4 border-l-purple-400' : '' }}">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">💵 Folha Líquida</p>
                        <p class="mt-1 text-2xl font-bold text-purple-600">
                            R$ {{ number_format($resumo['folha_liquida'], 2, ',', '.') }}
                        </p>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-purple-100 rounded-xl">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
                @php
                    $mediaPorFuncionario =
                        $resumo['total_funcionarios'] > 0
                            ? $resumo['folha_liquida'] / $resumo['total_funcionarios']
                            : 0;
                @endphp
                <div class="mt-3 text-xs text-gray-400">
                    Média por funcionário: <span class="font-medium text-purple-600">R$
                        {{ number_format($mediaPorFuncionario, 2, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- Gráfico rápido de distribuição --}}
        <div class="grid grid-cols-1 gap-4 mb-8 lg:grid-cols-3">
            <div class="p-5 bg-white border border-gray-100 shadow-sm rounded-xl">
                <h4 class="mb-3 text-sm font-semibold text-gray-700">Distribuição Bruta vs Líquida</h4>
                @php
                    $alturaBruta = $resumo['folha_bruta'] > 0 ? 100 : 0;
                    $alturaLiquida =
                        $resumo['folha_bruta'] > 0
                            ? round(($resumo['folha_liquida'] / $resumo['folha_bruta']) * 100)
                            : 0;
                @endphp
                <div class="flex items-end h-32 gap-6">
                    <div class="flex flex-col items-center flex-1">
                        <span class="mb-1 text-xs font-bold text-green-600">R$
                            {{ number_format($resumo['folha_bruta'] / 1000, 1, ',', '.') }}k</span>
                        <div class="w-full bg-green-500 rounded-t-lg" style="height: {{ $alturaBruta }}%"></div>
                        <span class="mt-1 text-xs text-gray-400">Bruta</span>
                    </div>
                    <div class="flex flex-col items-center flex-1">
                        <span class="mb-1 text-xs font-bold text-purple-600">R$
                            {{ number_format($resumo['folha_liquida'] / 1000, 1, ',', '.') }}k</span>
                        <div class="w-full bg-purple-500 rounded-t-lg" style="height: {{ $alturaLiquida }}%"></div>
                        <span class="mt-1 text-xs text-gray-400">Líquida</span>
                    </div>
                </div>
            </div>

            <div class="p-5 bg-white border border-gray-100 shadow-sm rounded-xl">
                <h4 class="mb-3 text-sm font-semibold text-gray-700">Resumo Rápido</h4>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Total Funcionários</span>
                        <span class="font-semibold">{{ $resumo['total_funcionarios'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Média Salarial Bruta</span>
                        <span class="font-semibold text-green-600">R$
                            {{ number_format($resumo['total_funcionarios'] > 0 ? $resumo['folha_bruta'] / $resumo['total_funcionarios'] : 0, 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Média de Descontos</span>
                        <span class="font-semibold text-red-600">R$
                            {{ number_format($resumo['total_funcionarios'] > 0 ? $resumo['total_descontos'] / $resumo['total_funcionarios'] : 0, 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between pt-2 border-t">
                        <span class="font-medium text-gray-700">Custo Total Empresa</span>
                        <span class="font-semibold text-indigo-600">R$
                            {{ number_format($resumo['folha_bruta'] * 1.3, 2, ',', '.') }}</span>
                    </div>
                    <p class="text-xs text-gray-400">* Estimativa com 30% de encargos</p>
                </div>
            </div>

            <div class="p-5 bg-white border border-gray-100 shadow-sm rounded-xl">
                <h4 class="mb-3 text-sm font-semibold text-gray-700">Top 5 Maiores Salários</h4>
                @php
                    $topSalarios = $funcionarios->sortByDesc('salario_bruto')->take(5);
                @endphp
                <div class="space-y-2">
                    @foreach ($topSalarios as $func)
                        <div class="flex items-center justify-between text-sm">
                            <div class="flex items-center min-w-0 gap-2">
                                <div
                                    class="flex items-center justify-center flex-shrink-0 text-xs font-bold text-white rounded-full w-7 h-7 bg-gradient-to-br from-indigo-400 to-purple-500">
                                    {{ mb_strtoupper(mb_substr($func->nome_completo, 0, 1)) }}
                                </div>
                                <span class="text-gray-700 truncate">{{ $func->nome_completo }}</span>
                            </div>
                            <span class="flex-shrink-0 font-medium text-green-600">R$
                                {{ number_format($func->salario_bruto, 2, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Detalhamento por Departamento --}}
        <div class="overflow-hidden bg-white border border-gray-100 shadow-sm rounded-xl">
            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-purple-50">
                <h3 class="font-semibold text-gray-800">🏢 Detalhamento por Departamento</h3>
            </div>
            <div class="overflow-x-auto">
                {{-- @ php
                    $funcionariosPorDepartamento = $funcionarios->groupBy('departamento.nome');
                @ endphp --}}
                @php
                    $funcionariosPorDepartamento = $funcionarios->groupBy(function ($func) {
                        return $func->departamento->nome ?? 'Sem Departamento';
                    });
                @endphp

                @if ($funcionariosPorDepartamento->count() > 0)
                    <table class="min-w-full text-sm divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Departamento</th>
                                <th
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">
                                    Funcionários</th>
                                <th
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">
                                    Folha Bruta</th>
                                <th
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">
                                    Descontos</th>
                                <th
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">
                                    Folha Líquida</th>
                                <th
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">
                                    % do Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($funcionariosPorDepartamento as $departamento => $funcs)
                                @php
                                    $bruta = $funcs->sum('salario_bruto');
                                    $descontos = $funcs->sum('total_descontos');
                                    $liquida = $funcs->sum('salario_liquido');
                                    $percentual =
                                        $resumo['folha_bruta'] > 0
                                            ? round(($bruta / $resumo['folha_bruta']) * 100, 1)
                                            : 0;
                                @endphp
                                <tr class="transition-colors hover:bg-gray-50">
                                    <td class="px-6 py-3.5 font-medium text-gray-900">
                                        {{ $departamento ?? 'Sem Departamento' }}</td>
                                    <td class="px-6 py-3.5 text-center">
                                        <span
                                            class="px-2 py-0.5 bg-indigo-100 text-indigo-700 rounded-full text-xs font-medium">
                                            {{ $funcs->count() }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3.5 text-right text-green-600 font-medium">
                                        R$ {{ number_format($bruta, 2, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-3.5 text-right text-red-600">
                                        R$ {{ number_format($descontos, 2, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-3.5 text-right text-purple-600 font-semibold">
                                        R$ {{ number_format($liquida, 2, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-3.5 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <div class="w-20 bg-gray-100 rounded-full h-1.5">
                                                <div class="bg-indigo-500 h-1.5 rounded-full"
                                                    style="width: {{ $percentual }}%"></div>
                                            </div>
                                            <span class="text-xs text-gray-400">{{ $percentual }}%</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="font-semibold bg-gray-50">
                            <tr>
                                <td class="px-6 py-3.5 text-gray-700">TOTAL</td>
                                <td class="px-6 py-3.5 text-center text-indigo-700">{{ $resumo['total_funcionarios'] }}
                                </td>
                                <td class="px-6 py-3.5 text-right text-green-700">R$
                                    {{ number_format($resumo['folha_bruta'], 2, ',', '.') }}</td>
                                <td class="px-6 py-3.5 text-right text-red-700">R$
                                    {{ number_format($resumo['total_descontos'], 2, ',', '.') }}</td>
                                <td class="px-6 py-3.5 text-right text-purple-700">R$
                                    {{ number_format($resumo['folha_liquida'], 2, ',', '.') }}</td>
                                <td class="px-6 py-3.5 text-right text-gray-700">100%</td>
                            </tr>
                        </tfoot>
                    </table>
                @else
                    <div class="p-12 text-center text-gray-400">
                        <p class="mb-1 text-lg">📭</p>
                        <p>Nenhum dado disponível para esta competência.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
