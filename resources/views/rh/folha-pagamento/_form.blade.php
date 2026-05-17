@php
    $isEdit = isset($folhaPagamento);
    $model = $isEdit ? $folhaPagamento : null;
@endphp

<div class="space-y-8">

    {{-- ─── SEÇÃO: FUNCIONÁRIO E COMPETÊNCIA ─────────────────── --}}
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-base font-semibold text-gray-900 flex items-center gap-2">
                <svg class="h-5 w-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Identificação
            </h3>
        </div>
        <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-3 gap-6">

            {{-- Funcionário --}}
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Funcionário <span class="text-red-500">*</span>
                </label>
                <select name="funcionario_id"
                    class="w-full border-gray-300 rounded-md shadow-sm text-sm
                               focus:ring-indigo-500 focus:border-indigo-500
                               @error('funcionario_id') border-red-500 @enderror">
                    <option value="">Selecione...</option>
                    @foreach ($funcionarios as $f)
                        <option value="{{ $f->id }}"
                            {{ old('funcionario_id', $model?->funcionario_id) == $f->id ? 'selected' : '' }}>
                            {{ $f->nome_completo }}
                        </option>
                    @endforeach
                </select>
                @error('funcionario_id')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Competência --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Competência <span class="text-red-500">*</span>
                </label>
                <input type="month" name="competencia"
                    value="{{ old('competencia', $model?->competencia?->format('Y-m')) }}"
                    class="w-full border-gray-300 rounded-md shadow-sm text-sm
                              focus:ring-indigo-500 focus:border-indigo-500
                              @error('competencia') border-red-500 @enderror" />
                @error('competencia')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Quinto Dia Útil --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Quinto Dia Útil</label>
                <input type="date" name="quinto_dia_util"
                    value="{{ old('quinto_dia_util', $model?->quinto_dia_util?->format('Y-m-d')) }}"
                    class="w-full border-gray-300 rounded-md shadow-sm text-sm
                              focus:ring-indigo-500 focus:border-indigo-500" />
            </div>

            {{-- Status --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Status <span class="text-red-500">*</span>
                </label>
                <select name="status"
                    class="w-full border-gray-300 rounded-md shadow-sm text-sm
                               focus:ring-indigo-500 focus:border-indigo-500
                               @error('status') border-red-500 @enderror">
                    <option value="pendente" {{ old('status', $model?->status) === 'pendente' ? 'selected' : '' }}>
                        Pendente</option>
                    <option value="processado" {{ old('status', $model?->status) === 'processado' ? 'selected' : '' }}>
                        Processado</option>
                    <option value="pago" {{ old('status', $model?->status) === 'pago' ? 'selected' : '' }}>Pago
                    </option>
                </select>
                @error('status')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

        </div>
    </div>

    {{-- ─── SEÇÃO: PROVENTOS ───────────────────────────────────── --}}
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200 bg-green-50">
            <h3 class="text-base font-semibold text-green-800 flex items-center gap-2">
                <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Proventos
            </h3>
        </div>
        <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

            @php
                $proventos = [
                    'salario_base' => ['label' => 'Salário Base', 'required' => true],
                    'gratificacao_feriado' => ['label' => 'Gratificação Feriado'],
                    'dsr_hora_extra' => ['label' => 'DSR Hora Extra'],
                    'salario_familia_hr_extra' => ['label' => 'Salário Família / Hr Extra'],
                    'arredondamento_provento' => ['label' => 'Arredondamento (Provento)'],
                ];
            @endphp

            @foreach ($proventos as $campo => $config)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        {{ $config['label'] }}
                        @if (!empty($config['required']))
                            <span class="text-red-500">*</span>
                        @endif
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 text-sm">R$</span>
                        <input type="text" name="{{ $campo }}" inputmode="decimal" placeholder="0,00"
                            value="{{ old($campo, number_format((float) ($model?->{$campo} ?? 0), 2, ',', '.')) }}"
                            class="w-full pl-9 border-gray-300 rounded-md shadow-sm text-sm text-right
                                      focus:ring-indigo-500 focus:border-indigo-500
                                      @error($campo) border-red-500 @enderror" />
                    </div>
                    @error($campo)
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            @endforeach

            {{-- Totalizador Proventos (Alpine.js) --}}
            <div class="sm:col-span-2 lg:col-span-3">
                <div class="bg-green-50 border border-green-200 rounded-md px-4 py-3 flex justify-between items-center"
                    x-data="totalizadorFolha()" x-init="calcular()">
                    <span class="text-sm font-medium text-green-800">Total Proventos:</span>
                    <span class="text-lg font-bold text-green-800" x-text="'R$ ' + formatarBR(totalProventos)"></span>
                </div>
            </div>

        </div>
    </div>

    {{-- ─── SEÇÃO: DESCONTOS ───────────────────────────────────── --}}
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200 bg-red-50">
            <h3 class="text-base font-semibold text-red-800 flex items-center gap-2">
                <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                </svg>
                Descontos
            </h3>
        </div>
        <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

            @php
                $descontos = [
                    'desconto_inss' => 'Desconto INSS',
                    'vale_dia_20' => 'Vale Dia 20',
                    'vale_extra' => 'Vale Extra',
                    'faltas_valor' => 'Faltas (Valor)',
                    'dsr_faltas' => 'DSR Faltas',
                    'arredondamento_desconto' => 'Arredondamento (Desconto)',
                ];
            @endphp

            @foreach ($descontos as $campo => $label)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $label }}</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 text-sm">R$</span>
                        <input type="text" name="{{ $campo }}" inputmode="decimal" placeholder="0,00"
                            value="{{ old($campo, number_format((float) ($model?->{$campo} ?? 0), 2, ',', '.')) }}"
                            class="w-full pl-9 border-gray-300 rounded-md shadow-sm text-sm text-right
                                      focus:ring-indigo-500 focus:border-indigo-500
                                      @error($campo) border-red-500 @enderror" />
                    </div>
                    @error($campo)
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            @endforeach

        </div>
    </div>

    {{-- ─── SEÇÃO: RESUMO CALCULADO (Alpine.js) ───────────────── --}}
    <div class="bg-white shadow rounded-lg" x-data="totalizadorFolha()" x-init="iniciar()">

        <div class="px-6 py-4 border-b border-gray-200 bg-indigo-50">
            <h3 class="text-base font-semibold text-indigo-800 flex items-center gap-2">
                <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                Resumo do Cálculo
            </h3>
        </div>

        <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-3 gap-4 text-center">

            <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                <p class="text-xs text-green-600 uppercase font-medium mb-1">Total Proventos</p>
                <p class="text-2xl font-bold text-green-700" x-text="'R$ ' + formatarBR(totalProventos)">R$ 0,00</p>
            </div>

            <div class="bg-red-50 rounded-lg p-4 border border-red-200">
                <p class="text-xs text-red-600 uppercase font-medium mb-1">Total Descontos</p>
                <p class="text-2xl font-bold text-red-700" x-text="'R$ ' + formatarBR(totalDescontos)">R$ 0,00</p>
            </div>

            <div class="bg-indigo-50 rounded-lg p-4 border border-indigo-200">
                <p class="text-xs text-indigo-600 uppercase font-medium mb-1">Salário Líquido</p>
                <p class="text-2xl font-bold text-indigo-700" x-text="'R$ ' + formatarBR(salarioLiquido)">R$ 0,00</p>
            </div>

        </div>
    </div>

    {{-- ─── SEÇÃO: OBSERVAÇÃO ──────────────────────────────────── --}}
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-base font-semibold text-gray-700">Observação</h3>
        </div>
        <div class="px-6 py-5">
            <textarea name="observacao" rows="3" maxlength="1000" placeholder="Observações adicionais sobre esta folha..."
                class="w-full border-gray-300 rounded-md shadow-sm text-sm
                             focus:ring-indigo-500 focus:border-indigo-500">{{ old('observacao', $model?->observacao) }}</textarea>
            @error('observacao')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

