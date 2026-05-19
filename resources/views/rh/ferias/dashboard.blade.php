{{-- resources/views/rh/ferias/dashboard.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">📅 Dashboard de Férias</h1>
                <p class="mt-1 text-sm text-gray-500">
                    Visão geral do controle de férias •
                    <span class="font-medium">{{ now()->format('d/m/Y') }}</span>
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('rh.funcionarios.index') }}"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 text-sm">
                    👥 Funcionários
                </a>
                <a href="{{ route('rh.ferias.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm">
                    📋 Todos os Períodos
                </a>
            </div>
        </div>

        {{-- Cards de Resumo --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            {{-- Férias Vencidas --}}
            <a href="#vencidas" class="block">
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-red-600 font-medium">🔴 Férias Vencidas</p>
                            <p class="text-3xl font-bold text-red-700">{{ $feriasVencidas->count() }}</p>
                            <p class="text-xs text-red-500 mt-1">Pagamento em dobro</p>
                        </div>
                        <svg class="h-10 w-10 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                </div>
            </a>

            {{-- Vence em 30 dias --}}
            <a href="#vencendo30" class="block">
                <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-orange-600 font-medium">🟠 Vence em 30 dias</p>
                            <p class="text-3xl font-bold text-orange-700">{{ $vencendo30dias->count() }}</p>
                            <p class="text-xs text-orange-500 mt-1">Atenção necessária</p>
                        </div>
                        <svg class="h-10 w-10 text-orange-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </a>

            {{-- Vence em 60 dias --}}
            <a href="#vencendo60" class="block">
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-yellow-600 font-medium">🟡 Vence em 60 dias</p>
                            <p class="text-3xl font-bold text-yellow-700">{{ $vencendo60dias->count() }}</p>
                            <p class="text-xs text-yellow-500 mt-1">Programar férias</p>
                        </div>
                        <svg class="h-10 w-10 text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </a>

            {{-- Em andamento --}}
            <a href="#emAndamento" class="block">
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-green-600 font-medium">🟢 Em andamento</p>
                            <p class="text-3xl font-bold text-green-700">{{ $feriasEmAndamento->count() }}</p>
                            <p class="text-xs text-green-500 mt-1">Funcionários de férias</p>
                        </div>
                        <svg class="h-10 w-10 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </div>
            </a>
        </div>

        {{-- ============================================ --}}
        {{-- 🔴 Férias Vencidas --}}
        {{-- ============================================ --}}
        <div id="vencidas">
            @if ($feriasVencidas->count() > 0)
                <div class="bg-white rounded-lg shadow mb-6">
                    <div class="px-6 py-4 border-b border-gray-200 bg-red-50 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-red-800">
                            🔴 Férias Vencidas
                            <span class="text-sm font-normal text-red-600">(Pagamento em Dobro)</span>
                        </h2>
                        <span class="text-sm text-red-600 font-medium">{{ $feriasVencidas->count() }} funcionário(s)</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-red-50">
                                <tr>
                                    <th class="px-4 py-3 text-left font-medium text-red-800">Funcionário</th>
                                    <th class="px-4 py-3 text-left font-medium text-red-800">Cargo</th>
                                    <th class="px-4 py-3 text-left font-medium text-red-800">Admissão</th>
                                    <th class="px-4 py-3 text-left font-medium text-red-800">Vencimento</th>
                                    <th class="px-4 py-3 text-left font-medium text-red-800">Status</th>
                                    <th class="px-4 py-3 text-center font-medium text-red-800">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach ($feriasVencidas as $funcionario)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 font-medium">{{ $funcionario->nome_completo }}</td>
                                        <td class="px-4 py-3">{{ $funcionario->cargo?->titulo ?? 'N/A' }}</td>
                                        {{-- ✅ CORRIGIDO: data_admissao no contrato --}}
                                        <td class="px-4 py-3">
                                            {{ $funcionario->contrato?->data_admissao
                                                ? \Carbon\Carbon::parse($funcionario->contrato->data_admissao)->format('d/m/Y')
                                                : 'N/A' }}
                                        </td>
                                        {{-- ✅ CORRIGIDO: fallback para ferias_vencimento null --}}
                                        <td class="px-4 py-3 text-red-600 font-medium">
                                            {{ $funcionario->ferias_vencimento
                                                ? \Carbon\Carbon::parse($funcionario->ferias_vencimento)->format('d/m/Y')
                                                : 'Não definida' }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <span
                                                class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800 font-medium">
                                                Vencida (Dobro)
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <a href="{{ route('rh.ferias.create', ['funcionario' => $funcionario->id]) }}"
                                                class="text-indigo-600 hover:text-indigo-900 text-xs font-medium">
                                                Agendar Férias
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="bg-white rounded-lg shadow mb-6 p-6 text-center text-gray-500">
                    ✅ Nenhuma férias vencida!
                </div>
            @endif
        </div>

        {{-- ============================================ --}}
        {{-- 🟠 Vencendo em 30 dias --}}
        {{-- ============================================ --}}
        <div id="vencendo30">
            @if ($vencendo30dias->count() > 0)
                <div class="bg-white rounded-lg shadow mb-6">
                    <div class="px-6 py-4 border-b border-gray-200 bg-orange-50 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-orange-800">🟠 Férias Vencendo em 30 Dias</h2>
                        <span class="text-sm text-orange-600 font-medium">{{ $vencendo30dias->count() }}
                            funcionário(s)</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-orange-50">
                                <tr>
                                    <th class="px-4 py-3 text-left font-medium text-orange-800">Funcionário</th>
                                    <th class="px-4 py-3 text-left font-medium text-orange-800">Cargo</th>
                                    <th class="px-4 py-3 text-left font-medium text-orange-800">Vencimento</th>
                                    <th class="px-4 py-3 text-left font-medium text-orange-800">Dias Restantes</th>
                                    <th class="px-4 py-3 text-center font-medium text-orange-800">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach ($vencendo30dias as $funcionario)
                                    @php
                                        $diasRestantes = now()
                                            ->startOfDay()
                                            ->diffInDays(\Carbon\Carbon::parse($funcionario->ferias_vencimento), false);
                                    @endphp
                                    <tr class="hover:bg-gray-50 {{ $diasRestantes <= 7 ? 'bg-orange-50' : '' }}">
                                        <td class="px-4 py-3 font-medium">
                                            {{ $funcionario->nome_completo }}
                                            @if ($diasRestantes <= 7)
                                                <span class="ml-1 text-xs text-red-500">⚠️</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">{{ $funcionario->cargo?->titulo ?? 'N/A' }}</td>
                                        <td class="px-4 py-3 text-orange-600 font-medium">
                                            {{ \Carbon\Carbon::parse($funcionario->ferias_vencimento)->format('d/m/Y') }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <span
                                                class="px-2 py-1 text-xs rounded-full font-medium
                                                {{ $diasRestantes <= 7 ? 'bg-red-100 text-red-800' : 'bg-orange-100 text-orange-800' }}">
                                                {{ $diasRestantes }} dias
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <a href="{{ route('rh.ferias.create', ['funcionario' => $funcionario->id]) }}"
                                                class="text-indigo-600 hover:text-indigo-900 text-xs font-medium">
                                                Agendar
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        {{-- ============================================ --}}
        {{-- 🟡 Vencendo em 60 dias --}}
        {{-- ============================================ --}}
        <div id="vencendo60">
            @if ($vencendo60dias->count() > 0)
                <div class="bg-white rounded-lg shadow mb-6">
                    <div class="px-6 py-4 border-b border-gray-200 bg-yellow-50 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-yellow-800">🟡 Férias Vencendo em 60 Dias</h2>
                        <span class="text-sm text-yellow-600 font-medium">{{ $vencendo60dias->count() }}
                            funcionário(s)</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-yellow-50">
                                <tr>
                                    <th class="px-4 py-3 text-left font-medium text-yellow-800">Funcionário</th>
                                    <th class="px-4 py-3 text-left font-medium text-yellow-800">Cargo</th>
                                    <th class="px-4 py-3 text-left font-medium text-yellow-800">Admissão</th>
                                    <th class="px-4 py-3 text-left font-medium text-yellow-800">Vencimento</th>
                                    <th class="px-4 py-3 text-left font-medium text-yellow-800">Dias Restantes</th>
                                    <th class="px-4 py-3 text-center font-medium text-yellow-800">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach ($vencendo60dias as $funcionario)
                                    @php
                                        $diasRestantes = now()
                                            ->startOfDay()
                                            ->diffInDays(\Carbon\Carbon::parse($funcionario->ferias_vencimento), false);
                                    @endphp
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 font-medium">{{ $funcionario->nome_completo }}</td>
                                        <td class="px-4 py-3">{{ $funcionario->cargo?->titulo ?? 'N/A' }}</td>
                                        {{-- ✅ CORRIGIDO --}}
                                        <td class="px-4 py-3">
                                            {{ $funcionario->contrato?->data_admissao
                                                ? \Carbon\Carbon::parse($funcionario->contrato->data_admissao)->format('d/m/Y')
                                                : 'N/A' }}
                                        </td>
                                        <td class="px-4 py-3 text-yellow-600 font-medium">
                                            {{ \Carbon\Carbon::parse($funcionario->ferias_vencimento)->format('d/m/Y') }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <span
                                                class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800 font-medium">
                                                {{ $diasRestantes }} dias
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <a href="{{ route('rh.ferias.create', ['funcionario' => $funcionario->id]) }}"
                                                class="text-indigo-600 hover:text-indigo-900 text-xs font-medium">
                                                Programar
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        {{-- ============================================ --}}
        {{-- 🟢 Férias em Andamento --}}
        {{-- ============================================ --}}
        <div id="emAndamento">
            @if ($feriasEmAndamento->count() > 0)
                <div class="bg-white rounded-lg shadow mb-6">
                    <div class="px-6 py-4 border-b border-gray-200 bg-green-50 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-green-800">🟢 Férias em Andamento</h2>
                        <span class="text-sm text-green-600 font-medium">{{ $feriasEmAndamento->count() }}
                            funcionário(s)</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-green-50">
                                <tr>
                                    <th class="px-4 py-3 text-left font-medium text-green-800">Funcionário</th>
                                    <th class="px-4 py-3 text-left font-medium text-green-800">Cargo</th>
                                    <th class="px-4 py-3 text-left font-medium text-green-800">Início</th>
                                    <th class="px-4 py-3 text-left font-medium text-green-800">Fim</th>
                                    <th class="px-4 py-3 text-left font-medium text-green-800">Retorno</th>
                                    <th class="px-4 py-3 text-left font-medium text-green-800">Progresso</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach ($feriasEmAndamento as $periodo)
                                    @php
                                        $totalDias = $periodo->data_inicio->diffInDays($periodo->data_fim) + 1;
                                        $diasPassados = $periodo->data_inicio->diffInDays(now()->startOfDay());
                                        $progresso = min(100, round(($diasPassados / $totalDias) * 100));
                                    @endphp
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 font-medium">{{ $periodo->funcionario->nome_completo }}</td>
                                        <td class="px-4 py-3">{{ $periodo->funcionario->cargo?->titulo ?? 'N/A' }}</td>
                                        <td class="px-4 py-3">{{ $periodo->data_inicio->format('d/m/Y') }}</td>
                                        <td class="px-4 py-3">{{ $periodo->data_fim->format('d/m/Y') }}</td>
                                        <td class="px-4 py-3 font-medium text-green-600">
                                            {{ $periodo->data_fim->addDay()->format('d/m/Y') }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2">
                                                <div class="w-24 bg-gray-200 rounded-full h-2">
                                                    <div class="bg-green-500 h-2 rounded-full"
                                                        style="width: {{ $progresso }}%"></div>
                                                </div>
                                                <span class="text-xs text-gray-600">{{ $progresso }}%</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="bg-white rounded-lg shadow mb-6 p-6 text-center text-gray-500">
                    🏢 Nenhum funcionário está de férias no momento.
                </div>
            @endif
        </div>

        {{-- ============================================ --}}
        {{-- ✅ Próximas Férias Agendadas --}}
        {{-- ============================================ --}}
        @if ($feriasAgendadas->count() > 0)
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="px-6 py-4 border-b border-gray-200 bg-blue-50 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-blue-800">📅 Próximas Férias Agendadas</h2>
                    <span class="text-sm text-blue-600 font-medium">{{ $feriasAgendadas->count() }} período(s)</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-blue-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-medium text-blue-800">Funcionário</th>
                                <th class="px-4 py-3 text-left font-medium text-blue-800">Cargo</th>
                                <th class="px-4 py-3 text-left font-medium text-blue-800">Início</th>
                                <th class="px-4 py-3 text-left font-medium text-blue-800">Fim</th>
                                <th class="px-4 py-3 text-left font-medium text-blue-800">Dias</th>
                                <th class="px-4 py-3 text-center font-medium text-blue-800">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach ($feriasAgendadas as $periodo)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-medium">{{ $periodo->funcionario->nome_completo }}</td>
                                    <td class="px-4 py-3">{{ $periodo->funcionario->cargo?->titulo ?? 'N/A' }}</td>
                                    <td class="px-4 py-3">{{ $periodo->data_inicio->format('d/m/Y') }}</td>
                                    <td class="px-4 py-3">{{ $periodo->data_fim->format('d/m/Y') }}</td>
                                    <td class="px-4 py-3">
                                        {{ $periodo->data_inicio->diffInDays($periodo->data_fim) + 1 }} dias
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <a href="{{ route('rh.ferias.edit', $periodo) }}"
                                            class="text-indigo-600 hover:text-indigo-900 text-xs font-medium">
                                            Editar
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- Vencendo em 90 dias (menos crítico, pode ser colapsável) --}}
        @if ($vencendo90dias->count() > 0)
            <div class="bg-white rounded-lg shadow mb-6" x-data="{ open: false }">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between cursor-pointer"
                    @click="open = !open">
                    <h2 class="text-lg font-semibold text-gray-700">
                        ⚪ Férias Vencendo em 90 Dias
                        <span class="text-sm font-normal text-gray-500">({{ $vencendo90dias->count() }}
                            funcionários)</span>
                    </h2>
                    <svg class="h-5 w-5 text-gray-400 transform transition-transform" :class="{ 'rotate-180': open }"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
                <div x-show="open" x-transition>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left">Funcionário</th>
                                    <th class="px-4 py-3 text-left">Cargo</th>
                                    <th class="px-4 py-3 text-left">Vencimento</th>
                                    <th class="px-4 py-3 text-left">Dias Restantes</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach ($vencendo90dias as $funcionario)
                                    @php
                                        $diasRestantes = now()
                                            ->startOfDay()
                                            ->diffInDays(\Carbon\Carbon::parse($funcionario->ferias_vencimento), false);
                                    @endphp
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 font-medium">{{ $funcionario->nome_completo }}</td>
                                        <td class="px-4 py-3">{{ $funcionario->cargo?->titulo ?? 'N/A' }}</td>
                                        <td class="px-4 py-3">
                                            {{ \Carbon\Carbon::parse($funcionario->ferias_vencimento)->format('d/m/Y') }}
                                        </td>
                                        <td class="px-4 py-3">{{ $diasRestantes }} dias</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
