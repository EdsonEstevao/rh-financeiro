@extends('layouts.app')

@section('content')
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- ── CABEÇALHO ─────────────────────────────────────────── --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Resumo Geral da Folha de Pagamento</h1>
                <p class="mt-1 text-sm text-gray-500">
                    Visualização consolidada por funcionário com todos os componentes salariais.
                </p>
            </div>

            {{-- Botão PDF --}}
            @can('folha.reports')
                {{-- <a href="{{ route('rh.folha-pagamento.resumo-geral.pdf', request()->query()) }}" target="_blank" --}}
                <a href="#" target="_blank"
                    class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-red-700 transition-colors focus:outline-none focus:ring-2 focus:ring-red-500">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                    Baixar PDF
                </a>
            @endcan
        </div>

        {{-- ── FILTROS ─────────────────────────────────────────────── --}}
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200 p-5">
            <form method="GET" action="{{ route('rh.folha-pagamento.resumo-geral') }}"
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                {{-- Competência --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">
                        Competência
                    </label>
                    <select name="competencia"
                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Todas</option>
                        @foreach ($competencias as $comp)
                            <option value="{{ $comp }}" {{ request('competencia') == $comp ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::parse($comp)->translatedFormat('F/Y') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Local de Trabalho --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">
                        Local de Trabalho
                    </label>
                    <select name="local_trabalho"
                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Todos</option>
                        @foreach ($locaisTrabalho as $local)
                            <option value="{{ $local }}" {{ request('local_trabalho') == $local ? 'selected' : '' }}>
                                {{ $local }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Função/Cargo --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">
                        Função
                    </label>
                    <select name="cargo_id"
                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Todas</option>
                        @foreach ($cargos as $cargo)
                            <option value="{{ $cargo->id }}" {{ request('cargo_id') == $cargo->id ? 'selected' : '' }}>
                                {{ $cargo->titulo }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Botões --}}
                <div class="flex items-end gap-2">
                    <button type="submit"
                        class="flex-1 inline-flex justify-center items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                                clip-rule="evenodd" />
                        </svg>
                        Filtrar
                    </button>
                    <a href="{{ route('rh.folha-pagamento.resumo-geral') }}"
                        class="inline-flex justify-center items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                        Limpar
                    </a>
                </div>
            </form>
        </div>

        {{-- ── TABELA ──────────────────────────────────────────────── --}}
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200 overflow-hidden">

            {{-- Info de registros --}}
            <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
                <p class="text-sm text-gray-500">
                    Exibindo <span class="font-semibold text-gray-900">{{ $folhas->firstItem() ?? 0 }}</span>
                    a <span class="font-semibold text-gray-900">{{ $folhas->lastItem() ?? 0 }}</span>
                    de <span class="font-semibold text-gray-900">{{ $folhas->total() }}</span> registros
                </p>
                @if (request('competencia'))
                    <span
                        class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-0.5 text-xs font-medium text-indigo-700 ring-1 ring-indigo-600/20">
                        {{ \Carbon\Carbon::parse(request('competencia'))->translatedFormat('F/Y') }}
                    </span>
                @endif
            </div>

            {{-- Scroll horizontal --}}
            <div class="overflow-x-auto">
                <table class="min-w-full text-xs">

                    {{-- THEAD --}}
                    <thead>
                        <tr class="bg-indigo-700 text-white">
                            <th
                                class="sticky left-0 z-10 bg-indigo-700 whitespace-nowrap px-3 py-3 text-left font-semibold uppercase tracking-wide">
                                Local de Trabalho
                            </th>
                            <th class="whitespace-nowrap px-3 py-3 text-left font-semibold uppercase tracking-wide">
                                Contratação
                            </th>
                            <th class="whitespace-nowrap px-3 py-3 text-center font-semibold uppercase tracking-wide">
                                Funcionários
                            </th>
                            <th class="whitespace-nowrap px-3 py-3 text-left font-semibold uppercase tracking-wide">
                                Função
                            </th>
                            <th class="whitespace-nowrap px-3 py-3 text-right font-semibold uppercase tracking-wide">
                                Salário Real
                            </th>
                            <th class="whitespace-nowrap px-3 py-3 text-right font-semibold uppercase tracking-wide">
                                Salário
                            </th>
                            <th class="whitespace-nowrap px-3 py-3 text-right font-semibold uppercase tracking-wide">
                                Dia 20 Vale
                            </th>
                            <th class="whitespace-nowrap px-3 py-3 text-right font-semibold uppercase tracking-wide">
                                Desc. INSS
                            </th>
                            <th class="whitespace-nowrap px-3 py-3 text-right font-semibold uppercase tracking-wide">
                                Vale Extra
                            </th>
                            <th class="whitespace-nowrap px-3 py-3 text-right font-semibold uppercase tracking-wide">
                                Faltas
                            </th>
                            <th class="whitespace-nowrap px-3 py-3 text-right font-semibold uppercase tracking-wide">
                                D.S.R Faltas
                            </th>
                            <th class="whitespace-nowrap px-3 py-3 text-right font-semibold uppercase tracking-wide">
                                Arred. Desc.
                            </th>
                            <th class="whitespace-nowrap px-3 py-3 text-right font-semibold uppercase tracking-wide">
                                Arred. Prov.
                            </th>
                            <th class="whitespace-nowrap px-3 py-3 text-right font-semibold uppercase tracking-wide">
                                Gratif./Feriado
                            </th>
                            <th class="whitespace-nowrap px-3 py-3 text-right font-semibold uppercase tracking-wide">
                                DSR Hr Extra
                            </th>
                            <th class="whitespace-nowrap px-3 py-3 text-right font-semibold uppercase tracking-wide">
                                Sal. Família + Hr Extra
                            </th>
                            <th class="whitespace-nowrap px-3 py-3 text-center font-semibold uppercase tracking-wide">
                                5º Dia Útil
                            </th>
                        </tr>
                    </thead>

                    {{-- TBODY --}}
                    <tbody class="divide-y divide-gray-100">
                        {{-- @dd($folhas) --}}
                        @forelse($folhas as $k => $folha)
                            @php
                                $func = $folha->funcionario;
                            @endphp
                            <tr
                                class="hover:bg-indigo-50/40 transition-colors
                            {{ $loop->even ? 'bg-gray-50/60' : 'bg-white' }}">

                                {{-- Local de Trabalho --}}
                                <td
                                    class="sticky left-0 z-10 whitespace-nowrap px-3 py-2.5 font-medium text-gray-900
                                {{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                                    {{ $func->local_trabalho ?? '—' }}
                                </td>

                                {{-- Contratação --}}
                                <td class="whitespace-nowrap px-3 py-2.5 text-gray-600">
                                    {{ $func->data_admissao ? \Carbon\Carbon::parse($func->data_admissao)->format('d/m/Y') : '—' }}
                                </td>

                                {{-- Funcionário --}}
                                <td class="whitespace-nowrap px-3 py-2.5 text-center">
                                    <div class="font-semibold text-gray-900">{{ $func->nome_completo ?? '—' }}</div>
                                    @if ($func?->cpf)
                                        <div class="text-gray-400 text-[10px]">
                                            {{ substr($func->cpf, 0, 3) }}.***.***-{{ substr($func->cpf, -2) }}
                                        </div>
                                    @endif
                                </td>

                                {{-- Função --}}
                                <td class="whitespace-nowrap px-3 py-2.5 text-gray-600">
                                    {{ $func->cargo->titulo ?? '—' }}
                                </td>

                                {{-- Salário Real --}}
                                <td class="whitespace-nowrap px-3 py-2.5 text-right font-medium text-gray-900">
                                    R$ {{ number_format($folha->salario_base ?? 0, 2, ',', '.') }}
                                </td>

                                {{-- Salário Base --}}
                                <td class="whitespace-nowrap px-3 py-2.5 text-right text-gray-700">
                                    R$ {{ number_format($folha->salario_base ?? 0, 2, ',', '.') }}
                                </td>


                                {{-- Dia 20 Vale --}}
                                <td class="whitespace-nowrap px-3 py-2.5 text-right text-gray-700">

                                    @forelse ($folha->lancamentos as $lanc)
                                        @if ($lanc->tipo == 'vale_dia_20')
                                            <span class="text-amber-600 font-medium">
                                                R$ {{ number_format($lanc->valor_unitario, 2, ',', '.') }}
                                            </span>
                                        @endif

                                    @empty
                                        <span class="text-gray-400">—</span>
                                    @endforelse
                                </td>
                                {{-- INSS --}}
                                <td class="whitespace-nowrap px-3 py-2.5 text-right">

                                    @forelse ($folha->lancamentos as $lanc)
                                        @if ($lanc->tipo == 'inss')
                                            <span class="text-amber-600 font-medium">
                                                R$ {{ number_format($lanc->valor_unitario, 2, ',', '.') }}
                                            </span>
                                        @endif

                                    @empty
                                        <span class="text-gray-400">—</span>
                                    @endforelse
                                </td>

                                {{-- Vale Extra --}}
                                <td class="whitespace-nowrap px-3 py-2.5 text-right">

                                    @forelse ($folha->lancamentos as $lanc)
                                        @if ($lanc->tipo == 'vale_extra')
                                            <span class="text-amber-600 font-medium">
                                                R$ {{ number_format($lanc->valor_unitario, 2, ',', '.') }}
                                            </span>
                                        @endif

                                    @empty
                                        <span class="text-gray-400">—</span>
                                    @endforelse
                                </td>

                                {{-- Faltas --}}
                                <td class="whitespace-nowrap px-3 py-2.5 text-right">
                                    @forelse ($folha->lancamentos as $lanc)
                                        @if ($lanc->tipo == 'falta')
                                            <span class="text-amber-600 font-medium">
                                                R$ {{ number_format($lanc->valor_unitario, 2, ',', '.') }}
                                            </span>
                                        @endif

                                    @empty
                                        <span class="text-gray-400">—</span>
                                    @endforelse
                                </td>

                                {{-- D.S.R Faltas --}}
                                <td class="whitespace-nowrap px-3 py-2.5 text-right">
                                    @forelse ($folha->lancamentos as $lanc)
                                        @if ($lanc->tipo == 'dsr_falta')
                                            <span class="text-amber-600 font-medium">
                                                R$ {{ number_format($lanc->valor_unitario, 2, ',', '.') }}
                                            </span>
                                        @endif

                                    @empty
                                        <span class="text-gray-400">—</span>
                                    @endforelse

                                </td>

                                {{-- Arred. Desc. Folha --}}
                                <td class="whitespace-nowrap px-3 py-2.5 text-right">
                                    @forelse ($folha->lancamentos as $lanc)
                                        @if ($lanc->tipo == 'arredondamento')
                                            <span class="text-amber-600 font-medium">
                                                R$ {{ number_format($lanc->valor_unitario, 2, ',', '.') }}
                                            </span>
                                        @endif

                                    @empty
                                        <span class="text-gray-400">—</span>
                                    @endforelse

                                </td>

                                {{-- Arred. Prov. Folha --}}
                                <td class="whitespace-nowrap px-3 py-2.5 text-right">
                                    @if (($folha->arredondamento_provento ?? 0) != 0)
                                        <span class="text-green-600 font-medium">
                                            R$ {{ number_format($folha->arredondamento_provento, 2, ',', '.') }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>

                                {{-- Gratificação/Feriado --}}
                                <td class="whitespace-nowrap px-3 py-2.5 text-right">


                                    @forelse ($folha->lancamentos as $lanc)
                                        @if ($lanc->tipo == 'gratificacao')
                                            <span class="text-green-600 font-medium">
                                                R$ {{ number_format($lanc->valor_unitario, 2, ',', '.') }}
                                            </span>
                                        @endif

                                    @empty
                                        <span class="text-gray-400">—</span>
                                    @endforelse
                                </td>

                                {{-- DSR Hora Extra --}}
                                <td class="whitespace-nowrap px-3 py-2.5 text-right">

                                    @forelse ($folha->lancamentos as $lanc)
                                        @if ($lanc->tipo == 'dsr_hora_extra')
                                            <span class="text-green-600 font-medium">
                                                R$ {{ number_format($lanc->valor_unitario, 2, ',', '.') }}
                                            </span>
                                        @endif

                                    @empty
                                        <span class="text-gray-400">—</span>
                                    @endforelse
                                </td>

                                {{-- Salário Família + Hr Extra --}}
                                <td class="whitespace-nowrap px-3 py-2.5 text-right">
                                    @if (($folha->salario_familia_hr_extra ?? 0) > 0)
                                        <span class="text-green-600 font-medium">
                                            R$ {{ number_format($folha->salario_familia_hr_extra, 2, ',', '.') }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>

                                {{-- 5º Dia Útil --}}
                                <td class="whitespace-nowrap px-3 py-2.5 text-center">
                                    @if ($folha->quinto_dia_util)
                                        <span
                                            class="inline-flex items-center rounded-full bg-indigo-50 px-2 py-0.5 text-xs font-medium text-indigo-700 ring-1 ring-indigo-600/20">
                                            {{ \Carbon\Carbon::parse($folha->quinto_dia_util)->format('d/m/Y') }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="17" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center gap-3 text-gray-400">
                                        <svg class="h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                                        </svg>
                                        <p class="text-sm font-medium">Nenhum registro encontrado</p>
                                        <p class="text-xs">Tente ajustar os filtros acima</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                    {{-- TFOOT — TOTALIZADORES --}}
                    @if ($folhas->count() > 0)
                        <tfoot>
                            <tr class="bg-indigo-900 text-white font-bold text-xs">
                                <td
                                    class="sticky left-0 z-10 bg-indigo-900 whitespace-nowrap px-3 py-3 uppercase tracking-wide">
                                    TOTAIS
                                </td>
                                <td class="px-3 py-3">—</td>
                                <td class="px-3 py-3 text-center">
                                    {{ $totais->total_funcionarios ?? 0 }} func.
                                </td>
                                <td class="px-3 py-3">—</td>
                                {{-- Salário Real --}}
                                <td class="px-3 py-3 text-right">
                                    R$ {{ number_format($totais->total_salario_real ?? 0, 2, ',', '.') }}
                                </td>
                                {{-- Salário Base --}}
                                <td class="px-3 py-3 text-right">
                                    R$ {{ number_format($totais->total_salario_base ?? 0, 2, ',', '.') }}
                                </td>
                                {{-- Vale Dia 20 --}}
                                <td class="px-3 py-3 text-right">
                                    R$ {{ number_format($totais->total_vale_dia_20 ?? 0, 2, ',', '.') }}
                                </td>
                                {{-- INSS --}}
                                <td class="px-3 py-3 text-right">
                                    R$ {{ number_format($totais->total_desconto_inss ?? 0, 2, ',', '.') }}
                                </td>
                                {{-- Vale Extra --}}
                                <td class="px-3 py-3 text-right">
                                    R$ {{ number_format($totais->total_vale_extra ?? 0, 2, ',', '.') }}
                                </td>
                                {{-- Faltas --}}
                                <td class="px-3 py-3 text-right">
                                    {{ $totais->total_faltas ?? 0 }}d
                                </td>
                                {{-- DSR Faltas --}}
                                <td class="px-3 py-3 text-right">
                                    R$ {{ number_format($totais->total_dsr_faltas ?? 0, 2, ',', '.') }}
                                </td>
                                {{-- Arred. Desc --}}
                                <td class="px-3 py-3 text-right">
                                    R$ {{ number_format($totais->total_arred_desconto ?? 0, 2, ',', '.') }}
                                </td>
                                {{-- Arred. Prov --}}
                                <td class="px-3 py-3 text-right">
                                    R$ {{ number_format($totais->total_arred_provento ?? 0, 2, ',', '.') }}
                                </td>
                                {{-- Gratificação --}}
                                <td class="px-3 py-3 text-right">
                                    R$ {{ number_format($totais->total_gratificacao ?? 0, 2, ',', '.') }}
                                </td>
                                {{-- DSR Hr Extra --}}
                                <td class="px-3 py-3 text-right">
                                    R$ {{ number_format($totais->total_dsr_hora_extra ?? 0, 2, ',', '.') }}
                                </td>
                                {{-- Sal. Família + Hr Extra --}}
                                <td class="px-3 py-3 text-right">
                                    R$ {{ number_format($totais->total_sal_familia_hr_extra ?? 0, 2, ',', '.') }}
                                </td>
                                {{-- 5º Dia Útil --}}
                                <td class="px-3 py-3 text-center">
                                    @if ($totais->quinto_dia_util)
                                        {{ \Carbon\Carbon::parse($totais->quinto_dia_util)->format('d/m/Y') }}
                                    @else
                                        —
                                    @endif
                                </td>
                                {{-- Total Proventos --}}
                                <td class="px-3 py-3 text-right text-green-300">
                                    R$ {{ number_format($totais->total_proventos ?? 0, 2, ',', '.') }}
                                </td>

                                {{-- Total Descontos --}}
                                <td class="px-3 py-3 text-right text-red-300">
                                    R$ {{ number_format($totais->total_descontos ?? 0, 2, ',', '.') }}
                                </td>

                                {{-- Salário Líquido Total --}}
                                <td class="px-3 py-3 text-right text-yellow-300">
                                    R$ {{ number_format($totais->total_salario_liquido ?? 0, 2, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    @endif

                </table>
            </div>

            {{-- PAGINAÇÃO --}}
            @if ($folhas->hasPages())
                <div class="border-t border-gray-100 px-5 py-4">
                    {{ $folhas->links() }}
                </div>
            @endif

        </div>
        {{-- /TABELA --}}

    </div>
@endsection
