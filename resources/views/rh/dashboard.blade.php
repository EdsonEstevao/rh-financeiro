{{-- resources/views/rh/dashboard/index.blade.php --}}
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
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

                {{-- Cards de Estatísticas Gerais --}}
                <div class="grid grid-cols-1 gap-6 mb-8 md:grid-cols-2 lg:grid-cols-4">
                    {{-- Total de Funcionários --}}
                    <div class="overflow-hidden bg-white rounded-lg shadow-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-lg">
                                        <svg class="text-blue-600 w-7 h-7" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z">
                                            </path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1 ml-4">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">
                                            Total de Funcionários
                                        </dt>
                                        <dd class="text-3xl font-bold text-gray-900">
                                            {{ number_format($totalFuncionarios) }}
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Novos Funcionários --}}
                    <div class="overflow-hidden bg-white rounded-lg shadow-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="flex items-center justify-center w-12 h-12 bg-green-100 rounded-lg">
                                        <svg class="text-green-600 w-7 h-7" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z">
                                            </path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1 ml-4">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">
                                            Novos Este Mês
                                        </dt>
                                        <dd class="text-3xl font-bold text-gray-900">
                                            {{ number_format($novosFuncionariosEsseMes) }}
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Departamentos --}}
                    <div class="overflow-hidden bg-white rounded-lg shadow-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="flex items-center justify-center w-12 h-12 bg-yellow-100 rounded-lg">
                                        <svg class="text-yellow-600 w-7 h-7" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                            </path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1 ml-4">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">
                                            Departamentos
                                        </dt>
                                        <dd class="text-3xl font-bold text-gray-900">
                                            {{ number_format($totalDepartamentos) }}
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Cargos --}}
                    <div class="overflow-hidden bg-white rounded-lg shadow-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="flex items-center justify-center w-12 h-12 bg-purple-100 rounded-lg">
                                        <svg class="text-purple-600 w-7 h-7" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m8 0H8m8 0v2a2 2 0 01-2 2H10a2 2 0 01-2-2V8m8 0V6a2 2 0 00-2-2H10a2 2 0 00-2 2v2">
                                            </path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1 ml-4">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">
                                            Cargos Ativos
                                        </dt>
                                        <dd class="text-3xl font-bold text-gray-900">
                                            {{ number_format($totalCargos) }}
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Segunda linha de gráficos --}}
                <div class="grid grid-cols-1 gap-6 mb-8 lg:grid-cols-2">
                    {{-- Gráfico de Funcionários por Departamento --}}
                    <div class="p-6 bg-white rounded-lg shadow-lg">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Funcionários por Departamento</h3>
                        </div>
                        <div class="h-80">
                            <canvas id="departamentoChart"></canvas>
                        </div>
                    </div>

                    {{-- Gráfico de Distribuição Salarial --}}
                    <div class="p-6 bg-white rounded-lg shadow-lg">
                        <h3 class="mb-4 text-lg font-semibold text-gray-900">Distribuição Salarial</h3>
                        <div class="h-80">
                            <canvas id="salarioChart"></canvas>
                        </div>
                    </div>
                </div>

                {{-- Terceira linha --}}
                <div class="grid grid-cols-1 gap-6 mb-8 lg:grid-cols-3">
                    {{-- Aniversariantes do Mês --}}
                    <div class="p-6 bg-white rounded-lg shadow-lg">
                        <h3 class="mb-4 text-lg font-semibold text-gray-900">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 2a8 8 0 100 16 8 8 0 000-16zM8 9.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 9.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM10 11.5a1 1 0 100 2h.01a1 1 0 100-2H10z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                Aniversariantes do Mês ({{ $aniversariantesDoMes->count() }})
                            </div>
                        </h3>
                        <div class="space-y-3 overflow-y-auto max-h-64">
                            @forelse($aniversariantesDoMes as $funcionario)
                                <div class="flex items-center p-2 space-x-3 rounded hover:bg-gray-50">
                                    <div class="flex items-center justify-center w-10 h-10 bg-yellow-100 rounded-full">
                                        <span class="text-sm font-medium text-yellow-700">
                                            {{ substr($funcionario->nome_completo, 0, 2) }}
                                        </span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">
                                            {{ $funcionario->nome_completo }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            {{ Carbon\Carbon::parse($funcionario->data_nascimento)->format('d/m') }}
                                            ({{ Carbon\Carbon::parse($funcionario->data_nascimento)->age }} anos)
                                        </p>
                                    </div>
                                </div>
                            @empty
                                <div class="py-8 text-center text-gray-500">
                                    <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 4l6 6m0-6l-6 6"></path>
                                    </svg>
                                    <p class="mt-2 text-sm">Nenhum aniversariante este mês</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Próximas Férias --}}
                    <div class="p-6 bg-white rounded-lg shadow-lg">
                        <h3 class="mb-4 text-lg font-semibold text-gray-900">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                Próximas Férias ({{ $proximasFerias->count() }})
                            </div>
                        </h3>
                        <div class="space-y-3 overflow-y-auto max-h-64">
                            @forelse($proximasFerias as $ferias)
                                <div class="flex items-center p-2 space-x-3 rounded hover:bg-gray-50">
                                    <div class="flex items-center justify-center w-10 h-10 bg-blue-100 rounded-full">
                                        <span class="text-sm font-medium text-blue-700">
                                            {{ substr($ferias->funcionario->nome_completo, 0, 2) }}
                                        </span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">
                                            {{ $ferias->funcionario->nome_completo }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            {{ $ferias->data_inicio->format('d/m') }} -
                                            {{ $ferias->data_fim->format('d/m/Y') }}
                                            ({{ $ferias->diasCorridos() }} dias)
                                        </p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                        {{ $ferias->status === 'aprovada' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ ucfirst($ferias->status) }}
                                        </span>
                                    </div>
                                </div>

                                {{-- ...continuação do arquivo --}}

                            @empty
                                <div class="py-8 text-center text-gray-500">
                                    <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 4l6 6m0-6l-6 6" />
                                    </svg>
                                    <p class="mt-2 text-sm">Nenhuma férias programada para os próximos 30 dias</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Status dos Funcionários --}}
                    <div class="p-6 bg-white rounded-lg shadow-lg">
                        <h3 class="mb-4 text-lg font-semibold text-gray-900">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Status dos Funcionários
                            </div>
                        </h3>

                        <div class="space-y-4">
                            @php
                                $statusLabels = [
                                    'ativo' => 'Ativos',
                                    'inativo' => 'Inativos',
                                    'ferias' => 'Em férias',
                                ];

                                $statusColors = [
                                    'ativo' => 'bg-green-500',
                                    'inativo' => 'bg-red-500',
                                    'ferias' => 'bg-blue-500',
                                ];

                                $totalStatus = collect($funcionariosPorStatus)->sum() ?: 1;
                            @endphp

                            @foreach ($funcionariosPorStatus as $status => $count)
                                @php
                                    $pct = round(($count / $totalStatus) * 100, 1);
                                @endphp

                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="inline-block w-2.5 h-2.5 rounded-full {{ $statusColors[$status] ?? 'bg-gray-400' }}"></span>
                                            <span class="text-sm font-medium text-gray-700">
                                                {{ $statusLabels[$status] ?? ucfirst($status) }}
                                            </span>
                                        </div>
                                        <div class="text-sm text-gray-700">
                                            <span class="font-semibold">{{ $count }}</span>
                                            <span class="text-gray-500">({{ $pct }}%)</span>
                                        </div>
                                    </div>
                                    <div class="w-full h-2 bg-gray-200 rounded-full">
                                        <div class="h-2 rounded-full {{ $statusColors[$status] ?? 'bg-gray-400' }}"
                                            style="width: {{ $pct }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Quarta linha (2 gráficos) --}}
                <div class="grid grid-cols-1 gap-6 mb-8 lg:grid-cols-2">
                    {{-- Folha (bruto x líquido) --}}
                    <div class="p-6 bg-white rounded-lg shadow-lg">
                        <h3 class="mb-4 text-lg font-semibold text-gray-900">Folha (Bruto x Líquido) - últimos 6 meses</h3>
                        <div class="h-80">
                            <canvas id="folhaChart"></canvas>
                        </div>
                        <p class="mt-3 text-xs text-gray-500">
                            Considera apenas folhas com status <span class="font-medium">fechada</span> e soma via tabela
                            <span class="font-medium">holerites</span>.
                        </p>
                    </div>

                    {{-- Admissões vs Demissões --}}
                    <div class="p-6 bg-white rounded-lg shadow-lg">
                        <h3 class="mb-4 text-lg font-semibold text-gray-900">Admissões vs Demissões - últimos 12 meses</h3>
                        <div class="h-80">
                            <canvas id="admissoesChart"></canvas>
                        </div>
                    </div>
                </div>

                {{-- Quinta linha (3 gráficos menores) --}}
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                    {{-- Gênero --}}
                    <div class="p-6 bg-white rounded-lg shadow-lg">
                        <h3 class="mb-4 text-lg font-semibold text-gray-900">Funcionários por gênero</h3>
                        <div class="h-64">
                            <canvas id="generoChart"></canvas>
                        </div>
                    </div>

                    {{-- Faixa etária --}}
                    <div class="p-6 bg-white rounded-lg shadow-lg">
                        <h3 class="mb-4 text-lg font-semibold text-gray-900">Faixa etária</h3>
                        <div class="h-64">
                            <canvas id="idadeChart"></canvas>
                        </div>
                    </div>

                    {{-- Atividade (Activity Log) - opcional / placeholder --}}
                    {{-- <div class="p-6 bg-white rounded-lg shadow-lg">
                        <h3 class="mb-4 text-lg font-semibold text-gray-900">Ações recentes (auditoria)</h3>

                        <div class="space-y-2 text-sm text-gray-600">
                            <p>
                                Este bloco pode listar os últimos registros de
                                <span class="font-medium">activity_log</span> (Spatie Activitylog) relacionados a RH.
                            </p>

                            <p class="text-xs text-gray-500">
                                Se você quiser, eu implemento esse “feed” com filtro por <span
                                    class="font-medium">log_name</span> = <span class="font-medium">rh</span>.
                            </p>

                            <div class="p-3 mt-4 text-xs text-gray-600 border rounded bg-gray-50">
                                Próximo passo recomendado: criar um log_name padrão “rh” nos models de RH para facilitar
                                filtros no dashboard.
                            </div>
                        </div>
                    </div> --}}
                </div>

            </div>
        </div>
    @endsection
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                Chart.defaults.responsive = true;
                Chart.defaults.maintainAspectRatio = false;

                const palette = {
                    blue: '#3B82F6',
                    green: '#10B981',
                    red: '#EF4444',
                    yellow: '#F59E0B',
                    purple: '#8B5CF6',
                    cyan: '#06B6D4',
                    pink: '#EC4899',
                    lime: '#84CC16',
                    gray: '#6B7280'
                };

                // 1) Funcionários por Departamento (doughnut)
                const porDepto = @json($funcionariosPorDepartamento);
                const deptoCtx = document.getElementById('departamentoChart')?.getContext('2d');

                if (deptoCtx) {
                    new Chart(deptoCtx, {
                        type: 'doughnut',
                        data: {
                            labels: porDepto.map(i => i.name),
                            datasets: [{
                                data: porDepto.map(i => i.count),
                                backgroundColor: [
                                    palette.blue, palette.green, palette.yellow, palette.purple,
                                    palette.pink, palette.cyan, palette.lime, palette.red
                                ],
                                borderColor: '#fff',
                                borderWidth: 2
                            }]
                        },
                        options: {
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }
                    });
                }

                // 2) Distribuição Salarial (bar)
                const distSal = @json($distribuicaoSalarial);
                const salarioCtx = document.getElementById('salarioChart')?.getContext('2d');

                if (salarioCtx) {
                    new Chart(salarioCtx, {
                        type: 'bar',
                        data: {
                            labels: distSal.map(i => i.label),
                            datasets: [{
                                label: 'Funcionários',
                                data: distSal.map(i => i.value),
                                backgroundColor: 'rgba(59, 130, 246, 0.2)',
                                borderColor: palette.blue,
                                borderWidth: 1,
                                borderRadius: 6
                            }]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        precision: 0
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false
                                }
                            }
                        }
                    });
                }

                // 3) Folha Bruto x Líquido (line)
                const folha = @json($dadosFolhaPagamento);
                const folhaCtx = document.getElementById('folhaChart')?.getContext('2d');

                if (folhaCtx) {
                    new Chart(folhaCtx, {
                        type: 'line',
                        data: {
                            labels: folha.map(i => i.periodo),
                            datasets: [{
                                    label: 'Bruto',
                                    data: folha.map(i => i.total_bruto),
                                    borderColor: palette.blue,
                                    backgroundColor: 'rgba(59,130,246,0.12)',
                                    fill: true,
                                    tension: 0.35,
                                    pointRadius: 3
                                },
                                {
                                    label: 'Líquido',
                                    data: folha.map(i => i.total_liquido),
                                    borderColor: palette.green,
                                    backgroundColor: 'rgba(16,185,129,0.12)',
                                    fill: true,
                                    tension: 0.35,
                                    pointRadius: 3
                                }
                            ]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: (v) => 'R$ ' + Number(v).toLocaleString('pt-BR')
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                },
                                tooltip: {
                                    callbacks: {
                                        label: (ctx) =>
                                            `${ctx.dataset.label}: R$ ${Number(ctx.parsed.y).toLocaleString('pt-BR', {minimumFractionDigits: 2})}`
                                    }
                                }
                            }
                        }
                    });
                }

                // 4) Admissões vs Demissões (line)
                const adm = @json($admissoesVsDemissoes);
                const admCtx = document.getElementById('admissoesChart')?.getContext('2d');

                if (admCtx) {
                    new Chart(admCtx, {
                        type: 'line',
                        data: {
                            labels: adm.map(i => i.periodo),
                            datasets: [{
                                    label: 'Admissões',
                                    data: adm.map(i => i.admissoes),
                                    borderColor: palette.green,
                                    backgroundColor: 'rgba(16,185,129,0.10)',
                                    fill: false,
                                    tension: 0.35
                                },
                                {
                                    label: 'Demissões',
                                    data: adm.map(i => i.demissoes),
                                    borderColor: palette.red,
                                    backgroundColor: 'rgba(239,68,68,0.10)',
                                    fill: false,
                                    tension: 0.35
                                }
                            ]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1,
                                        precision: 0
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }
                    });
                }

                // 5) Funcionários por gênero (pie)
                const genero = @json($funcionariosPorGenero);
                const generoLabels = Object.keys(genero);
                const generoValues = Object.values(genero);
                const generoCtx = document.getElementById('generoChart')?.getContext('2d');

                if (generoCtx) {
                    new Chart(generoCtx, {
                        type: 'pie',
                        data: {
                            labels: generoLabels,
                            datasets: [{
                                data: generoValues,
                                backgroundColor: [palette.blue, palette.pink, palette.gray],
                                borderColor: '#fff',
                                borderWidth: 2
                            }]
                        },
                        options: {
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }
                    });
                }

                // 6) Faixa etária (bar)
                const idade = @json($funcionariosPorIdade);
                const idadeCtx = document.getElementById('idadeChart')?.getContext('2d');

                if (idadeCtx) {
                    new Chart(idadeCtx, {
                        type: 'bar',
                        data: {
                            labels: idade.map(i => i.label),
                            datasets: [{
                                label: 'Funcionários',
                                data: idade.map(i => i.value),
                                backgroundColor: 'rgba(139, 92, 246, 0.2)',
                                borderColor: palette.purple,
                                borderWidth: 1,
                                borderRadius: 6
                            }]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        precision: 0
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false
                                }
                            }
                        }
                    });
                }
            });
        </script>
    @endpush
