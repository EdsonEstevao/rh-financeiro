{{-- Na view show.blade.php, após a tabela de totais --}}

{{-- Lançamentos Detalhados --}}
@extends('layouts.app')
@dd('teste')
@section('content')
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6 sm:flex sm:items-center sm:justify-between">
            <div class="flex items-center">
                <a href="{{ route('rh.folha-pagamento.index') }}" class="text-gray-400 hover:text-gray-600 mr-4">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold leading-tight text-gray-900">
                        Folha {{ ucfirst($folhaPagamento->competencia->translatedFormat('F')) }}
                        {{ $folhaPagamento->competencia->translatedFormat(' \d\e Y') }}
                    </h1>
                    @php
                        $statusColor = [
                            'fechada' => 'bg-green-100 text-green-800',
                            'aberta' => 'bg-yellow-100 text-yellow-800',
                            'default' => 'bg-red-100 text-red-800',
                        ];
                    @endphp
                    <p class="mt-1 text-sm text-gray-600">
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColor[$folhaPagamento->status] }}">
                            {{ ucfirst($folhaPagamento->status) }}
                        </span>
                    </p>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow mt-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">📋 Lançamentos Detalhados</h2>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- Proventos --}}
                        <div>
                            <h3 class="font-semibold text-green-700 mb-3">💵 Proventos</h3>
                            <table class="min-w-full text-sm">
                                <thead class="bg-green-50">
                                    <tr>
                                        <th class="px-3 py-2 text-left">Descrição</th>
                                        <th class="px-3 py-2 text-right">Qtd</th>
                                        <th class="px-3 py-2 text-right">Valor</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y">
                                    @foreach ($folhaPagamento->lancamentos->where('categoria', 'provento') as $lancamento)
                                        <tr>
                                            <td class="px-3 py-2">{{ $lancamento->descricao }}</td>
                                            <td class="px-3 py-2 text-right">
                                                @if ($lancamento->quantidade > 1)
                                                    @if ($lancamento->tipo == 'salario_familia')
                                                        {{ (int) $lancamento->quantidade }}
                                                    @else
                                                        {{ number_format($lancamento->quantidade, 1, ',', '.') }}
                                                    @endif


                                                    @if (in_array($lancamento->tipo, ['hora_extra_normal', 'hora_extra_sabado', 'hora_extra_feriado']))
                                                        h
                                                    @endif
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="px-3 py-2 text-right font-medium">
                                                R$ {{ number_format($lancamento->valor_total, 2, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-green-50 font-bold">
                                    <tr>
                                        <td class="px-3 py-2">Total Proventos</td>
                                        <td></td>
                                        <td class="px-3 py-2 text-right text-green-800">
                                            R$
                                            {{ number_format($folhaPagamento->lancamentos->where('categoria', 'provento')->sum('valor_total'), 2, ',', '.') }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        {{-- Descontos --}}
                        <div>
                            <h3 class="font-semibold text-red-700 mb-3">📉 Descontos</h3>
                            <table class="min-w-full text-sm">
                                <thead class="bg-red-50">
                                    <tr>
                                        <th class="px-3 py-2 text-left">Descrição</th>
                                        <th class="px-3 py-2 text-right">Valor</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y">
                                    @foreach ($folhaPagamento->lancamentos->where('categoria', 'desconto') as $lancamento)
                                        <tr>
                                            <td class="px-3 py-2">{{ $lancamento->descricao }}</td>
                                            <td class="px-3 py-2 text-right font-medium text-red-700">
                                                - R$ {{ number_format($lancamento->valor_total, 2, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-red-50 font-bold">
                                    <tr>
                                        <td class="px-3 py-2">Total Descontos</td>
                                        <td class="px-3 py-2 text-right text-red-800">
                                            - R$
                                            {{ number_format($folhaPagamento->lancamentos->where('categoria', 'desconto')->sum('valor_total'), 2, ',', '.') }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
