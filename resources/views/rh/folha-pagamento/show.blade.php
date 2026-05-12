@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6 sm:flex sm:items-center sm:justify-between">
            <div class="flex items-center">
                <a href="{{ route('rh.folhas.index') }}" class="text-gray-400 hover:text-gray-600 mr-4">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold leading-tight text-gray-900">
                        Folha {{ $folha->competencia->format('F Y') }}
                    </h1>
                    <p class="mt-1 text-sm text-gray-600">
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        @if ($folha->status === 'fechada') bg-green-100 text-green-800
                        @elseif($folha->status === 'aberta') bg-yellow-100 text-yellow-800
                        @else bg-red-100 text-red-800 @endif">
                            {{ ucfirst($folha->status) }}
                        </span>
                    </p>
                </div>
            </div>
            <div class="mt-4 sm:mt-0 flex space-x-3">
                @if ($folha->status === 'aberta')
                    <form action="{{ route('rh.folhas.gerar-holerites', $folha) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Gerar Holerites
                        </button>
                    </form>

                    @if ($folha->holerites->count() > 0)
                        <form action="{{ route('rh.folhas.fechar', $folha) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                onclick="return confirm('Tem certeza que deseja fechar esta folha? Após fechada, não será possível fazer alterações.')"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Fechar Folha
                            </button>
                        </form>
                    @endif
                @elseif($folha->status === 'fechada')
                    <form action="{{ route('rh.folhas.reabrir', $folha) }}" method="POST" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" onclick="return confirm('Tem certeza que deseja reabrir esta folha?')"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" />
                            </svg>
                            Reabrir Folha
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Messages -->
        @if (session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-md">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-md">
                {{ session('error') }}
            </div>
        @endif

        <!-- Resumo -->
        <div class="bg-white overflow-hidden shadow rounded-lg mb-6">
            <div class="p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Resumo da Folha</h3>
                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Funcionários</dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $totais['funcionarios'] }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Salário Bruto</dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900">R$
                            {{ number_format($totais['salario_bruto'], 2, ',', '.') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Total Descontos</dt>
                        <dd class="mt-1 text-3xl font-semibold text-red-600">R$
                            {{ number_format($totais['inss'] + $totais['irrf'], 2, ',', '.') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Salário Líquido</dt>
                        <dd class="mt-1 text-3xl font-semibold text-green-600">R$
                            {{ number_format($totais['salario_liquido'], 2, ',', '.') }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Holerites -->
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Holerites</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Detalhes dos holerites de cada funcionário</p>
            </div>

            @if ($folha->holerites->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Funcionário</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Salário Bruto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    INSS</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    IRRF</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    VT</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Salário Líquido</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($folha->holerites as $holerite)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="h-10 w-10 flex-shrink-0">
                                                <div
                                                    class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                    <span class="text-sm font-medium text-gray-700">
                                                        {{ substr($holerite->funcionario->nome, 0, 2) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $holerite->funcionario->nome }}</div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $holerite->funcionario->cargo ?? 'N/A' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        R$ {{ number_format($holerite->salario_bruto, 2, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">
                                        R$ {{ number_format($holerite->inss_valor, 2, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">
                                        R$ {{ number_format($holerite->irrf_valor, 2, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">
                                        R$ {{ number_format($holerite->vt_valor, 2, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">
                                        R$ {{ number_format($holerite->salario_liquido, 2, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-6 py-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum holerite gerado</h3>
                    <p class="mt-1 text-sm text-gray-500">Clique em "Gerar Holerites" para processar a folha.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
