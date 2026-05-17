{{-- resources/views/rh/folha-pagamento/create.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="folhaPagamentoForm()">

        {{-- Header --}}
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Nova Folha de Pagamento</h1>
                <p class="mt-1 text-sm text-gray-600">Selecione o funcionário, preencha as variáveis e veja o resultado</p>
            </div>
            <a href="{{ route('rh.folha-pagamento.index') }}"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50">
                ← Voltar
            </a>
        </div>

        <form action="{{ route('rh.folha-pagamento.store') }}" method="POST">
            @csrf

            {{-- ============================================ --}}
            {{-- GRID: ENTRADA (ESQUERDA) | SAÍDA (DIREITA) --}}
            {{-- ============================================ --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- ============================================ --}}
                {{-- COLUNA ESQUERDA: DADOS DE ENTRADA --}}
                {{-- ============================================ --}}
                <div class="space-y-6">

                    {{-- FUNCIONÁRIO --}}
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">👤 Selecione o Funcionário</h2>

                        <div class="relative" @click.away="showDropdown = false">
                            <input type="text" x-model="searchTerm" @input="searchFuncionario()"
                                @focus="showDropdown = true" @keydown.escape="showDropdown = false"
                                @keydown.arrow-down.prevent="highlightNext()" @keydown.arrow-up.prevent="highlightPrev()"
                                @keydown.enter.prevent="selectHighlighted()" placeholder="Digite o nome do funcionário..."
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                            <div x-show="showDropdown && searchResults.length > 0" x-transition
                                class="absolute z-50 w-full mt-1 bg-white rounded-md shadow-lg border max-h-48 overflow-auto">
                                <template x-for="(func, index) in searchResults" :key="func.id">
                                    <div @click="selectFuncionario(func)" @mouseenter="highlightedIndex = index"
                                        :class="{ 'bg-indigo-50': index === highlightedIndex }"
                                        class="px-3 py-2 cursor-pointer hover:bg-indigo-50 border-b last:border-0">
                                        <p class="font-medium text-sm" x-text="func.nome_completo"></p>
                                        <p class="text-xs text-gray-500">
                                            <span x-text="func.cargo || 'Sem função'"></span> •
                                            <span x-text="formatMoney(func.salario_base)"></span>
                                        </p>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <input type="hidden" name="funcionario_id" x-model="funcionario.id">

                    </div>

                    {{-- DADOS DO FUNCIONÁRIO (PREENCHIDO AUTOMATICAMENTE) --}}
                    <div x-show="funcionario.id" class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">📋 Dados do Funcionário</h2>

                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <label class="block text-xs text-gray-500">Local de Trabalho</label>
                                <input type="text" x-model="funcionario.local_trabalho" disabled
                                    class="mt-1 block w-full rounded-md border-gray-200 bg-gray-50 text-gray-700 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500">Tipo de Contratação</label>
                                <input type="text" x-model="funcionario.tipo_contratacao" disabled
                                    class="mt-1 block w-full rounded-md border-gray-200 bg-gray-50 text-gray-700 text-sm uppercase">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500">Função</label>
                                <input type="text" x-model="funcionario.cargo" disabled
                                    class="mt-1 block w-full rounded-md border-gray-200 bg-gray-50 text-gray-700 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500">Salário Base</label>
                                <input type="text" :value="formatMoney(funcionario.salario_base)" disabled
                                    class="mt-1 block w-full rounded-md border-gray-200 bg-green-50 text-green-700 text-sm font-semibold">
                            </div>
                        </div>
                    </div>

                    {{-- VARIÁVEIS MENSAIS (ÚNICOS CAMPOS QUE VOCÊ PRECISA PREENCHER) --}}
                    <div x-show="funcionario.id" class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">📝 Variáveis do Mês</h2>

                        <div class="space-y-4">
                            {{-- Competência --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700">📅 Competência</label>
                                <input type="month" name="competencia" x-model="competencia" @change="calcularDiasUteis()"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            {{-- Após selecionar funcionário e competência --}}
                            <div x-show="folhaExistente" x-transition
                                class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                                <div class="flex items-center">
                                    <svg class="h-5 w-5 text-yellow-400 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <p class="text-sm text-yellow-700" x-text="mensagemErro"></p>
                                </div>
                                <a :href="`/rh/folha-pagamento/${funcionario.id}/edit`"
                                    class="mt-2 inline-flex items-center text-sm text-yellow-800 hover:text-yellow-900">
                                    ✏️ Editar folha existente →
                                </a>
                            </div>



                            {{-- Grid de variáveis --}}
                            <div class="grid grid-cols-2 gap-4">
                                {{-- Dia 20 Vale --}}
                                <div>
                                    <label class="block text-xs font-medium text-gray-700">💳 Dia 20 Vale (R$)</label>
                                    <input type="number" name="vale_dia_20" x-model.number="form.vale_dia_20"
                                        step="0.01" min="0" value="0"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                </div>

                                {{-- Vale Extra --}}
                                <div>
                                    <label class="block text-xs font-medium text-gray-700">🎫 Vale Extra (R$)</label>
                                    <input type="number" name="vale_extra" x-model.number="form.vale_extra" step="0.01"
                                        min="0" value="0"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                </div>

                                {{-- Total de Faltas (dias) --}}
                                <div>
                                    <label class="block text-xs font-medium text-gray-700">📉 Total de Faltas
                                        (dias)</label>
                                    <input type="number" name="faltas_dias" x-model.number="form.faltas_dias"
                                        step="0.5" min="0" value="0" @input="calcularTotais()"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                </div>

                                {{-- Hora Extra (total) --}}
                                <div>
                                    <label class="block text-xs font-medium text-gray-700">⏰ Hora Extra (total
                                        horas)</label>
                                    <input type="number" name="horas_extras_totais"
                                        x-model.number="form.horas_extras_totais" step="0.5" min="0"
                                        value="0" @input="calcularTotais()"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                </div>

                                {{-- Sábado (horas) --}}
                                <div>
                                    <label class="block text-xs font-medium text-gray-700">🗓️ Sábado (horas)</label>
                                    <input type="number" name="horas_sabado" x-model.number="form.horas_sabado"
                                        step="0.5" min="0" value="0" @input="calcularTotais()"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                </div>

                                {{-- Feriado 100% (horas) --}}
                                <div>
                                    <label class="block text-xs font-medium text-gray-700">🎌 Feriado 100% (horas)</label>
                                    <input type="number" name="horas_feriado" x-model.number="form.horas_feriado"
                                        step="0.5" min="0" value="0" @input="calcularTotais()"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                </div>

                                {{-- Gratificação --}}
                                <div>
                                    <label class="block text-xs font-medium text-gray-700">🎁 Gratificação (R$)</label>
                                    <input type="number" name="gratificacao_feriado"
                                        x-model.number="form.gratificacao_feriado" step="0.01" min="0"
                                        value="0" @input="calcularTotais()"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ============================================ --}}
                {{-- COLUNA DIREITA: RESULTADOS CALCULADOS --}}
                {{-- ============================================ --}}
                <div x-show="funcionario.id && competencia" class="space-y-6">

                    {{-- INFORMAÇÕES DA JORNADA --}}
                    <div class="bg-blue-50 rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold text-blue-800 mb-4">📊 Informações da Jornada</h2>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-blue-600">Dias Úteis:</span>
                                <span class="font-semibold ml-2" x-text="diasUteis"></span>
                            </div>
                            <div>
                                <span class="text-blue-600">Dom/Feriados:</span>
                                <span class="font-semibold ml-2" x-text="domingosFeriados"></span>
                            </div>
                            <div>
                                <span class="text-blue-600">Valor Hora Normal:</span>
                                <span class="font-semibold ml-2" x-text="formatMoney(valorHoraNormal)"></span>
                            </div>
                            <div>
                                <span class="text-blue-600">Valor Hora Extra (50%):</span>
                                <span class="font-semibold ml-2" x-text="formatMoney(valorHoraExtra)"></span>
                            </div>
                            <div>
                                <span class="text-blue-600">Valor Hora Sábado (50%):</span>
                                <span class="font-semibold ml-2" x-text="formatMoney(valorHoraExtra)"></span>
                            </div>
                            <div>
                                <span class="text-blue-600">Valor Hora Feriado (100%):</span>
                                <span class="font-semibold ml-2" x-text="formatMoney(valorHoraNormal * 2)"></span>
                            </div>
                        </div>
                    </div>

                    {{-- RESULTADO FINAL (TABELA COMPLETA) --}}
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">📋 Folha de Pagamento Calculada</h2>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Campo
                                        </th>
                                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Valor
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    {{-- Dados Cadastrais --}}
                                    <tr class="bg-gray-50">
                                        <td colspan="2" class="px-3 py-2 text-xs font-semibold text-gray-700">DADOS
                                            CADASTRAIS</td>
                                    </tr>
                                    <tr>
                                        <td class="px-3 py-2 text-gray-600">Local de Trabalho</td>
                                        <td class="px-3 py-2 text-right font-medium"
                                            x-text="funcionario.local_trabalho || '-'"></td>
                                    </tr>
                                    <tr>
                                        <td class="px-3 py-2 text-gray-600">Tipo de Contratação</td>
                                        <td class="px-3 py-2 text-right font-medium uppercase"
                                            x-text="funcionario.tipo_contratacao || '-'"></td>
                                    </tr>
                                    <tr>
                                        <td class="px-3 py-2 text-gray-600">Funcionário</td>
                                        <td class="px-3 py-2 text-right font-medium" x-text="funcionario.nome_completo">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="px-3 py-2 text-gray-600">Função</td>
                                        <td class="px-3 py-2 text-right font-medium" x-text="funcionario.cargo || '-'">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="px-3 py-2 text-gray-600">Salário Real</td>
                                        <td class="px-3 py-2 text-right font-semibold text-green-700"
                                            x-text="formatMoney(funcionario.salario_base)"></td>
                                    </tr>

                                    {{-- Proventos --}}
                                    <tr class="bg-gray-50">
                                        <td colspan="2" class="px-3 py-2 text-xs font-semibold text-green-700">
                                            PROVENTOS</td>
                                    </tr>
                                    <tr>
                                        <td class="px-3 py-2 text-gray-600">Salário</td>
                                        <td class="px-3 py-2 text-right font-medium"
                                            x-text="formatMoney(form.salario_base)"></td>
                                    </tr>
                                    <tr x-show="form.horas_extras_totais > 0">
                                        <td class="px-3 py-2 text-gray-600">Hora Extra (50%)</td>
                                        <td class="px-3 py-2 text-right font-medium"
                                            x-text="formatMoney(totalHorasExtrasNormais)"></td>
                                    </tr>
                                    <tr x-show="form.horas_sabado > 0">
                                        <td class="px-3 py-2 text-gray-600">Sábado (50%)</td>
                                        <td class="px-3 py-2 text-right font-medium"
                                            x-text="formatMoney(totalHorasSabado)"></td>
                                    </tr>
                                    <tr x-show="form.horas_feriado > 0">
                                        <td class="px-3 py-2 text-gray-600">Feriado 100%</td>
                                        <td class="px-3 py-2 text-right font-medium"
                                            x-text="formatMoney(totalHorasFeriado)"></td>
                                    </tr>
                                    <tr>
                                        <td class="px-3 py-2 text-gray-600">DSR Hora Extra</td>
                                        <td class="px-3 py-2 text-right font-medium text-indigo-700"
                                            x-text="formatMoney(dsrHoraExtra)"></td>
                                    </tr>
                                    <tr x-show="salarioFamilia > 0">
                                        <td class="px-3 py-2 text-gray-600">Salário Família</td>
                                        <td class="px-3 py-2 text-right font-medium" x-text="formatMoney(salarioFamilia)">
                                        </td>
                                    </tr>
                                    <tr x-show="form.gratificacao_feriado > 0">
                                        <td class="px-3 py-2 text-gray-600">Gratificação</td>
                                        <td class="px-3 py-2 text-right font-medium"
                                            x-text="formatMoney(form.gratificacao_feriado)"></td>
                                    </tr>
                                    <tr x-show="arredondamentoProvento != 0">
                                        <td class="px-3 py-2 text-gray-600">Arred. Provento</td>
                                        <td class="px-3 py-2 text-right font-medium"
                                            x-text="formatMoney(arredondamentoProvento)"></td>
                                    </tr>
                                    <tr class="bg-green-50">
                                        <td class="px-3 py-2 font-semibold text-green-800">TOTAL PROVENTOS</td>
                                        <td class="px-3 py-2 text-right font-bold text-green-800"
                                            x-text="formatMoney(totalProventos)"></td>
                                    </tr>

                                    {{-- Descontos --}}
                                    <tr class="bg-gray-50">
                                        <td colspan="2" class="px-3 py-2 text-xs font-semibold text-red-700">DESCONTOS
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="px-3 py-2 text-gray-600">Desconto INSS</td>
                                        <td class="px-3 py-2 text-right font-medium text-red-700"
                                            x-text="formatMoney(inss)"></td>
                                    </tr>
                                    <tr x-show="form.vale_dia_20 > 0">
                                        <td class="px-3 py-2 text-gray-600">Dia 20 Vale</td>
                                        <td class="px-3 py-2 text-right font-medium text-red-700"
                                            x-text="formatMoney(form.vale_dia_20)"></td>
                                    </tr>
                                    <tr x-show="form.vale_extra > 0">
                                        <td class="px-3 py-2 text-gray-600">Vale Extra</td>
                                        <td class="px-3 py-2 text-right font-medium text-red-700"
                                            x-text="formatMoney(form.vale_extra)"></td>
                                    </tr>
                                    <tr x-show="faltasValor > 0">
                                        <td class="px-3 py-2 text-gray-600">Faltas</td>
                                        <td class="px-3 py-2 text-right font-medium text-red-700"
                                            x-text="formatMoney(faltasValor)"></td>
                                    </tr>
                                    <tr x-show="dsrFaltas > 0">
                                        <td class="px-3 py-2 text-gray-600">D.S.R Faltas</td>
                                        <td class="px-3 py-2 text-right font-medium text-red-700"
                                            x-text="formatMoney(dsrFaltas)"></td>
                                    </tr>
                                    <tr x-show="arredondamentoDesconto != 0">
                                        <td class="px-3 py-2 text-gray-600">Arred. Desc.</td>
                                        <td class="px-3 py-2 text-right font-medium text-red-700"
                                            x-text="formatMoney(arredondamentoDesconto)"></td>
                                    </tr>
                                    <tr class="bg-red-50">
                                        <td class="px-3 py-2 font-semibold text-red-800">TOTAL DESCONTOS</td>
                                        <td class="px-3 py-2 text-right font-bold text-red-800"
                                            x-text="formatMoney(totalDescontos)"></td>
                                    </tr>

                                    {{-- Líquido --}}
                                    <tr class="bg-blue-100">
                                        <td class="px-3 py-2 font-semibold text-blue-800">💰 SALÁRIO LÍQUIDO</td>
                                        <td class="px-3 py-2 text-right font-bold text-blue-800 text-lg"
                                            x-text="formatMoney(salarioLiquido)"></td>
                                    </tr>

                                    {{-- 5º Dia Útil --}}
                                    <tr>
                                        <td class="px-3 py-2 text-gray-600">📅 5º Dia Útil</td>
                                        <td class="px-3 py-2 text-right font-medium" x-text="quintoDiaUtil || '-'"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Botão Salvar --}}
                    <div class="flex justify-end">
                        {{-- <button type="submit"
                            class="px-8 py-3 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 font-semibold shadow-lg">
                            💾 Salvar Folha de Pagamento
                        </button> --}}
                        {{-- Desabilitar botão se já existe --}}
                        <button type="submit" :disabled="!funcionario.id || !competencia || folhaExistente"
                            class="px-8 py-3 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed font-semibold shadow-lg">
                            💾 Salvar Folha de Pagamento
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        function folhaPagamentoForm() {
            return {
                // Busca
                searchTerm: '',
                searchResults: [],
                showDropdown: false,
                highlightedIndex: -1,

                // Funcionário selecionado
                funcionario: {},
                competencia: '',

                // Formulário (dados de entrada)
                form: {
                    salario_base: 0,
                    local_trabalho: '',
                    tipo_contratacao: '',
                    vale_dia_20: 0,
                    vale_extra: 0,
                    faltas_dias: 0,
                    horas_extras_totais: 0,
                    horas_sabado: 0,
                    horas_feriado: 0,
                    gratificacao_feriado: 0,
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
                arredondamentoProvento: 0,
                arredondamentoDesconto: 0,
                quintoDiaUtil: '',
                folhaExistente: false,
                totalDescontos: 0,
                salarioLiquido: 0,
                mensagemErro: '',

                // E adicione um watcher para competencia
                // No init ou como método separado
                init() {
                    this.$watch('competencia', () => {
                        this.calcularDiasUteis();
                        this.verificarFolhaExistente();
                    });
                },

                // ─── BUSCA FUNCIONÁRIO ──────────────────────
                async searchFuncionario() {
                    if (this.searchTerm.length < 2) {
                        this.searchResults = [];
                        return;
                    }

                    try {
                        const url = `/rh/folha-pagamento/buscar?q=${encodeURIComponent(this.searchTerm)}`;
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
                    this.form.local_trabalho = func.local_trabalho || '';
                    this.form.tipo_contratacao = func.tipo_contratacao || '';
                    this.searchTerm = func.nome_completo;
                    this.showDropdown = false;
                    this.searchResults = [];

                    this.calcularTotais();
                    this.verificarFolhaExistente();

                    if (this.competencia) this.calcularDiasUteis();

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

                // ─── CÁLCULOS ───────────────────────────────
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

                    // INSS
                    this.calcularInss(salario);

                    // Salário Família
                    this.calcularSalarioFamilia(salario);

                    // Faltas
                    if (this.diasUteis > 0) {
                        const valorDia = salario / this.diasUteis;
                        this.faltasValor = Math.round(this.form.faltas_dias * valorDia * 100) / 100;
                        this.dsrFaltas = Math.round(this.faltasValor * (this.domingosFeriados / this.diasUteis) * 100) /
                            100;
                    }

                    // Arredondamentos
                    const liquidoBruto = this.totalProventos - this.totalDescontos;
                    const diff = Math.round((liquidoBruto - Math.floor(liquidoBruto * 100) / 100) * 100) / 100;
                    if (diff > 0.005) {
                        this.arredondamentoDesconto = diff;
                        this.arredondamentoProvento = 0;
                    } else if (diff < -0.005) {
                        this.arredondamentoProvento = Math.abs(diff);
                        this.arredondamentoDesconto = 0;
                    } else {
                        this.arredondamentoProvento = 0;
                        this.arredondamentoDesconto = 0;
                    }
                },

                calcularInss(salario) {
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

                calcularSalarioFamilia(salario) {
                    const dependentes = this.funcionario.qtd_dependentes_salario_familia || 0;
                    if (dependentes === 0 || salario > 1819.26) {
                        this.salarioFamilia = 0;
                        return;
                    }
                    this.salarioFamilia = Math.round(dependentes * 62.04 * 100) / 100;
                },

                // ─── GETTERS COMPUTADOS ─────────────────────
                get totalHorasExtrasNormais() {
                    return Math.round(this.form.horas_extras_totais * this.valorHoraExtra * 100) / 100;
                },

                get totalHorasSabado() {
                    return Math.round(this.form.horas_sabado * this.valorHoraExtra * 100) / 100;
                },

                get totalHorasFeriado() {
                    return Math.round(this.form.horas_feriado * this.valorHoraNormal * 2 * 100) / 100;
                },

                get totalHorasExtras() {
                    return this.totalHorasExtrasNormais + this.totalHorasSabado + this.totalHorasFeriado;
                },

                get horasExtrasTotal() {
                    return this.form.horas_extras_totais + this.form.horas_sabado + this.form.horas_feriado;
                },

                get dsrHoraExtra() {
                    if (this.horasExtrasTotal === 0 || this.diasUteis === 0) return 0;
                    const media = this.horasExtrasTotal / this.diasUteis;
                    return Math.round(media * this.domingosFeriados * this.valorHoraExtra * 100) / 100;
                },

                get totalProventos() {
                    return (
                        this.form.salario_base +
                        this.totalHorasExtras +
                        this.dsrHoraExtra +
                        this.salarioFamilia +
                        (this.form.gratificacao_feriado || 0) +
                        this.arredondamentoProvento
                    );
                },

                get totalDescontos() {
                    return (
                        this.inss +
                        (this.form.vale_dia_20 || 0) +
                        (this.form.vale_extra || 0) +
                        this.faltasValor +
                        this.dsrFaltas +
                        this.arredondamentoDesconto
                    );
                },

                get salarioLiquido() {
                    return Math.round((this.totalProventos - this.totalDescontos) * 100) / 100;
                },

                // ─── HELPERS ────────────────────────────────
                formatMoney(value) {
                    if (value === null || value === undefined) return 'R$ 0,00';
                    return 'R$ ' + parseFloat(value).toLocaleString('pt-BR', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                },
                async verificarFolhaExistente() {
                    if (!this.funcionario.id || !this.competencia) return;

                    try {
                        const response = await fetch(
                            `/rh/folha-pagamento/verificar?funcionario_id=${this.funcionario.id}&competencia=${this.competencia}`
                        );
                        const data = await response.json();

                        if (data.existe) {
                            this.folhaExistente = true;
                            this.mensagemErro =
                                `⚠️ Já existe uma folha para ${this.funcionario.nome_completo} na competência ${this.competencia}!`;
                        } else {
                            this.folhaExistente = false;
                            this.mensagemErro = '';
                        }
                    } catch (error) {
                        console.error('Erro:', error);
                    }
                }
            }
        }
    </script>
@endpush
