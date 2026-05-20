{{-- resources/views/rh/dashboard.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="px-4 py-6 mx-auto max-w-7xl sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">📊 Dashboard RH</h1>
                <p class="mt-1 text-sm text-gray-500">
                    Visão geral • <span class="font-medium">{{ now()->format('d/m/Y') }}</span>
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('rh.funcionarios.index') }}"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white transition-colors bg-indigo-600 rounded-lg hover:bg-indigo-700">
                    👥 Todos Funcionários
                </a>
                <a href="{{ route('rh.ferias.dashboard') }}"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 transition-colors bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    📅 Dashboard Férias
                </a>
            </div>
        </div>

        {{-- Cards Principais --}}
        <div class="grid grid-cols-1 gap-4 mb-8 md:grid-cols-2 lg:grid-cols-4">

            {{-- Total Funcionários --}}
            <div class="p-5 transition-shadow bg-white border border-gray-100 shadow-sm rounded-xl hover:shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Funcionários</p>
                        <p class="mt-1 text-3xl font-bold text-gray-900">{{ $totalFuncionarios }}</p>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-indigo-100 rounded-xl">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
                <div class="flex items-center gap-2 mt-3 text-xs text-gray-400">
                    <span class="w-2 h-2 bg-green-400 rounded-full"></span>
                    {{ $funcionariosAtivos }} ativos
                </div>
            </div>

            {{-- Férias Vencidas --}}
            <a href="{{ route('rh.ferias.dashboard') }}#vencidas" class="block">
                <div
                    class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow {{ $feriasVencidas > 0 ? 'border-l-4 border-l-red-400' : '' }}">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Férias Vencidas</p>
                            <p class="text-3xl font-bold {{ $feriasVencidas > 0 ? 'text-red-600' : 'text-gray-900' }} mt-1">
                                {{ $feriasVencidas }}</p>
                        </div>
                        <div
                            class="w-12 h-12 {{ $feriasVencidas > 0 ? 'bg-red-100' : 'bg-gray-100' }} rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 {{ $feriasVencidas > 0 ? 'text-red-600' : 'text-gray-400' }}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                    </div>
                    @if ($feriasVencidas > 0)
                        <div class="mt-3 text-xs font-medium text-red-500">⚠️ Pagamento em dobro</div>
                    @endif
                </div>
            </a>

            {{-- Férias em Andamento --}}
            <div class="p-5 transition-shadow bg-white border border-gray-100 shadow-sm rounded-xl hover:shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Em Férias</p>
                        <p class="mt-1 text-3xl font-bold text-green-600">{{ $feriasEmAndamento }}</p>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-green-100 rounded-xl">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </div>
                <div class="mt-3 text-xs text-gray-400">
                    {{ now()->format('d/m/Y') }} • Hoje
                </div>
            </div>

            {{-- Folha de Pagamento --}}
            <a href="{{ route('rh.folha-pagamento.index') }}" class="block">
                <div class="p-5 transition-shadow bg-white border border-gray-100 shadow-sm rounded-xl hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Folha do Mês</p>
                            <p class="mt-1 text-3xl font-bold text-gray-900">{{ $folhaMesAtual }}</p>
                        </div>
                        <div class="flex items-center justify-center w-12 h-12 bg-purple-100 rounded-xl">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3 text-xs text-gray-400">
                        {{ ucfirst(now()->translatedFormat('F/Y')) }}
                    </div>
                </div>
            </a>
        </div>

        {{-- Segunda linha --}}
        <div class="grid grid-cols-1 gap-6 mb-8 lg:grid-cols-3">

            {{-- Próximas Férias --}}
            <div class="bg-white border border-gray-100 shadow-sm lg:col-span-2 rounded-xl">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800">📅 Próximas Férias Agendadas</h3>
                    <a href="{{ route('rh.ferias.dashboard') }}"
                        class="text-xs font-medium text-indigo-600 hover:text-indigo-800">
                        Ver todas →
                    </a>
                </div>
                <div class="overflow-x-auto">
                    @if ($proximasFerias->count() > 0)
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-xs font-medium text-left text-gray-500">Funcionário</th>
                                    <th class="px-4 py-2 text-xs font-medium text-left text-gray-500">Cargo</th>
                                    <th class="px-4 py-2 text-xs font-medium text-left text-gray-500">Início</th>
                                    <th class="px-4 py-2 text-xs font-medium text-left text-gray-500">Fim</th>
                                    <th class="px-4 py-2 text-xs font-medium text-left text-gray-500">Dias</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach ($proximasFerias as $ferias)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2.5 font-medium">{{ $ferias->funcionario->nome_completo }}</td>
                                        <td class="px-4 py-2.5 text-gray-500">
                                            {{ $ferias->funcionario->cargo?->titulo ?? 'N/A' }}</td>
                                        <td class="px-4 py-2.5">{{ $ferias->data_inicio->format('d/m/Y') }}</td>
                                        <td class="px-4 py-2.5">{{ $ferias->data_fim->format('d/m/Y') }}</td>
                                        <td class="px-4 py-2.5">
                                            <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full text-xs">
                                                {{ $ferias->data_inicio->diffInDays($ferias->data_fim) + 1 }} dias
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="p-6 text-center text-gray-400">
                            Nenhuma férias agendada para os próximos dias.
                        </div>
                    @endif
                </div>
            </div>

            {{-- Aniversariantes do Mês --}}
            <div class="bg-white border border-gray-100 shadow-sm rounded-xl">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800">🎂 Aniversariantes de {{ now()->translatedFormat('F') }}</h3>
                </div>
                <div class="p-4">
                    @if ($aniversariantes->count() > 0)
                        <div class="space-y-3">
                            @foreach ($aniversariantes as $funcionario)
                                <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50">
                                    <div
                                        class="flex items-center justify-center flex-shrink-0 w-10 h-10 text-sm font-bold text-white rounded-full bg-gradient-to-br from-pink-400 to-purple-500">
                                        {{ mb_strtoupper(mb_substr($funcionario->nome_completo, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">
                                            {{ $funcionario->nome_completo }}</p>
                                        <p class="text-xs text-gray-400">{{ $funcionario->cargo?->titulo ?? 'N/A' }}</p>
                                    </div>
                                    <div class="ml-auto text-sm font-medium text-pink-600">
                                        {{ \Carbon\Carbon::parse($funcionario->data_nascimento)->format('d/m') }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="py-6 text-center text-gray-400">
                            Nenhum aniversariante este mês.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Terceira linha --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

            {{-- Últimas Admissões --}}
            <div class="bg-white border border-gray-100 shadow-sm rounded-xl">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800">🆕 Últimas Admissões</h3>
                    <a href="{{ route('rh.funcionarios.index') }}"
                        class="text-xs font-medium text-indigo-600 hover:text-indigo-800">
                        Ver todos →
                    </a>
                </div>
                <div class="overflow-x-auto">
                    @if ($ultimasAdmissoes->count() > 0)
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-xs font-medium text-left text-gray-500">Funcionário</th>
                                    <th class="px-4 py-2 text-xs font-medium text-left text-gray-500">Cargo</th>
                                    <th class="px-4 py-2 text-xs font-medium text-left text-gray-500">Admissão</th>
                                    <th class="px-4 py-2 text-xs font-medium text-left text-gray-500">Tempo</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach ($ultimasAdmissoes as $funcionario)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2.5 font-medium">{{ $funcionario->nome_completo }}</td>
                                        <td class="px-4 py-2.5 text-gray-500">{{ $funcionario->cargo?->titulo ?? 'N/A' }}
                                        </td>
                                        <td class="px-4 py-2.5">
                                            {{ $funcionario->contrato?->data_admissao
                                                ? \Carbon\Carbon::parse($funcionario->contrato->data_admissao)->format('d/m/Y')
                                                : 'N/A' }}
                                        </td>
                                        <td class="px-4 py-2.5">
                                            @if ($funcionario->contrato?->data_admissao)
                                                <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs">
                                                    {{ \Carbon\Carbon::parse($funcionario->contrato->data_admissao)->diffForHumans() }}
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="p-6 text-center text-gray-400">
                            Nenhuma admissão recente.
                        </div>
                    @endif
                </div>
            </div>

            {{-- Distribuição por Departamento --}}
            <div class="bg-white border border-gray-100 shadow-sm rounded-xl">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800">🏢 Funcionários por Departamento</h3>
                </div>
                <div class="p-4">
                    @if ($departamentos->count() > 0)
                        <div class="space-y-3">
                            @foreach ($departamentos as $dep)
                                @php
                                    $total = $dep->funcionarios_count;
                                    $porcentagem =
                                        $totalFuncionarios > 0 ? round(($total / $totalFuncionarios) * 100) : 0;
                                    $cores = [
                                        'bg-indigo-500',
                                        'bg-blue-500',
                                        'bg-green-500',
                                        'bg-yellow-500',
                                        'bg-purple-500',
                                        'bg-pink-500',
                                    ];
                                    $cor = $cores[$loop->index % count($cores)];
                                @endphp
                                <div>
                                    <div class="flex justify-between mb-1 text-sm">
                                        <span class="font-medium text-gray-700">{{ $dep->nome }}</span>
                                        <span class="text-gray-400">{{ $total }} ({{ $porcentagem }}%)</span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-2.5">
                                        <div class="{{ $cor }} h-2.5 rounded-full transition-all duration-500"
                                            style="width: {{ max($porcentagem, 2) }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="py-6 text-center text-gray-400">
                            Nenhum departamento cadastrado.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
