@extends('layouts.app')


@section('content')
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- ─── HEADER ──────────────────────────────────────────────── --}}
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Detalhes da Folha de Pagamento</h1>
                <p class="mt-1 text-sm text-gray-600">
                    {{ $folhaPagamento->funcionario->nome_completo ?? '—' }} —
                    {{ \Carbon\Carbon::parse($folhaPagamento->competencia)->translatedFormat('F/Y') }}
                </p>
            </div>
            <div class="flex items-center gap-3">
                {{-- PDF --}}
                <a href="{{ route('rh.folha-pagamento.pdf', ['folhaPagamento' => $folhaPagamento->competencia->format('Y-m')]) }}"
                    target="_blank"
                    class="inline-flex items-center px-4 py-2 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50 shadow-sm">
                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    PDF
                </a>
                {{-- Editar --}}
                @can('folha-pagamento.edit')
                    <a href="{{ route('rh.folha-pagamento.edit', $folhaPagamento) }}"
                        class="inline-flex items-center px-4 py-2 border border-yellow-400 text-sm font-medium rounded-md text-yellow-700 bg-white hover:bg-yellow-50 shadow-sm">
                        <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Editar
                    </a>
                @endcan
                {{-- Voltar --}}
                <a href="{{ route('rh.folha-pagamento.index') }}"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 shadow-sm">
                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Voltar
                </a>
            </div>
        </div>

        {{-- ─── CARD: IDENTIFICAÇÃO ─────────────────────────────────── --}}
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-900 flex items-center gap-2">
                    <svg class="h-5 w-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Identificação
                </h3>
                {{-- Badge Status --}}
                @php
                    $badge = match ($folhaPagamento->status) {
                        'pago' => 'bg-green-100 text-green-800 border-green-200',
                        'processado' => 'bg-blue-100 text-blue-800 border-blue-200',
                        default => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                    };
                    $label = match ($folhaPagamento->status) {
                        'pago' => 'Pago',
                        'processado' => 'Processado',
                        default => 'Pendente',
                    };
                @endphp
                <span
                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold border {{ $badge }}">
                    {{ $label }}
                </span>
            </div>

            <div class="px-6 py-5 grid grid-cols-2 sm:grid-cols-4 gap-6">
                <div>
                    <p class="text-xs text-gray-500 uppercase font-medium">Funcionário</p>
                    <p class="mt-1 text-sm font-semibold text-gray-900">
                        {{ $folhaPagamento->funcionario->nome_completo ?? '—' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-medium">Competência</p>
                    <p class="mt-1 text-sm font-semibold text-gray-900">
                        {{ \Carbon\Carbon::parse($folhaPagamento->competencia)->translatedFormat('F/Y') }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-medium">Quinto Dia Útil</p>
                    <p class="mt-1 text-sm font-semibold text-gray-900">
                        {{ $folhaPagamento->quinto_dia_util
                            ? \Carbon\Carbon::parse($folhaPagamento->quinto_dia_util)->format('d/m/Y')
                            : '—' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-medium">Criado em</p>
                    <p class="mt-1 text-sm font-semibold text-gray-900">
                        {{ $folhaPagamento->created_at->format('d/m/Y H:i') }}
                    </p>
                </div>
            </div>
        </div>

        {{-- ─── GRID: PROVENTOS + DESCONTOS ───────────────────────── --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

            {{-- PROVENTOS --}}
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-green-50">
                    <h3 class="text-base font-semibold text-green-800 flex items-center gap-2">
                        <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Proventos
                    </h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @php
                        $proventos = [
                            'Salário Base' => $folhaPagamento->salario_base,
                            'Gratificação Feriado' => $folhaPagamento->gratificacao_feriado,
                            'DSR Hora Extra' => $folhaPagamento->horas_extras_totais,
                            'Salário Família / Hr Extra' => $folhaPagamento->salario_familia_hr_extra,
                            'Arredondamento (Provento)' => $folhaPagamento->arredondamento_provento,
                        ];
                    @endphp
                    @foreach ($proventos as $label => $valor)
                        <div class="px-6 py-3 flex justify-between text-sm">
                            <span class="text-gray-600">{{ $label }}</span>
                            <span class="font-medium text-gray-900">
                                R$ {{ number_format($valor ?? 0, 2, ',', '.') }}
                            </span>
                        </div>
                    @endforeach
                    {{-- Total Proventos --}}
                    <div class="px-6 py-4 flex justify-between bg-green-50">
                        <span class="text-sm font-bold text-green-800">Total Proventos</span>
                        <span class="text-base font-bold text-green-800">
                            R$ {{ number_format($folhaPagamento->total_proventos, 2, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- DESCONTOS --}}
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-red-50">
                    <h3 class="text-base font-semibold text-red-800 flex items-center gap-2">
                        <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                        </svg>
                        Descontos
                    </h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @php
                        $descontos = [
                            'Desconto INSS' => $folhaPagamento->desconto_inss,
                            'Vale Dia 20' => $folhaPagamento->vale_dia_20,
                            'Vale Extra' => $folhaPagamento->vale_extra,
                            'Faltas (Valor)' => $folhaPagamento->faltas_valor,
                            'DSR Faltas' => $folhaPagamento->dsr_faltas,
                            'Arredondamento (Desconto)' => $folhaPagamento->arredondamento_desconto,
                        ];
                    @endphp
                    @foreach ($descontos as $label => $valor)
                        <div class="px-6 py-3 flex justify-between text-sm">
                            <span class="text-gray-600">{{ $label }}</span>
                            <span class="font-medium text-gray-900">
                                R$ {{ number_format($valor ?? 0, 2, ',', '.') }}
                            </span>
                        </div>
                    @endforeach
                    {{-- Total Descontos --}}
                    <div class="px-6 py-4 flex justify-between bg-red-50">
                        <span class="text-sm font-bold text-red-800">Total Descontos</span>
                        <span class="text-base font-bold text-red-800">
                            R$ {{ number_format($folhaPagamento->total_descontos, 2, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>

        </div>

        {{-- ─── CARD: SALÁRIO LÍQUIDO ───────────────────────────────── --}}
        <div class="bg-indigo-700 shadow rounded-lg px-6 py-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-indigo-200 text-sm font-medium uppercase">Salário Líquido a Receber</p>
                    <p class="text-white text-xs mt-0.5">
                        Referente a
                        {{ \Carbon\Carbon::parse($folhaPagamento->competencia)->translatedFormat('F \d\e Y') }}
                    </p>
                </div>
                <p class="text-4xl font-bold text-white">
                    R$ {{ number_format($folhaPagamento->salario_liquido, 2, ',', '.') }}
                </p>
            </div>
        </div>

        {{-- ─── CARD: OBSERVAÇÃO ───────────────────────────────────── --}}
        @if ($folhaPagamento->observacao)
            <div class="bg-white shadow rounded-lg mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-base font-semibold text-gray-700">Observação</h3>
                </div>
                <div class="px-6 py-5">
                    <p class="text-sm text-gray-600 whitespace-pre-line">{{ $folhaPagamento->observacao }}</p>
                </div>
            </div>
        @endif

        {{-- ─── CARD: LOG DE ATIVIDADES ────────────────────────────── --}}
        @if ($folhaPagamento?->activities?->isNotEmpty() ?? [])
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-base font-semibold text-gray-700 flex items-center gap-2">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Histórico de Alterações
                    </h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @foreach ($folhaPagamento->activities->sortByDesc('created_at') as $log)
                        <div class="px-6 py-3 flex items-start gap-3">
                            <div class="flex-shrink-0 mt-0.5">
                                <span
                                    class="inline-flex items-center justify-center h-7 w-7 rounded-full bg-indigo-100 text-indigo-600 text-xs font-bold">
                                    {{ strtoupper(substr($log->causer?->name ?? 'S', 0, 1)) }}
                                </span>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-800">{{ $log->description }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    {{ $log->causer?->name ?? 'Sistema' }} —
                                    {{ $log->created_at->format('d/m/Y H:i') }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
@endsection
