{{-- resources/views/dashboard.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="px-4 py-6 mx-auto max-w-7xl sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">👋 Olá, {{ auth()->user()->name }}!</h1>
            <p class="mt-1 text-sm text-gray-500">
                Bem-vindo ao sistema • <span class="font-medium">{{ now()->translatedFormat('l, d \d\e F \d\e Y') }}</span>
            </p>
        </div>

        {{-- Cards de Resumo Geral --}}
        <div class="grid grid-cols-1 gap-4 mb-8 md:grid-cols-2 lg:grid-cols-4">

            {{-- Funcionários Ativos --}}
            <a href="{{ route('rh.funcionarios.index') }}" class="block group">
                <div
                    class="p-5 transition-all bg-white border border-gray-100 shadow-sm rounded-xl hover:shadow-md group-hover:border-indigo-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Funcionários Ativos</p>
                            <p class="mt-1 text-3xl font-bold text-gray-900">{{ $totalFuncionarios }}</p>
                        </div>
                        <div
                            class="flex items-center justify-center w-12 h-12 transition-colors bg-indigo-100 rounded-xl group-hover:bg-indigo-200">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 mt-3">
                        <span class="flex items-center gap-1 text-xs text-gray-400">
                            <span class="w-1.5 h-1.5 bg-green-400 rounded-full"></span>
                            {{ $totalAtivos }} ativos
                        </span>
                    </div>
                </div>
            </a>

            {{-- Férias em Andamento --}}
            <a href="{{ route('rh.ferias.dashboard') }}" class="block group">
                <div
                    class="p-5 transition-all bg-white border border-gray-100 shadow-sm rounded-xl hover:shadow-md group-hover:border-green-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Em Férias Hoje</p>
                            <p class="mt-1 text-3xl font-bold text-green-600">{{ $feriasHoje }}</p>
                        </div>
                        <div
                            class="flex items-center justify-center w-12 h-12 transition-colors bg-green-100 rounded-xl group-hover:bg-green-200">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3 text-xs text-gray-400">
                        {{ now()->format('d/m/Y') }} • Hoje
                    </div>
                </div>
            </a>

            {{-- Folha de Pagamento --}}
            <a href="{{ route('rh.folha-pagamento.index') }}" class="block group">
                <div
                    class="p-5 transition-all bg-white border border-gray-100 shadow-sm rounded-xl hover:shadow-md group-hover:border-purple-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Folha {{ now()->translatedFormat('F/Y') }}</p>
                            <p class="mt-1 text-3xl font-bold text-purple-600">{{ $folhaMesCount }}</p>
                        </div>
                        <div
                            class="flex items-center justify-center w-12 h-12 transition-colors bg-purple-100 rounded-xl group-hover:bg-purple-200">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 mt-3">
                        <span
                            class="px-2 py-0.5 text-xs rounded-full 
                        {{ $folhaMesCount > 0 ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $folhaMesCount > 0 ? 'Gerada' : 'Pendente' }}
                        </span>
                    </div>
                </div>
            </a>

            {{-- Alertas --}}
            {{-- <a href="{{ route('rh.ferias.dashboard') }}#vencidas" class="block group">
                <div
                    class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-all group-hover:border-red-200 {{ $alertasFerias > 0 ? 'border-l-4 border-l-red-400' : '' }}">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Alertas de Férias</p>
                            <p class="text-3xl font-bold {{ $alertasFerias > 0 ? 'text-red-600' : 'text-gray-900' }} mt-1">
                                {{ $alertasFerias }}</p>
                        </div>
                        <div
                            class="w-12 h-12 {{ $alertasFerias > 0 ? 'bg-red-100' : 'bg-gray-100' }} rounded-xl flex items-center justify-center group-hover:bg-red-200 transition-colors">
                            <svg class="w-6 h-6 {{ $alertasFerias > 0 ? 'text-red-600' : 'text-gray-400' }}" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </div>
                    </div>
                    @if ($alertasFerias > 0)
                        <div class="mt-3 text-xs font-medium text-red-500">⚠️ Necessita atenção</div>
                    @endif
                </div>
            </a> --}}
            {{-- Substitua o card "Alertas de Férias" por este --}}
            <a href="{{ route('rh.ferias.index') }}#ferias-direito" class="block group">
                <div
                    class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-all group-hover:border-amber-200 
        {{ $feriasDireito->where('status_alerta', 'disponivel')->count() > 0 ? 'border-l-4 border-l-amber-400' : '' }}">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Direito a Férias</p>
                            <p
                                class="text-3xl font-bold {{ $feriasDireito->count() > 0 ? 'text-amber-600' : 'text-gray-900' }} mt-1">
                                {{ $feriasDireito->count() }}
                            </p>
                        </div>
                        <div
                            class="w-12 h-12 {{ $feriasDireito->count() > 0 ? 'bg-amber-100' : 'bg-gray-100' }} rounded-xl flex items-center justify-center group-hover:bg-amber-200 transition-colors">
                            <svg class="w-6 h-6 {{ $feriasDireito->count() > 0 ? 'text-amber-600' : 'text-gray-400' }}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </div>
                    </div>
                    @php
                        $disponiveis = $feriasDireito->where('status_alerta', 'disponivel')->count();
                        $urgentes = $feriasDireito->where('status_alerta', 'urgente')->count();
                    @endphp
                    <div class="mt-3 space-y-1">
                        @if ($disponiveis > 0)
                            <div class="text-xs font-medium text-green-600">✅ {{ $disponiveis }} disponível(is)</div>
                        @endif
                        @if ($urgentes > 0)
                            <div class="text-xs font-medium text-red-500">⚠️ {{ $urgentes }} urgente(s)</div>
                        @endif
                    </div>
                </div>
            </a>
        </div>

        {{-- Conteúdo Principal --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

            {{-- Coluna Esquerda (2/3) --}}
            <div class="space-y-6 lg:col-span-2">

                {{-- Próximas Férias --}}
                <div class="bg-white border border-gray-100 shadow-sm rounded-xl">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                        <h3 class="font-semibold text-gray-800">📅 Próximas Férias (30 dias)</h3>
                        <a href="{{ route('rh.ferias.dashboard') }}"
                            class="text-xs font-medium text-indigo-600 hover:text-indigo-800">
                            Ver todas →
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        {{-- Adicione na coluna esquerda, antes das Próximas Férias --}}

                        {{-- 🔔 Funcionários com Direito a Férias --}}
                        @if ($feriasDireito->count() > 0)
                            <div class="overflow-hidden bg-white border border-gray-100 shadow-sm rounded-xl">
                                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-amber-50 to-orange-50">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <span class="text-xl">🔔</span>
                                            <h3 class="font-semibold text-gray-800">Funcionários com Direito a Férias</h3>
                                        </div>
                                        <span
                                            class="px-3 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-700">
                                            {{ $feriasDireito->count() }} funcionário(s)
                                        </span>
                                    </div>
                                    <p class="mt-1 ml-8 text-xs text-gray-500">
                                        Período aquisitivo completado ou próximo de completar
                                    </p>
                                </div>

                                <div class="overflow-x-auto">
                                    <table class="min-w-full text-sm">
                                        <thead class="bg-amber-50/50">
                                            <tr>
                                                <th
                                                    class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 uppercase">
                                                    Funcionário</th>
                                                <th
                                                    class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 uppercase">
                                                    Cargo</th>
                                                <th
                                                    class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 uppercase">
                                                    Período Aquisitivo</th>
                                                <th
                                                    class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 uppercase">
                                                    Status</th>
                                                <th
                                                    class="px-4 py-2.5 text-center text-xs font-medium text-gray-500 uppercase">
                                                    Ação</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y">
                                            @foreach ($feriasDireito as $funcionario)
                                                @php
                                                    $fimPeriodo = \Carbon\Carbon::parse(
                                                        $funcionario->periodo_aquisitivo_fim,
                                                    );
                                                    $inicioPeriodo = \Carbon\Carbon::parse(
                                                        $funcionario->periodo_aquisitivo_inicio,
                                                    );
                                                    $diasRestantes = $funcionario->dias_para_vencer;
                                                @endphp
                                                <tr
                                                    class="hover:bg-gray-50 transition-colors
                            @if ($funcionario->status_alerta === 'urgente') bg-red-50/30
                            @elseif($funcionario->status_alerta === 'disponivel') bg-green-50/30 @endif">
                                                    <td class="px-4 py-3">
                                                        <div class="flex items-center gap-2">
                                                            <div
                                                                class="flex items-center justify-center flex-shrink-0 w-8 h-8 text-xs font-bold text-white rounded-full bg-gradient-to-br from-amber-400 to-orange-500">
                                                                {{ mb_strtoupper(mb_substr($funcionario->nome_completo, 0, 1)) }}
                                                            </div>
                                                            <span
                                                                class="font-medium text-gray-900">{{ $funcionario->nome_completo }}</span>
                                                        </div>
                                                    </td>
                                                    <td class="px-4 py-3 text-gray-500">
                                                        {{ $funcionario->cargo?->titulo ?? 'N/A' }}</td>
                                                    <td class="px-4 py-3">
                                                        <div class="text-xs">
                                                            <p class="text-gray-600">
                                                                {{ $inicioPeriodo->format('d/m/Y') }} →
                                                                {{ $fimPeriodo->format('d/m/Y') }}
                                                            </p>
                                                            @if ($diasRestantes > 0)
                                                                <p class="text-gray-400 mt-0.5">
                                                                    Completa em {{ $diasRestantes }} dias
                                                                </p>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        @if ($diasRestantes <= 0)
                                                            {{-- Já completou --}}
                                                            <span
                                                                class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium text-green-700 bg-green-100 rounded-full">
                                                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                                                Disponível
                                                            </span>
                                                        @elseif($diasRestantes <= 15)
                                                            {{-- Urgente --}}
                                                            <span
                                                                class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium text-red-700 bg-red-100 rounded-full">
                                                                <span
                                                                    class="w-1.5 h-1.5 bg-red-500 rounded-full animate-pulse"></span>
                                                                Em {{ $diasRestantes }} dias
                                                            </span>
                                                        @else
                                                            {{-- Atenção --}}
                                                            <span
                                                                class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded-full bg-amber-100 text-amber-700">
                                                                <span class="w-1.5 h-1.5 bg-amber-500 rounded-full"></span>
                                                                Em {{ $diasRestantes }} dias
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-3 text-center">
                                                        <a href="{{ route('rh.ferias.create', ['funcionario' => $funcionario->id]) }}"
                                                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-xs font-medium transition-colors">
                                                            📅 Agendar
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                        @if ($proximasFerias->count() > 0)
                            <table class="min-w-full text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 uppercase">
                                            Funcionário</th>
                                        <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 uppercase">
                                            Departamento</th>
                                        <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 uppercase">
                                            Início
                                        </th>
                                        <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 uppercase">Fim
                                        </th>
                                        <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 uppercase">
                                            Status
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y">
                                    @foreach ($proximasFerias as $ferias)
                                        <tr class="transition-colors hover:bg-gray-50">
                                            <td class="px-4 py-3 font-medium text-gray-900">
                                                {{ $ferias->funcionario->nome_completo }}</td>
                                            <td class="px-4 py-3 text-gray-500">
                                                {{ $ferias->funcionario->departamento?->nome ?? 'N/A' }}</td>
                                            <td class="px-4 py-3">{{ $ferias->data_inicio->format('d/m/Y') }}</td>
                                            <td class="px-4 py-3">{{ $ferias->data_fim->format('d/m/Y') }}</td>
                                            <td class="px-4 py-3">
                                                <span
                                                    class="px-2 py-0.5 text-xs rounded-full font-medium
                                                @if ($ferias->status === 'aprovada') bg-green-100 text-green-700
                                                @elseif($ferias->status === 'planejada') bg-blue-100 text-blue-700
                                                @else bg-gray-100 text-gray-600 @endif">
                                                    {{ ucfirst($ferias->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="p-8 text-center text-gray-400">
                                <p class="mb-1 text-lg">🎉</p>
                                <p>Nenhuma férias agendada para os próximos 30 dias.</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Últimas Atividades --}}
                <div class="bg-white border border-gray-100 shadow-sm rounded-xl">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                        <h3 class="font-semibold text-gray-800">📝 Atividades Recentes</h3>
                    </div>
                    <div class="p-4">
                        @if ($ultimasAtividades->count() > 0)
                            <div class="space-y-3">
                                @foreach ($ultimasAtividades as $atividade)
                                    <div class="flex items-start gap-3 p-2 transition-colors rounded-lg hover:bg-gray-50">
                                        <div
                                            class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0
                                        @if ($atividade->event === 'created') bg-green-100 text-green-600
                                        @elseif($atividade->event === 'updated') bg-blue-100 text-blue-600
                                        @elseif($atividade->event === 'deleted') bg-red-100 text-red-600
                                        @else bg-gray-100 text-gray-600 @endif">
                                            @if ($atividade->event === 'created')
                                                ➕
                                            @elseif($atividade->event === 'updated')
                                                ✏️
                                            @elseif($atividade->event === 'deleted')
                                                🗑️
                                            @else📌
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm text-gray-700">{{ $atividade->description }}</p>
                                            <p class="text-xs text-gray-400 mt-0.5">
                                                {{ $atividade->causer?->name ?? 'Sistema' }} •
                                                {{ $atividade->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="py-6 text-center text-gray-400">
                                Nenhuma atividade recente.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Coluna Direita (1/3) --}}
            <div class="space-y-6">

                {{-- Aniversariantes --}}
                <div class="bg-white border border-gray-100 shadow-sm rounded-xl">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-pink-50 to-purple-50">
                        <h3 class="font-semibold text-gray-800">🎂 Aniversariantes de {{ now()->translatedFormat('F') }}
                        </h3>
                    </div>
                    <div class="p-4">
                        @if ($aniversariantes->count() > 0)
                            <div class="space-y-2">
                                @foreach ($aniversariantes as $funcionario)
                                    <div class="flex items-center gap-3 p-2 transition-colors rounded-lg hover:bg-gray-50">
                                        <div
                                            class="flex items-center justify-center flex-shrink-0 w-10 h-10 text-sm font-bold text-white rounded-full shadow bg-gradient-to-br from-pink-400 to-purple-500">
                                            {{ mb_strtoupper(mb_substr($funcionario->nome_completo, 0, 1)) }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">
                                                {{ $funcionario->nome_completo }}</p>
                                            <p class="text-xs text-gray-400">
                                                {{ $funcionario->departamento?->nome ?? 'N/A' }}</p>
                                        </div>
                                        <div class="text-sm font-semibold text-pink-600">
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

                {{-- Distribuição por Departamento --}}
                <div class="bg-white border border-gray-100 shadow-sm rounded-xl">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="font-semibold text-gray-800">🏢 Por Departamento</h3>
                    </div>
                    <div class="p-4">
                        @if ($departamentos->count() > 0)
                            <div class="space-y-4">
                                @foreach ($departamentos as $dep)
                                    @php
                                        $porcentagem =
                                            $totalFuncionarios > 0
                                                ? round(($dep->funcionarios_count / $totalFuncionarios) * 100)
                                                : 0;
                                        $cores = [
                                            'bg-indigo-500',
                                            'bg-blue-500',
                                            'bg-green-500',
                                            'bg-yellow-500',
                                            'bg-purple-500',
                                            'bg-pink-500',
                                            'bg-teal-500',
                                        ];
                                        $cor = $cores[$loop->index % count($cores)];
                                    @endphp
                                    <div>
                                        <div class="flex justify-between text-sm mb-1.5">
                                            <span class="font-medium text-gray-700">{{ $dep->nome }}</span>
                                            <span class="font-medium text-gray-400">{{ $dep->funcionarios_count }}</span>
                                        </div>
                                        <div class="w-full h-2 bg-gray-100 rounded-full">
                                            <div class="{{ $cor }} h-2 rounded-full transition-all duration-700"
                                                style="width: {{ max($porcentagem, 3) }}%"></div>
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

                {{-- Links Rápidos --}}
                <div class="bg-white border border-gray-100 shadow-sm rounded-xl">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="font-semibold text-gray-800">⚡ Acesso Rápido</h3>
                    </div>
                    <div class="p-4 space-y-1">
                        @can('funcionarios.view')
                            <a href="{{ route('rh.funcionarios.create') }}"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-indigo-50 text-sm text-gray-600 hover:text-indigo-700 transition-colors">
                                <span class="text-lg">➕</span> Novo Funcionário
                            </a>
                        @endcan
                        @can('ferias.create')
                            <a href="{{ route('rh.ferias.index') }}"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-indigo-50 text-sm text-gray-600 hover:text-indigo-700 transition-colors">
                                <span class="text-lg">📅</span> Agendar Férias
                            </a>
                        @endcan
                        @can('folha.create')
                            <a href="{{ route('rh.folha-pagamento.create') }}"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-indigo-50 text-sm text-gray-600 hover:text-indigo-700 transition-colors">
                                <span class="text-lg">💰</span> Gerar Folha
                            </a>
                        @endcan
                        @can('folha.reports')
                            <a href="{{ route('rh.folha-pagamento.resumo-geral') }}"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-indigo-50 text-sm text-gray-600 hover:text-indigo-700 transition-colors">
                                <span class="text-lg">📊</span> Relatório Geral
                            </a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
