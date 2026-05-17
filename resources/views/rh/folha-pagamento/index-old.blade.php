@extends('layouts.app')

@section('content')
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">

        {{-- ─── HEADER ──────────────────────────────────────────────── --}}
        <div class="mb-6 sm:flex sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold leading-tight text-gray-900">Folha de Pagamento</h1>
                <p class="mt-1 text-sm text-gray-600">Gerencie as folhas de pagamento mensais</p>
            </div>
            <div class="mt-4 sm:mt-0 flex items-center gap-3">
                {{-- Botão PDF --}}
                <a href="{{ route('rh.folha-pagamento.pdf.geral', ['competencia' => $competencia, 'status' => $status]) }}"
                    target="_blank"
                    class="inline-flex items-center px-4 py-2 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50 shadow-sm">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    Exportar PDF
                </a>
                {{-- Botão Nova Folha --}}
                <a href="{{ route('rh.folha-pagamento.create') }}"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Nova Folha
                </a>
            </div>
        </div>

        {{-- ─── MENSAGENS ───────────────────────────────────────────── --}}
        {{-- @if (session('success'))
            <div
                class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-md flex items-center gap-2">
                <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-md flex items-center gap-2">
                <svg class="h-5 w-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                {{ session('error') }}
            </div>
        @endif --}}

        {{-- ─── CARDS RESUMO ────────────────────────────────────────── --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            {{-- Funcionários --}}
            <div class="bg-white rounded-lg shadow p-5 flex items-center gap-4">
                <div class="flex-shrink-0 bg-indigo-100 rounded-full p-3">
                    <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium uppercase">Funcionários</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totais->total_funcionarios ?? 0 }}</p>
                </div>
            </div>

            {{-- Total Proventos --}}
            <div class="bg-white rounded-lg shadow p-5 flex items-center gap-4">
                <div class="flex-shrink-0 bg-green-100 rounded-full p-3">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium uppercase">Total Proventos</p>
                    <p class="text-xl font-bold text-green-700">
                        R$ {{ number_format($totais->total_proventos ?? 0, 2, ',', '.') }}
                    </p>
                </div>
            </div>

            {{-- Total Descontos --}}
            <div class="bg-white rounded-lg shadow p-5 flex items-center gap-4">
                <div class="flex-shrink-0 bg-red-100 rounded-full p-3">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium uppercase">Total Descontos</p>
                    <p class="text-xl font-bold text-red-700">
                        R$ {{ number_format($totais->total_descontos ?? 0, 2, ',', '.') }}
                    </p>
                </div>
            </div>

            {{-- Total Líquido --}}
            <div class="bg-white rounded-lg shadow p-5 flex items-center gap-4">
                <div class="flex-shrink-0 bg-yellow-100 rounded-full p-3">
                    <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium uppercase">Total Líquido</p>
                    <p class="text-xl font-bold text-indigo-700">
                        R$ {{ number_format($totais->total_salario_liquido ?? 0, 2, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        {{-- ─── FILTROS ─────────────────────────────────────────────── --}}
        <div class="bg-white shadow rounded-lg p-4 mb-6">
            <form method="GET" action="{{ route('rh.folha-pagamento.index') }}" class="flex flex-wrap items-end gap-4">

                {{-- Competência --}}
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-medium text-gray-600 uppercase">Competência</label>
                    <input type="month" name="competencia" value="{{ $competencia }}"
                        class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500" />
                </div>

                {{-- Status --}}
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-medium text-gray-600 uppercase">Status</label>
                    <select name="status"
                        class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Todos</option>
                        <option value="cancelada" {{ request('status') === 'cancelada' ? 'selected' : '' }}>Cancelada
                        </option>
                        <option value="fechada" {{ request('status') === 'fechada' ? 'selected' : '' }}>Fechada</option>
                        </option>
                        <option value="aberta" {{ request('status') === 'aberta' ? 'selected' : '' }}>Aberta</option>
                    </select>
                </div>

                {{-- Botões --}}
                <div class="flex gap-2">
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">
                        <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 010 2H4a1 1 0 01-1-1zm3 6a1 1 0 011-1h10a1 1 0 010 2H7a1 1 0 01-1-1zm4 6a1 1 0 011-1h2a1 1 0 010 2h-2a1 1 0 01-1-1z" />
                        </svg>
                        Filtrar
                    </button>
                    <a href="{{ route('rh.folha-pagamento.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-200">
                        Limpar
                    </a>
                </div>

            </form>
        </div>

        {{-- ─── TABELA ──────────────────────────────────────────────── --}}
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">

                    {{-- THEAD --}}
                    <thead class="bg-indigo-800 text-white text-xs uppercase">
                        <tr>
                            <th class="px-3 py-3 text-left whitespace-nowrap">Funcionário</th>
                            <th class="px-3 py-3 text-center whitespace-nowrap">Competência</th>
                            <th class="px-3 py-3 text-center whitespace-nowrap">Status</th>
                            <th class="px-3 py-3 text-right whitespace-nowrap">Salário Base</th>
                            <th class="px-3 py-3 text-right whitespace-nowrap">Grat. Feriado</th>
                            <th class="px-3 py-3 text-right whitespace-nowrap">DSR Hr Extra</th>
                            <th class="px-3 py-3 text-right whitespace-nowrap">Sal. Família</th>
                            <th class="px-3 py-3 text-right whitespace-nowrap">Arred. Prov.</th>
                            <th class="px-3 py-3 text-right whitespace-nowrap text-green-300">Total Proventos</th>
                            <th class="px-3 py-3 text-right whitespace-nowrap">INSS</th>
                            <th class="px-3 py-3 text-right whitespace-nowrap">Vale Dia 20</th>
                            <th class="px-3 py-3 text-right whitespace-nowrap">Vale Extra</th>
                            <th class="px-3 py-3 text-right whitespace-nowrap">Faltas</th>
                            <th class="px-3 py-3 text-right whitespace-nowrap">DSR Faltas</th>
                            <th class="px-3 py-3 text-right whitespace-nowrap">Arred. Desc.</th>
                            <th class="px-3 py-3 text-right whitespace-nowrap text-red-300">Total Descontos</th>
                            <th class="px-3 py-3 text-right whitespace-nowrap text-yellow-300">Salário Líquido</th>
                            <th class="px-3 py-3 text-center whitespace-nowrap">Ações</th>
                        </tr>
                    </thead>

                    {{-- TBODY --}}
                    <tbody class="divide-y divide-gray-200 text-sm">
                        @forelse($folhas as $folha)
                            <tr class="hover:bg-indigo-50 transition">

                                <td class="px-3 py-2.5 whitespace-nowrap font-medium text-gray-900">
                                    {{ $folha->funcionario->nome_completo ?? '—' }}
                                </td>

                                <td class="px-3 py-2.5 whitespace-nowrap text-center text-gray-600">
                                    {{ \Carbon\Carbon::parse($folha->competencia)->translatedFormat('M/Y') }}
                                </td>

                                {{-- Badge Status --}}
                                <td class="px-3 py-2.5 whitespace-nowrap text-center">
                                    @php
                                        $badge = match ($folha->status) {
                                            'aberta' => 'bg-green-100 text-green-800',
                                            'fechada' => 'bg-blue-100 text-blue-800',
                                            default => 'bg-yellow-100 text-yellow-800',
                                        };
                                        $label = match ($folha->status) {
                                            'aberta' => 'Aberta',
                                            'fechada' => 'Fechada',
                                            default => 'Cancelada',
                                        };
                                    @endphp
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badge }}">
                                        {{ $label }}
                                    </span>
                                </td>

                                <td class="px-3 py-2.5 text-right whitespace-nowrap">
                                    R$ {{ number_format($folha->salario_base, 2, ',', '.') }}
                                </td>
                                <td class="px-3 py-2.5 text-right whitespace-nowrap">
                                    R$ {{ number_format($folha->gratificacao_feriado, 2, ',', '.') }}
                                </td>
                                <td class="px-3 py-2.5 text-right whitespace-nowrap">
                                    R$ {{ number_format($folha->dsr_hora_extra, 2, ',', '.') }}
                                </td>
                                <td class="px-3 py-2.5 text-right whitespace-nowrap">
                                    R$ {{ number_format($folha->salario_familia_hr_extra, 2, ',', '.') }}
                                </td>
                                <td class="px-3 py-2.5 text-right whitespace-nowrap">
                                    R$ {{ number_format($folha->arredondamento_provento, 2, ',', '.') }}
                                </td>

                                {{-- Total Proventos (accessor) --}}
                                <td class="px-3 py-2.5 text-right whitespace-nowrap font-semibold text-green-700">
                                    R$ {{ number_format($folha->total_proventos, 2, ',', '.') }}
                                </td>

                                <td class="px-3 py-2.5 text-right whitespace-nowrap">
                                    R$ {{ number_format($folha->desconto_inss, 2, ',', '.') }}
                                </td>
                                <td class="px-3 py-2.5 text-right whitespace-nowrap">
                                    R$ {{ number_format($folha->vale_dia_20, 2, ',', '.') }}
                                </td>
                                <td class="px-3 py-2.5 text-right whitespace-nowrap">
                                    R$ {{ number_format($folha->vale_extra, 2, ',', '.') }}
                                </td>
                                <td class="px-3 py-2.5 text-right whitespace-nowrap">
                                    R$ {{ number_format($folha->faltas_valor, 2, ',', '.') }}
                                </td>
                                <td class="px-3 py-2.5 text-right whitespace-nowrap">
                                    R$ {{ number_format($folha->dsr_faltas, 2, ',', '.') }}
                                </td>
                                <td class="px-3 py-2.5 text-right whitespace-nowrap">
                                    R$ {{ number_format($folha->arredondamento_desconto, 2, ',', '.') }}
                                </td>

                                {{-- Total Descontos (accessor) --}}
                                <td class="px-3 py-2.5 text-right whitespace-nowrap font-semibold text-red-600">
                                    R$ {{ number_format($folha->total_descontos, 2, ',', '.') }}
                                </td>

                                {{-- Salário Líquido (accessor calculado) --}}
                                <td class="px-3 py-2.5 text-right whitespace-nowrap font-bold text-indigo-700">
                                    R$ {{ number_format($folha->salario_liquido, 2, ',', '.') }}
                                </td>

                                {{-- Ações --}}
                                <td class="px-3 py-2.5 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        {{-- Ver --}}
                                        <a href="{{ route('rh.folha-pagamento.show', $folha) }}" title="Visualizar"
                                            class="text-gray-500 hover:text-indigo-600">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        {{-- Editar --}}
                                        <a href="{{ route('rh.folha-pagamento.edit', $folha) }}" title="Editar"
                                            class="text-gray-500 hover:text-yellow-600">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        {{-- Excluir --}}
                                        <form action="{{ route('rh.folha-pagamento.destroy', $folha) }}" method="POST"
                                            onsubmit="return confirm('Confirma a exclusão desta folha?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Excluir"
                                                class="text-gray-500 hover:text-red-600">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="18" class="px-3 py-10 text-center text-gray-400">
                                    <svg class="mx-auto h-10 w-10 text-gray-300 mb-2" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Nenhuma folha encontrada para esta competência.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                    {{-- TFOOT --}}
                    <tfoot class="bg-indigo-900 text-white text-xs font-bold">
                        <tr>
                            <td class="px-3 py-3 whitespace-nowrap">
                                {{ $totais->total_funcionarios ?? 0 }} funcionário(s)
                            </td>
                            <td colspan="2"></td>
                            <td class="px-3 py-3 text-right whitespace-nowrap">
                                R$ {{ number_format($totais->total_salario_base ?? 0, 2, ',', '.') }}
                            </td>
                            <td class="px-3 py-3 text-right whitespace-nowrap">
                                R$ {{ number_format($totais->total_gratificacao ?? 0, 2, ',', '.') }}
                            </td>
                            <td class="px-3 py-3 text-right whitespace-nowrap">
                                R$ {{ number_format($totais->total_dsr_hora_extra ?? 0, 2, ',', '.') }}
                            </td>
                            <td class="px-3 py-3 text-right whitespace-nowrap">
                                R$ {{ number_format($totais->total_sal_familia_hr_extra ?? 0, 2, ',', '.') }}
                            </td>
                            <td class="px-3 py-3 text-right whitespace-nowrap">
                                R$ {{ number_format($totais->total_arred_provento ?? 0, 2, ',', '.') }}
                            </td>
                            <td class="px-3 py-3 text-right whitespace-nowrap text-green-300">
                                R$ {{ number_format($totais->total_proventos ?? 0, 2, ',', '.') }}
                            </td>
                            <td class="px-3 py-3 text-right whitespace-nowrap">
                                R$ {{ number_format($totais->total_desconto_inss ?? 0, 2, ',', '.') }}
                            </td>
                            <td class="px-3 py-3 text-right whitespace-nowrap">
                                R$ {{ number_format($totais->total_vale_dia_20 ?? 0, 2, ',', '.') }}
                            </td>
                            <td class="px-3 py-3 text-right whitespace-nowrap">
                                R$ {{ number_format($totais->total_vale_extra ?? 0, 2, ',', '.') }}
                            </td>
                            <td class="px-3 py-3 text-right whitespace-nowrap">
                                R$ {{ number_format($totais->total_faltas ?? 0, 2, ',', '.') }}
                            </td>
                            <td class="px-3 py-3 text-right whitespace-nowrap">
                                R$ {{ number_format($totais->total_dsr_faltas ?? 0, 2, ',', '.') }}
                            </td>
                            <td class="px-3 py-3 text-right whitespace-nowrap">
                                R$ {{ number_format($totais->total_arred_desconto ?? 0, 2, ',', '.') }}
                            </td>
                            <td class="px-3 py-3 text-right whitespace-nowrap text-red-300">
                                R$ {{ number_format($totais->total_descontos ?? 0, 2, ',', '.') }}
                            </td>
                            <td class="px-3 py-3 text-right whitespace-nowrap text-yellow-300">
                                R$ {{ number_format($totais->total_salario_liquido ?? 0, 2, ',', '.') }}
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>

                </table>
            </div>
        </div>

        {{-- ─── PAGINAÇÃO ───────────────────────────────────────────── --}}
        @if ($folhas->hasPages())
            <div class="mt-6">
                {{ $folhas->appends(request()->query())->links() }}
            </div>
        @endif

    </div>
@endsection
