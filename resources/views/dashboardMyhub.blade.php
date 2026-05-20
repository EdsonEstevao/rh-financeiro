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
        <div class="py-6">
            <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">

                {{-- Cards --}}
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                    @php $t = $totais; @endphp

                    <div class="p-4 bg-white rounded-lg shadow">
                        <div class="text-sm text-gray-500">Usuários</div>
                        <div class="text-2xl font-bold">{{ number_format($t['users']) }}</div>
                        <div class="mt-1 text-xs text-gray-500">Online (10 min): {{ number_format($t['online_users']) }}
                        </div>
                    </div>

                    <div class="p-4 bg-white rounded-lg shadow">
                        <div class="text-sm text-gray-500">Funcionários</div>
                        <div class="text-2xl font-bold">{{ number_format($t['func_ativos']) }}</div>
                        <div class="mt-1 text-xs text-gray-500">Inativos: {{ number_format($t['func_inativos']) }}</div>
                    </div>

                    <div class="p-4 bg-white rounded-lg shadow">
                        <div class="text-sm text-gray-500">Folha (mês)</div>
                        <div class="text-2xl font-bold">{{ number_format($t['folhas_fechadas_mes']) }}</div>
                        <div class="mt-1 text-xs text-gray-500">Abertas: {{ number_format($t['folhas_abertas_mes']) }}</div>
                    </div>

                    <div class="p-4 bg-white rounded-lg shadow">
                        <div class="text-sm text-gray-500">Auditoria</div>
                        <div class="text-2xl font-bold">{{ number_format($t['logs_24h']) }}</div>
                        <div class="mt-1 text-xs text-gray-500">Eventos nas últimas 24h</div>
                    </div>
                </div>

                {{-- Segunda linha --}}
                <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
                    <div class="p-4 bg-white rounded-lg shadow">
                        <div class="text-sm text-gray-500">Férias (próx. 30 dias)</div>
                        <div class="text-2xl font-bold">{{ number_format($t['ferias_30d']) }}</div>
                    </div>

                    <div class="p-4 bg-white rounded-lg shadow">
                        <div class="text-sm text-gray-500">Jobs falhos</div>
                        <div class="text-2xl font-bold">{{ number_format($t['failed_jobs']) }}</div>
                    </div>

                    <div class="p-4 bg-white rounded-lg shadow">
                        <div class="text-sm text-gray-500">Cache Locks</div>
                        <div class="text-2xl font-bold">{{ number_format($t['cache_locks']) }}</div>
                        <div class="mt-1 text-xs text-gray-500">Indicador (depende do uso de locks)</div>
                    </div>
                </div>

                {{-- Top eventos do activity log --}}
                <div class="p-4 bg-white rounded-lg shadow">
                    <h3 class="mb-3 text-lg font-semibold text-gray-900">Top eventos (últimos 7 dias)</h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left text-gray-500">
                                    <th class="py-2 pr-4">Evento</th>
                                    <th class="py-2 pr-4">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @forelse($charts['top_eventos_log_7d'] as $row)
                                    <tr>
                                        <td class="py-2 pr-4 text-gray-800">{{ $row->event ?? '—' }}</td>
                                        <td class="py-2 pr-4 font-semibold">{{ number_format($row->total) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="py-3 text-gray-500" colspan="2">Sem dados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
