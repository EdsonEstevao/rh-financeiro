{{-- resources/views/rh/folha-pagamento/edit.blade.php --}}
@extends('layouts.app')


@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="folhaPagamentoEditForm({{ Js::from($folhaPagamento->load(['funcionario.cargo', 'lancamentos'])) }})">

        {{-- Header --}}
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Editar Folha de Pagamento</h1>
                <p class="mt-1 text-sm text-gray-600">
                    Funcionário: <span class="font-semibold">{{ $folhaPagamento->funcionario->nome_completo }}</span> |
                    Competência: <span
                        class="font-semibold">{{ \Carbon\Carbon::parse($folhaPagamento->competencia)->format('m/Y') }}</span>
                </p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('rh.folha-pagamento.show', $folhaPagamento->id) }}"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    ← Voltar
                </a>
                <a href="{{ route('rh.folha-pagamento.pdf', $folhaPagamento->id) }}" target="_blank"
                    class="inline-flex items-center px-4 py-2 border border-red-300 rounded-md text-red-700 bg-white hover:bg-red-50">
                    📄 PDF
                </a>
            </div>
        </div>

        <form action="{{ route('rh.folha-pagamento.update', $folhaPagamento->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- COLUNA ESQUERDA: DADOS DE ENTRADA --}}
                <div class="space-y-6">

                    {{-- Dados do Funcionário --}}
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">📋 Dados do Funcionário</h2>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <label class="block text-xs text-gray-500">Nome</label>
                                <p class="font-semibold">{{ $folhaPagamento->funcionario->nome_completo }}</p>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500">Função</label>
                                <p class="font-semibold">{{ $folhaPagamento->funcionario->cargo?->nome ?? 'Não informado' }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500">Local de Trabalho</label>
                                <p class="font-semibold">
                                    {{ $folhaPagamento->funcionario->local_trabalho ?? 'Não informado' }}</p>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500">Tipo de Contratação</label>
                                <p class="font-semibold uppercase">
                                    {{ $folhaPagamento->funcionario->tipo_contratacao ?? 'CLT' }}</p>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500">Salário Base</label>
                                <p class="font-semibold text-green-700">R$
                                    {{ number_format($folhaPagamento->funcionario->salario_base, 2, ',', '.') }}</p>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500">Carga Horária</label>
                                <p class="font-semibold">{{ $folhaPagamento->funcionario->carga_horaria_semanal ?? 44 }}h
                                    semanais</p>
                            </div>
                        </div>
                    </div>

                    {{-- Lançamentos Existentes (EDITÁVEIS) --}}
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">📝 Lançamentos do Mês</h2>
                        <p class="text-sm text-gray-500 mb-4">Edite os valores conforme necessário</p>

                        <div class="space-y-4">
                            {{-- Competência (somente leitura) --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700">📅 Competência</label>
                                <input type="text"
                                    value="{{ \Carbon\Carbon::parse($folhaPagamento->competencia)->format('m/Y') }}"
                                    disabled class="mt-1 block w-full rounded-md border-gray-200 bg-gray-50 text-gray-700">
                                <input type="hidden" name="competencia"
                                    value="{{ \Carbon\Carbon::parse($folhaPagamento->competencia)->format('Y-m') }}">
                            </div>

                            {{-- Grid de lançamentos --}}
                            <div class="grid grid-cols-2 gap-4">

                                {{-- Horas Extras Normais --}}
                                <div>
                                    <label class="block text-xs font-medium text-gray-700">⏰ Horas Extras (50%)</label>
                                    <input type="number" name="horas_extras_totais"
                                        x-model.number="form.horas_extras_totais" step="0.5" min="0"
                                        @input="calcularTotais()"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    <p class="text-xs text-gray-400 mt-1">
                                        Valor: <span x-text="formatMoney(valorHoraExtra)"></span>/h
                                    </p>
                                </div>

                                {{-- Horas Sábado --}}
                                <div>
                                    <label class="block text-xs font-medium text-gray-700">🗓️ Sábado (50%)</label>
                                    <input type="number" name="horas_sabado" x-model.number="form.horas_sabado"
                                        step="0.5" min="0" @input="calcularTotais()"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    <p class="text-xs text-gray-400 mt-1">
                                        Valor: <span x-text="formatMoney(valorHoraExtra)"></span>/h
                                    </p>
                                </div>

                                {{-- Horas Feriado --}}
                                <div>
                                    <label class="block text-xs font-medium text-gray-700">🎌 Feriado (100%)</label>
                                    <input type="number" name="horas_feriado" x-model.number="form.horas_feriado"
                                        step="0.5" min="0" @input="calcularTotais()"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    <p class="text-xs text-gray-400 mt-1">
                                        Valor: <span x-text="formatMoney(valorHoraNormal * 2)"></span>/h
                                    </p>
                                </div>

                                {{-- Faltas --}}
                                <div>
                                    <label class="block text-xs font-medium text-gray-700">📉 Faltas (dias)</label>
                                    <input type="number" name="faltas_dias" x-model.number="form.faltas_dias"
                                        step="0.5" min="0" @input="calcularTotais()"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    <p class="text-xs text-gray-400 mt-1">
                                        Valor dia: <span x-text="formatMoney(form.salario_base / diasUteis)"></span>
                                    </p>
                                </div>

                                {{-- Vale Dia 20 --}}
                                <div>
                                    <label class="block text-xs font-medium text-gray-700">💳 Vale Dia 20 (R$)</label>
                                    <input type="number" name="vale_dia_20" x-model.number="form.vale_dia_20"
                                        step="0.01" min="0" @input="calcularTotais()"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                </div>

                                {{-- Vale Extra --}}
                                <div>
                                    <label class="block text-xs font-medium text-gray-700">🎫 Vale Extra (R$)</label>
                                    <input type="number" name="vale_extra" x-model.number="form.vale_extra" step="0.01"
                                        min="0" @input="calcularTotais()"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                </div>

                                {{-- Gratificação --}}
                                <div>
                                    <label class="block text-xs font-medium text-gray-700">🎁 Gratificação (R$)</label>
                                    <input type="number" name="gratificacao_feriado"
                                        x-model.number="form.gratificacao_feriado" step="0.01" min="0"
                                        @input="calcularTotais()"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                </div>

                                {{-- Status --}}
                                <div>
                                    <label class="block text-xs font-medium text-gray-700">📌 Status</label>
                                    <select name="status" x-model="form.status"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                        <option value="aberta">Aberta</option>
                                        <option value="fechada">Fechada</option>
                                    </select>
                                </div>
                            </div>

                            {{-- Observação --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700">📝 Observação</label>
                                <textarea name="observacao" x-model="form.observacao" rows="2"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                    placeholder="Observações sobre esta folha..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- COLUNA DIREITA: RESULTADOS --}}
                <div class="space-y-6">

                    {{-- Informações da Jornada --}}
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
                                <span class="text-blue-600">Valor Hora Extra:</span>
                                <span class="font-semibold ml-2" x-text="formatMoney(valorHoraExtra)"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Resumo Financeiro --}}
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">💰 Resumo Financeiro</h2>

                        <div class="grid grid-cols-3 gap-4 text-center">
                            <div class="bg-green-50 rounded-lg p-4">
                                <span class="text-xs text-green-600">Proventos</span>
                                <p class="text-xl font-bold text-green-700" x-text="formatMoney(totalProventos)"></p>
                            </div>
                            <div class="bg-red-50 rounded-lg p-4">
                                <span class="text-xs text-red-600">Descontos</span>
                                <p class="text-xl font-bold text-red-700" x-text="formatMoney(totalDescontos)"></p>
                            </div>
                            <div class="bg-blue-50 rounded-lg p-4">
                                <span class="text-xs text-blue-600">Líquido</span>
                                <p class="text-xl font-bold text-blue-700" x-text="formatMoney(salarioLiquido)"></p>
                            </div>
                        </div>
                    </div>

                    {{-- Tabela de Lançamentos --}}
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">📋 Lançamentos Calculados</h2>

                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="bg-gray-50">
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                                            Descrição</th>
                                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">
                                            Qtd/Horas</th>
                                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Valor
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y">
                                    {{-- Proventos --}}
                                    <tr class="bg-green-50">
                                        <td colspan="3" class="px-3 py-2 text-xs font-semibold text-green-700">💵
                                            PROVENTOS</td>
                                    </tr>
                                    <tr>
                                        <td class="px-3 py-2 text-gray-600">Salário Base</td>
                                        <td class="px-3 py-2 text-right text-gray-400">-</td>
                                        <td class="px-3 py-2 text-right font-medium"
                                            x-text="formatMoney(form.salario_base)"></td>
                                    </tr>
                                    <tr x-show="form.horas_extras_totais > 0">
                                        <td class="px-3 py-2 text-gray-600">Horas Extras (50%)</td>
                                        <td class="px-3 py-2 text-right text-gray-500"
                                            x-text="form.horas_extras_totais + 'h'"></td>
                                        <td class="px-3 py-2 text-right font-medium"
                                            x-text="formatMoney(totalHorasExtrasNormais)"></td>
                                    </tr>
                                    <tr x-show="form.horas_sabado > 0">
                                        <td class="px-3 py-2 text-gray-600">Sábado (50%)</td>
                                        <td class="px-3 py-2 text-right text-gray-500" x-text="form.horas_sabado + 'h'">
                                        </td>
                                        <td class="px-3 py-2 text-right font-medium"
                                            x-text="formatMoney(totalHorasSabado)"></td>
                                    </tr>
                                    <tr x-show="form.horas_feriado > 0">
                                        <td class="px-3 py-2 text-gray-600">Feriado (100%)</td>
                                        <td class="px-3 py-2 text-right text-gray-500" x-text="form.horas_feriado + 'h'">
                                        </td>
                                        <td class="px-3 py-2 text-right font-medium"
                                            x-text="formatMoney(totalHorasFeriado)"></td>
                                    </tr>
                                    <tr>
                                        <td class="px-3 py-2 text-gray-600">DSR Hora Extra</td>
                                        <td class="px-3 py-2 text-right text-gray-400">-</td>
                                        <td class="px-3 py-2 text-right font-medium text-indigo-700"
                                            x-text="formatMoney(dsrHoraExtra)"></td>
                                    </tr>
                                    <tr x-show="salarioFamilia > 0">
                                        <td class="px-3 py-2 text-gray-600">Salário Família</td>
                                        <td class="px-3 py-2 text-right text-gray-400">-</td>
                                        <td class="px-3 py-2 text-right font-medium" x-text="formatMoney(salarioFamilia)">
                                        </td>
                                    </tr>
                                    <tr x-show="form.gratificacao_feriado > 0">
                                        <td class="px-3 py-2 text-gray-600">Gratificação</td>
                                        <td class="px-3 py-2 text-right text-gray-400">-</td>
                                        <td class="px-3 py-2 text-right font-medium"
                                            x-text="formatMoney(form.gratificacao_feriado)"></td>
                                    </tr>
                                    <tr x-show="arredondamentoProvento != 0">
                                        <td class="px-3 py-2 text-gray-600">Arredondamento</td>
                                        <td class="px-3 py-2 text-right text-gray-400">-</td>
                                        <td class="px-3 py-2 text-right font-medium"
                                            x-text="formatMoney(arredondamentoProvento)"></td>
                                    </tr>

                                    {{-- Descontos --}}
                                    <tr class="bg-red-50">
                                        <td colspan="3" class="px-3 py-2 text-xs font-semibold text-red-700">📉
                                            DESCONTOS</td>
                                    </tr>
                                    <tr>
                                        <td class="px-3 py-2 text-gray-600">INSS</td>
                                        <td class="px-3 py-2 text-right text-gray-400">-</td>
                                        <td class="px-3 py-2 text-right font-medium text-red-700"
                                            x-text="formatMoney(inss)"></td>
                                    </tr>
                                    <tr x-show="form.vale_dia_20 > 0">
                                        <td class="px-3 py-2 text-gray-600">Vale Dia 20</td>
                                        <td class="px-3 py-2 text-right text-gray-400">-</td>
                                        <td class="px-3 py-2 text-right font-medium text-red-700"
                                            x-text="formatMoney(form.vale_dia_20)"></td>
                                    </tr>
                                    <tr x-show="form.vale_extra > 0">
                                        <td class="px-3 py-2 text-gray-600">Vale Extra</td>
                                        <td class="px-3 py-2 text-right text-gray-400">-</td>
                                        <td class="px-3 py-2 text-right font-medium text-red-700"
                                            x-text="formatMoney(form.vale_extra)"></td>
                                    </tr>
                                    <tr x-show="faltasValor > 0">
                                        <td class="px-3 py-2 text-gray-600">Faltas</td>
                                        <td class="px-3 py-2 text-right text-gray-500"
                                            x-text="form.faltas_dias + ' dias'"></td>
                                        <td class="px-3 py-2 text-right font-medium text-red-700"
                                            x-text="formatMoney(faltasValor)"></td>
                                    </tr>
                                    <tr x-show="dsrFaltas > 0">
                                        <td class="px-3 py-2 text-gray-600">DSR Faltas</td>
                                        <td class="px-3 py-2 text-right text-gray-400">-</td>
                                        <td class="px-3 py-2 text-right font-medium text-red-700"
                                            x-text="formatMoney(dsrFaltas)"></td>
                                    </tr>
                                    <tr x-show="arredondamentoDesconto != 0">
                                        <td class="px-3 py-2 text-gray-600">Arredondamento</td>
                                        <td class="px-3 py-2 text-right text-gray-400">-</td>
                                        <td class="px-3 py-2 text-right font-medium text-red-700"
                                            x-text="formatMoney(arredondamentoDesconto)"></td>
                                    </tr>
                                    {{-- 5º Dia Útil --}}
                                    <tr>
                                        <td class="px-3 py-2 text-gray-600">📅 5º Dia Útil</td>
                                        <td colspan="2" class="px-3 py-2 text-right font-medium"
                                            x-text="quintoDiaUtil || '-'"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Botões --}}
                    <div class="flex justify-end gap-3">
                        <a href="{{ route('rh.folha-pagamento.show', $folhaPagamento->id) }}"
                            class="px-6 py-3 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 font-semibold">
                            Cancelar
                        </a>
                        <button type="submit"
                            class="px-8 py-3 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 font-semibold shadow-lg">
                            💾 Atualizar Folha
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        function folhaPagamentoEditForm(folhaData) {
            // Extrai quantidades dos lançamentos existentes
            const lancamentos = folhaData.lancamentos || [];

            const getLancamento = (tipo) => {
                const l = lancamentos.find(l => l.tipo === tipo);
                return l ? parseFloat(l.quantidade) || 0 : 0;
            };
            const getLancamentoValor = (tipo) => {
                const l = lancamentos.find(l => l.tipo === tipo);
                return l ? parseFloat(l.valor_unitario) || 0 : 0;
            };

            return {
                funcionario: folhaData.funcionario || {},

                form: {
                    salario_base: parseFloat(folhaData.salario_base) || 0,
                    horas_extras_totais: getLancamento('hora_extra_normal'),
                    horas_sabado: getLancamento('hora_extra_sabado'),
                    horas_feriado: getLancamento('hora_extra_feriado'),
                    faltas_dias: getLancamento('falta'),
                    vale_dia_20: getLancamentoValor('vale_dia_20'), //parseFloat(folhaData.vale_dia_20) || 0,
                    vale_extra: getLancamentoValor('vale_extra'), //parseFloat(folhaData.vale_extra) || 0,
                    gratificacao_feriado: getLancamentoValor(
                        'gratificacao'), //parseFloat(folhaData.gratificacao_feriado) || 0,
                    status: folhaData.status || 'aberta',
                    observacao: folhaData.observacao || '',
                },

                diasUteis: 0,
                domingosFeriados: 0,
                valorHoraNormal: 0,
                valorHoraExtra: 0,
                inss: parseFloat(folhaData.desconto_inss) || 0,
                salarioFamilia: parseFloat(folhaData.salario_familia_hr_extra) || 0,
                faltasValor: parseFloat(folhaData.faltas_valor) || 0,
                dsrFaltas: parseFloat(folhaData.dsr_faltas) || 0,
                arredondamentoProvento: parseFloat(folhaData.arredondamento_provento) || 0,
                arredondamentoDesconto: parseFloat(folhaData.arredondamento_desconto) || 0,
                quintoDiaUtil: '',

                init() {
                    this.calcularDiasUteis();
                },

                async calcularDiasUteis() {
                    const competencia = folhaData.competencia;
                    if (!competencia) return;

                    try {
                        const data = new Date(competencia);
                        const ano = data.getFullYear();
                        const mes = String(data.getMonth() + 1).padStart(2, '0');
                        const comp = `${ano}-${mes}`;

                        const response = await fetch(`/rh/folha-pagamento/calendario?competencia=${comp}`);
                        const result = await response.json();
                        this.diasUteis = result.dias_uteis;
                        this.domingosFeriados = result.domingos_feriados;
                        this.quintoDiaUtil = result.quinto_dia_util;
                        this.calcularTotais();
                    } catch (error) {
                        console.error('Erro ao carregar calendário:', error);
                    }
                },

                calcularTotais() {
                    const salario = this.form.salario_base;
                    const cargaHoraria = this.funcionario.carga_horaria_semanal || 44;
                    const horasMensais = (cargaHoraria / 6) * 30;

                    this.valorHoraNormal = horasMensais > 0 ? salario / horasMensais : 0;
                    this.valorHoraExtra = this.valorHoraNormal * 1.5;

                    this.calcularInss(salario);
                    this.calcularSalarioFamilia(salario);
                    this.calcularFaltas();
                    this.calcularArredondamentos();
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

                calcularFaltas() {
                    if (this.diasUteis > 0 && this.form.faltas_dias > 0) {
                        const valorDia = this.form.salario_base / this.diasUteis;
                        this.faltasValor = Math.round(this.form.faltas_dias * valorDia * 100) / 100;
                        this.dsrFaltas = Math.round(this.faltasValor * (this.domingosFeriados / this.diasUteis) * 100) /
                            100;
                    } else {
                        this.faltasValor = 0;
                        this.dsrFaltas = 0;
                    }
                },

                calcularArredondamentos() {
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

                formatMoney(value) {
                    if (value === null || value === undefined) return 'R$ 0,00';
                    return 'R$ ' + parseFloat(value).toLocaleString('pt-BR', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                }
            }
        }
    </script>
@endpush
