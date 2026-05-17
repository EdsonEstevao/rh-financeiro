{{-- resources/views/rh/folha-pagamento/create.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="folhaPagamentoForm()">

        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Nova Folha de Pagamento</h1>
                <p class="mt-1 text-sm text-gray-600">Preencha apenas os dados variáveis. O resto é calculado
                    automaticamente!</p>
            </div>
            <a href="{{ route('rh.folha-pagamento.index') }}"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50">
                ← Voltar
            </a>
        </div>

        <form action="{{ route('rh.folha-pagamento.store') }}" method="POST">
            @csrf

            <div class="space-y-6">

                {{-- 1. FUNCIONÁRIO --}}
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">👤 Selecione o Funcionário</h2>

                    <div class="relative" @click.away="showDropdown = false">
                        <input type="text" x-model="searchTerm" @input="searchFuncionario()" @focus="showDropdown = true"
                            @keydown.escape="showDropdown = false" @keydown.arrow-down.prevent="highlightNext()"
                            @keydown.arrow-up.prevent="highlightPrev()" @keydown.enter.prevent="selectHighlighted()"
                            placeholder="Digite o nome do funcionário..."
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 pl-10">

                        <div x-show="showDropdown && searchResults.length > 0" x-transition
                            class="absolute z-50 w-full mt-1 bg-white rounded-md shadow-lg border max-h-60 overflow-auto">
                            <template x-for="(func, index) in searchResults" :key="func.id">
                                <div @click="selectFuncionario(func)" @mouseenter="highlightedIndex = index"
                                    :class="{ 'bg-indigo-50': index === highlightedIndex }"
                                    class="px-4 py-3 cursor-pointer hover:bg-indigo-50">
                                    <p class="font-medium" x-text="func.nome_completo"></p>
                                    <p class="text-sm text-gray-500">
                                        <span x-text="func.cargo || 'Sem cargo'"></span> •
                                        <span x-text="formatMoney(func.salario_base)"></span>
                                    </p>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Card do funcionário selecionado --}}
                    <div x-show="funcionario.id" x-transition
                        class="mt-4 bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500">Nome:</span>
                                <p class="font-semibold" x-text="funcionario.nome_completo"></p>
                            </div>
                            <div>
                                <span class="text-gray-500">Cargo:</span>
                                <p class="font-semibold" x-text="funcionario.cargo || 'Não informado'"></p>
                            </div>
                            <div>
                                <span class="text-gray-500">Salário Base:</span>
                                <p class="font-semibold text-green-700" x-text="formatMoney(funcionario.salario_base)"></p>
                            </div>
                            <div>
                                <span class="text-gray-500">Carga Horária:</span>
                                <p class="font-semibold" x-text="(funcionario.carga_horaria_semanal || 44) + 'h semanais'">
                                </p>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="funcionario_id" x-model="funcionario.id">
                </div>

                {{-- 2. COMPETÊNCIA --}}
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">📅 Competência</h2>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Mês/Ano</label>
                        <input type="month" name="competencia" x-model="competencia" @change="calcularDiasUteis()"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    {{-- Info jornada --}}
                    <div x-show="diasUteis > 0" class="mt-4 bg-blue-50 rounded-lg p-4">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            <div>
                                <span class="text-blue-600">Dias Úteis:</span>
                                <span class="font-semibold" x-text="diasUteis"></span>
                            </div>
                            <div>
                                <span class="text-blue-600">Domingos/Feriados:</span>
                                <span class="font-semibold" x-text="domingosFeriados"></span>
                            </div>
                            <div>
                                <span class="text-blue-600">Valor Hora Normal:</span>
                                <span class="font-semibold" x-text="formatMoney(valorHoraNormal)"></span>
                            </div>
                            <div>
                                <span class="text-blue-600">Valor Hora Extra:</span>
                                <span class="font-semibold" x-text="formatMoney(valorHoraExtra)"></span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 3. VARIÁVEIS MENSAIS (ÚNICOS CAMPOS QUE O USUÁRIO PRECISA PREENCHER) --}}
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">📝 Dados Variáveis do Mês</h2>
                    <p class="text-sm text-gray-500 mb-4">Preencha apenas o que for diferente do padrão</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">⏰ Horas Extras (total mês)</label>
                            <input type="number" name="horas_extras_totais" x-model.number="form.horas_extras_totais"
                                step="0.5" min="0" value="0"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">📉 Faltas (dias)</label>
                            <input type="number" name="faltas_dias" x-model.number="form.faltas_dias" step="0.5"
                                min="0" value="0"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">🎁 Gratificação Extra</label>
                            <input type="number" name="gratificacao_feriado" x-model.number="form.gratificacao_feriado"
                                step="0.01" min="0" value="0" placeholder="0,00"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="flex items-center cursor-pointer mt-6">
                                <input type="hidden" name="eh_diarista" value="0">
                                <input type="checkbox" name="eh_diarista" value="1" x-model="form.eh_diarista"
                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700">É Diarista?</span>
                            </label>
                            <div x-show="form.eh_diarista" class="mt-2">
                                <input type="number" name="valor_diaria" x-model.number="form.valor_diaria"
                                    step="0.01" placeholder="Valor da diária"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 4. RESUMO DOS CÁLCULOS (TUDO AUTOMÁTICO) --}}
                <div x-show="funcionario.id && competencia" class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">📊 Resumo dos Cálculos (Automático)</h2>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        {{-- Proventos --}}
                        <div class="bg-green-50 rounded-lg p-4">
                            <h3 class="font-semibold text-green-800 mb-3">💵 Proventos</h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span>Salário Base:</span>
                                    <span x-text="formatMoney(form.salario_base)"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Horas Extras:</span>
                                    <span x-text="formatMoney(totalHorasExtras)"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span>DSR Hora Extra:</span>
                                    <span x-text="formatMoney(dsrHoraExtra)"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Gratificação:</span>
                                    <span x-text="formatMoney(form.gratificacao_feriado)"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Salário Família:</span>
                                    <span x-text="formatMoney(salarioFamilia)"></span>
                                </div>
                                <hr class="border-green-300">
                                <div class="flex justify-between font-bold text-green-800">
                                    <span>Total Proventos:</span>
                                    <span x-text="formatMoney(totalProventos)"></span>
                                </div>
                            </div>
                        </div>

                        {{-- Descontos --}}
                        <div class="bg-red-50 rounded-lg p-4">
                            <h3 class="font-semibold text-red-800 mb-3">📉 Descontos</h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span>INSS:</span>
                                    <span x-text="formatMoney(inss)"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Faltas:</span>
                                    <span x-text="formatMoney(faltasValor)"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span>DSR Faltas:</span>
                                    <span x-text="formatMoney(dsrFaltas)"></span>
                                </div>
                                <hr class="border-red-300">
                                <div class="flex justify-between font-bold text-red-800">
                                    <span>Total Descontos:</span>
                                    <span x-text="formatMoney(totalDescontos)"></span>
                                </div>
                            </div>
                        </div>

                        {{-- Líquido --}}
                        <div class="bg-blue-50 rounded-lg p-4 flex flex-col justify-center items-center">
                            <h3 class="font-semibold text-blue-800 mb-3">💰 Salário Líquido</h3>
                            <p class="text-3xl font-bold text-blue-700" x-text="formatMoney(salarioLiquido)"></p>
                            <p class="text-xs text-gray-500 mt-2">5º dia útil: <span x-text="quintoDiaUtil"></span></p>
                        </div>
                    </div>
                </div>

                {{-- Botão Salvar --}}
                <div class="flex justify-end">
                    <button type="submit" :disabled="!funcionario.id || !competencia"
                        class="px-6 py-3 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed font-semibold">
                        💾 Gerar Folha de Pagamento
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        function folhaPagamentoForm() {
            return {
                // Dados do formulário
                searchTerm: '',
                searchResults: [],
                showDropdown: false,
                highlightedIndex: -1,
                funcionario: {},
                competencia: '',

                // Dados variáveis (únicos que o usuário preenche)
                form: {
                    salario_base: 0,
                    horas_extras_totais: 0,
                    faltas_dias: 0,
                    gratificacao_feriado: 0,
                    eh_diarista: false,
                    valor_diaria: 0,
                },

                // Valores calculados
                diasUteis: 0,
                domingosFeriados: 0,
                valorHoraNormal: 0,
                valorHoraExtra: 0,
                inss: 0,
                salarioFamilia: 0,
                faltasValor: 0,
                dsrFaltas: 0,
                quintoDiaUtil: '',

                // Métodos
                async searchFuncionario() {
                    if (this.searchTerm.length < 2) {
                        this.searchResults = [];
                        return;
                    }

                    try {
                        console.log(
                            `{{ route('rh.folha-pagamento.buscar') }}/?q=${encodeURIComponent(this.searchTerm)}`
                        );
                        const url = `{{ route('rh.folha-pagamento.buscar') }}/?q=${encodeURIComponent(this.searchTerm)}`
                        // const url = `/rh/folha-pagamento/buscar?q=${encodeURIComponent(this.searchTerm)}`;
                        const response = await fetch(url);
                        this.searchResults = await response.json();
                        this.showDropdown = true;
                        this.highlightedIndex = -1;
                    } catch (error) {
                        console.error('Erro:', error);
                    }
                },

                selectFuncionario(func) {
                    console.log(func);
                    this.funcionario = func;
                    this.form.salario_base = parseFloat(func.salario_base) || 0;
                    this.searchTerm = func.nome_completo;
                    this.showDropdown = false;
                    this.searchResults = [];

                    this.calcularDiasUteis();
                    this.calcularTotais();
                },

                highlightNext() {
                    if (this.searchResults.length === 0) return;
                    this.highlightedIndex = (this.highlightedIndex + 1) % this.searchResults.length;
                },

                highlightPrev() {
                    if (this.searchResults.length === 0) return;
                    this.highlightedIndex = (this.highlightedIndex - 1 + this.searchResults.length) % this.searchResults
                        .length;
                },

                selectHighlighted() {
                    if (this.highlightedIndex >= 0 && this.highlightedIndex < this.searchResults.length) {
                        this.selectFuncionario(this.searchResults[this.highlightedIndex]);
                    }
                },

                async calcularDiasUteis() {
                    if (!this.competencia) return;

                    try {
                        const response = await fetch(`/rh/folha-pagamento/calendario?competencia=${this.competencia}`);
                        const data = await response.json();
                        this.diasUteis = data.dias_uteis;
                        this.domingosFeriados = data.domingos_feriados;
                        this.quintoDiaUtil = data.quinto_dia_util;
                        this.calcularTotais();
                    } catch (error) {
                        console.error('Erro:', error);
                    }
                },

                calcularTotais() {
                    if (!this.funcionario.id) return;

                    const salario = this.form.salario_base;
                    const cargaHoraria = this.funcionario.carga_horaria_semanal || 44;
                    const horasMensais = (cargaHoraria / 6) * 30;

                    this.valorHoraNormal = horasMensais > 0 ? salario / horasMensais : 0;
                    this.valorHoraExtra = this.valorHoraNormal * 1.5;
                    this.calcularInss(salario);

                    if (this.diasUteis > 0) {
                        const valorDia = salario / this.diasUteis;
                        this.faltasValor = this.form.faltas_dias * valorDia;
                        this.dsrFaltas = this.faltasValor * (this.domingosFeriados / this.diasUteis);
                    }
                },

                calcularInss(salario) {
                    // Cálculo progressivo INSS
                    let inss = 0;
                    let restante = salario;

                    const faixas = [{
                            limite: 1412.00,
                            aliquota: 0.075
                        },
                        {
                            limite: 2666.68,
                            aliquota: 0.09
                        },
                        {
                            limite: 4000.03,
                            aliquota: 0.12
                        },
                        {
                            limite: 7786.02,
                            aliquota: 0.14
                        },
                    ];

                    for (const faixa of faixas) {
                        const valor = Math.min(restante, faixa.limite);
                        inss += valor * faixa.aliquota;
                        restante -= valor;
                        if (restante <= 0) break;
                    }

                    this.inss = Math.round(inss * 100) / 100;
                },

                // Getters computados
                get totalHorasExtras() {
                    return this.form.horas_extras_totais * this.valorHoraExtra;
                },

                get dsrHoraExtra() {
                    if (this.form.horas_extras_totais === 0 || this.diasUteis === 0) return 0;
                    const media = this.form.horas_extras_totais / this.diasUteis;
                    return Math.round(media * this.domingosFeriados * this.valorHoraExtra * 100) / 100;
                },

                get totalProventos() {
                    return this.form.salario_base +
                        this.totalHorasExtras +
                        this.dsrHoraExtra +
                        (this.form.gratificacao_feriado || 0) +
                        this.salarioFamilia;
                },

                get totalDescontos() {
                    return this.inss + this.faltasValor + this.dsrFaltas;
                },

                get salarioLiquido() {
                    return Math.round((this.totalProventos - this.totalDescontos) * 100) / 100;
                },

                formatMoney(value) {
                    if (!value) return 'R$ 0,00';
                    return 'R$ ' + parseFloat(value).toLocaleString('pt-BR', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                },

                formatDate(date) {
                    if (!date) return '-';
                    return new Date(date).toLocaleDateString('pt-BR');
                }
            }
        }
    </script>
@endpush
