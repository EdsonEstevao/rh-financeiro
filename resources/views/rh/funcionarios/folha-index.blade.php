{{-- resources/views/rh/funcionarios/folha-index.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

        {{-- Cabeçalho --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Folha de Pagamento</h1>
                <p class="mt-1 text-sm text-gray-500">
                    Competência: {{ now()->format('m/Y') }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                @can('rh.folha.resumo')
                    <a href="{{ route('rh.folha-pagamento.resumo') }}"
                        class="inline-flex items-center gap-1.5 rounded-md bg-white px-4 py-2 text-sm font-medium text-gray-700 ring-1 ring-gray-300 hover:bg-gray-50 transition-colors">
                        <svg class="h-4 w-4 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                            <path
                                d="M15.5 2A1.5 1.5 0 0014 3.5v13a1.5 1.5 0 001.5 1.5h1a1.5 1.5 0 001.5-1.5v-13A1.5 1.5 0 0016.5 2h-1zM9.5 6A1.5 1.5 0 008 7.5v9A1.5 1.5 0 009.5 18h1a1.5 1.5 0 001.5-1.5v-9A1.5 1.5 0 0010.5 6h-1zM3.5 10A1.5 1.5 0 002 11.5v5A1.5 1.5 0 003.5 18h1A1.5 1.5 0 006 16.5v-5A1.5 1.5 0 004.5 10h-1z" />
                        </svg>
                        Resumo Geral
                    </a>
                @endcan
                @can('rh.folha.calcular')
                    <a href="{{ route('rh.folha-pagamento.calcular') }}"
                        class="inline-flex items-center gap-1.5 rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition-colors">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"
                                clip-rule="evenodd" />
                        </svg>
                        Calcular Folha
                    </a>
                @endcan
            </div>
        </div>

        {{-- Alertas --}}
        @if (session('success'))
            <div
                class="flex items-center gap-3 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700 ring-1 ring-green-600/20">
                <svg class="h-5 w-5 flex-shrink-0 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"
                        clip-rule="evenodd" />
                </svg>
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="flex items-center gap-3 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700 ring-1 ring-red-600/20">
                <svg class="h-5 w-5 flex-shrink-0 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z"
                        clip-rule="evenodd" />
                </svg>
                {{ session('error') }}
            </div>
        @endif

        {{-- Totalizadores --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            @php
                $cards = [
                    [
                        'label' => 'Funcionários',
                        'value' => $totalizadores['total_funcionarios'],
                        'format' => 'int',
                        'color' => 'indigo',
                    ],
                    [
                        'label' => 'Folha Bruta',
                        'value' => $totalizadores['soma_bruto'],
                        'format' => 'money',
                        'color' => 'blue',
                    ],
                    [
                        'label' => 'Total Descontos',
                        'value' => $totalizadores['soma_descontos'],
                        'format' => 'money',
                        'color' => 'red',
                    ],
                    [
                        'label' => 'Folha Líquida',
                        'value' => $totalizadores['soma_liquido'],
                        'format' => 'money',
                        'color' => 'green',
                    ],
                ];
            @endphp

            @foreach ($cards as $card)
                <div class="rounded-lg bg-white p-5 shadow ring-1 ring-black/5">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">
                        {{ $card['label'] }}
                    </p>
                    <p class="mt-2 text-2xl font-bold text-gray-900">
                        @if ($card['format'] === 'money')
                            R$ {{ number_format($card['value'], 2, ',', '.') }}
                        @else
                            {{ $card['value'] }}
                        @endif
                    </p>
                </div>
            @endforeach
        </div>

        {{-- Filtros --}}
        <div class="rounded-lg bg-white shadow ring-1 ring-black/5 overflow-hidden" x-data="{ aberto: {{ request()->hasAny(['departamento_id', 'cargo_id', 'local_trabalho', 'apenas_ativos']) ? 'true' : 'false' }} }">

            <button @click="aberto = !aberto"
                class="flex w-full items-center justify-between px-6 py-4 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                <div class="flex items-center gap-2">
                    <svg class="h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M2.628 1.601C5.028 1.206 7.49 1 10 1s4.973.206 7.372.601a.75.75 0 01.628.74v2.288a2.25 2.25 0 01-.659 1.59l-4.682 4.683a2.25 2.25 0 00-.659 1.59v3.037c0 .684-.31 1.33-.844 1.757l-1.937 1.55A.75.75 0 018 18.25v-5.757a2.25 2.25 0 00-.659-1.591L2.659 6.22A2.25 2.25 0 012 4.629V2.34a.75.75 0 01.628-.74z"
                            clip-rule="evenodd" />
                    </svg>
                    Filtros
                    @if (request()->hasAny(['departamento_id', 'cargo_id', 'local_trabalho', 'apenas_ativos']))
                        <span
                            class="inline-flex items-center rounded-full bg-indigo-100 px-2 py-0.5 text-xs font-medium text-indigo-700">
                            ativos
                        </span>
                    @endif
                </div>
                <svg :class="aberto ? 'rotate-180' : ''" class="h-4 w-4 text-gray-400 transition-transform"
                    viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M5.22 8.22a.75.75 0 011.06 0L10 11.94l3.72-3.72a.75.75 0 111.06 1.06l-4.25 4.25a.75.75 0 01-1.06 0L5.22 9.28a.75.75 0 010-1.06z"
                        clip-rule="evenodd" />
                </svg>
            </button>

            <div x-show="aberto" x-collapse class="border-t border-gray-100">
                <form method="GET" action="{{ route('rh.folha-pagamento.index') }}" class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                        {{-- Departamento --}}
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Departamento</label>
                            <select name="departamento_id"
                                class="block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Todos</option>
                                @foreach ($departamentos as $dep)
                                    <option value="{{ $dep->id }}"
                                        {{ request('departamento_id') == $dep->id ? 'selected' : '' }}>
                                        {{ $dep->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Cargo --}}
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Cargo</label>
                            <select name="cargo_id"
                                class="block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Todos</option>
                                @foreach ($cargos as $cargo)
                                    <option value="{{ $cargo->id }}"
                                        {{ request('cargo_id') == $cargo->id ? 'selected' : '' }}>
                                        {{ $cargo->titulo }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Local de Trabalho --}}
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Local de Trabalho</label>
                            <select name="local_trabalho"
                                class="block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Todos</option>
                                @foreach ($locaisTrabalho as $local)
                                    <option value="{{ $local }}"
                                        {{ request('local_trabalho') == $local ? 'selected' : '' }}>
                                        {{ $local }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Apenas Ativos --}}
                        <div class="flex items-end">
                            <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                                <input type="hidden" name="apenas_ativos" value="0" />
                                <input type="checkbox" name="apenas_ativos" value="1"
                                    {{ request()->boolean('apenas_ativos') ? 'checked' : '' }}
                                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                                <span class="text-sm text-gray-700">Apenas ativos</span>
                            </label>
                        </div>
                    </div>

                    <div class="mt-4 flex items-center gap-2">
                        <button type="submit"
                            class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition-colors">
                            Filtrar
                        </button>
                        <a href="{{ route('rh.folha-pagamento.index') }}"
                            class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                            Limpar
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tabela --}}
        <div class="rounded-lg bg-white shadow ring-1 ring-black/5 overflow-hidden">
            @if ($funcionarios->isEmpty())
                <div class="px-6 py-16 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm1.294 6.336a6.721 6.721 0 01-3.17.789 6.721 6.721 0 01-3.168-.789 3.376 3.376 0 016.338 0z" />
                    </svg>
                    <p class="mt-4 text-sm text-gray-500">Nenhum funcionário encontrado.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">
                                    Funcionário</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">
                                    Cargo / Depto</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wide">
                                    Salário Base</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wide">
                                    Bruto</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wide">
                                    INSS</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wide">
                                    Líquido</th>
                                <th
                                    class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wide">
                                    Status</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wide">
                                    Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach ($funcionarios as $funcionario)
                                <tr class="hover:bg-gray-50 transition-colors">

                                    {{-- Nome --}}
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-indigo-100 text-xs font-bold text-indigo-700">
                                                {{ strtoupper(substr($funcionario->nome_completo, 0, 2)) }}
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900">{{ $funcionario->nome_completo }}</p>
                                                <p class="text-xs text-gray-400">
                                                    Adm:
                                                    {{ optional($funcionario->data_admissao)->format('d/m/Y') ?? '—' }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Cargo / Depto --}}
                                    <td class="px-4 py-3">
                                        <p class="text-gray-900">{{ optional($funcionario->cargo)->titulo ?? '—' }}</p>
                                        <p class="text-xs text-gray-400">
                                            {{ optional($funcionario->departamento)->nome ?? '—' }}</p>
                                    </td>

                                    {{-- Salário Base --}}
                                    <td class="px-4 py-3 text-right font-mono text-gray-700">
                                        R$ {{ number_format($funcionario->salario_base ?? 0, 2, ',', '.') }}
                                    </td>

                                    {{-- Bruto --}}
                                    <td class="px-4 py-3 text-right font-mono text-blue-700 font-medium">
                                        R$ {{ number_format($funcionario->salario_bruto ?? 0, 2, ',', '.') }}
                                    </td>

                                    {{-- INSS --}}
                                    <td class="px-4 py-3 text-right font-mono text-red-600">
                                        R$ {{ number_format($funcionario->desconto_inss_8_porcento ?? 0, 2, ',', '.') }}
                                    </td>

                                    {{-- Líquido --}}
                                    <td class="px-4 py-3 text-right font-mono text-green-700 font-bold">
                                        R$ {{ number_format($funcionario->salario_liquido ?? 0, 2, ',', '.') }}
                                    </td>

                                    {{-- Status --}}
                                    <td class="px-4 py-3 text-center">
                                        @if ($funcionario->ativo)
                                            <span
                                                class="inline-flex items-center gap-1 rounded-full bg-green-50 px-2 py-0.5 text-xs font-semibold text-green-700 ring-1 ring-green-600/20">
                                                <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span> Ativo
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2 py-0.5 text-xs font-semibold text-gray-500 ring-1 ring-gray-400/20">
                                                <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span> Inativo
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Ações --}}
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('rh.folha-pagamento.show', $funcionario) }}"
                                            class="inline-flex items-center gap-1 rounded-md bg-white px-2.5 py-1.5 text-xs font-medium text-gray-700 ring-1 ring-gray-300 hover:bg-gray-50 transition-colors">
                                            <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M10 12.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z" />
                                                <path fill-rule="evenodd"
                                                    d="M.664 10.59a1.651 1.651 0 010-1.186A10.004 10.004 0 0110 3c4.257 0 7.893 2.66 9.336 6.41.147.381.146.804 0 1.186A10.004 10.004 0 0110 17c-4.257 0-7.893-2.66-9.336-6.41z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            Ver Holerite
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                        {{-- Rodapé com totais da página --}}
                        <tfoot class="bg-gray-50 border-t-2 border-gray-200">
                            <tr>
                                <td colspan="2" class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase">
                                    Totais (página atual)
                                </td>
                                <td class="px-4 py-3 text-right font-mono text-sm font-bold text-gray-700">
                                    R$ {{ number_format($totalizadores['soma_salario_base'], 2, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-right font-mono text-sm font-bold text-blue-700">
                                    R$ {{ number_format($totalizadores['soma_bruto'], 2, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-right font-mono text-sm font-bold text-red-600">
                                    R$ {{ number_format($totalizadores['soma_inss'], 2, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-right font-mono text-sm font-bold text-green-700">
                                    R$ {{ number_format($totalizadores['soma_liquido'], 2, ',', '.') }}
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- Paginação --}}
                @if ($funcionarios->hasPages())
                    <div class="border-t border-gray-200 px-6 py-4">
                        {{ $funcionarios->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
@endsection