</div>

{{-- ─── ALPINE.JS: TOTALIZADOR ─────────────────────────────────── --}}
@push('scripts')
    <script>
        function totalizadorFolha() {
            return {
                totalProventos: 0,
                totalDescontos: 0,
                salarioLiquido: 0,

                camposProventos: [
                    'salario_base', 'gratificacao_feriado', 'dsr_hora_extra',
                    'salario_familia_hr_extra', 'arredondamento_provento'
                ],
                camposDescontos: [
                    'desconto_inss', 'vale_dia_20', 'vale_extra',
                    'faltas_valor', 'dsr_faltas', 'arredondamento_desconto'
                ],

                iniciar() {
                    this.calcular();
                    // Observa mudanças em todos os inputs do formulário
                    const inputs = document.querySelectorAll(
                        '[name="salario_base"],[name="gratificacao_feriado"],[name="dsr_hora_extra"],' +
                        '[name="salario_familia_hr_extra"],[name="arredondamento_provento"],' +
                        '[name="desconto_inss"],[name="vale_dia_20"],[name="vale_extra"],' +
                        '[name="faltas_valor"],[name="dsr_faltas"],[name="arredondamento_desconto"]'
                    );
                    inputs.forEach(input => {
                        input.addEventListener('input', () => this.calcular());
                    });
                },

                lerValor(nome) {
                    const el = document.querySelector(`[name="${nome}"]`);
                    if (!el) return 0;
                    const val = el.value.replace(/\./g, '').replace(',', '.');
                    return parseFloat(val) || 0;
                },

                calcular() {
                    this.totalProventos = this.camposProventos.reduce(
                        (acc, c) => acc + this.lerValor(c), 0
                    );
                    this.totalDescontos = this.camposDescontos.reduce(
                        (acc, c) => acc + this.lerValor(c), 0
                    );
                    this.salarioLiquido = this.totalProventos - this.totalDescontos;
                },

                formatarBR(valor) {
                    return valor.toLocaleString('pt-BR', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                }
            }
        }
    </script>
@endpush
