{{-- resources/views/rh/ferias/create.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="max-w-3xl px-4 py-6 mx-auto sm:px-6 lg:px-8" x-data="agendarFeriasForm()">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">📅 Agendar Férias</h1>
                <p class="mt-1 text-sm text-gray-500">
                    Funcionário: <span class="font-semibold text-indigo-600">{{ $funcionario->nome_completo }}</span>
                </p>
            </div>
            <a href="{{ url()->previous() }}"
                class="inline-flex items-center px-4 py-2 text-sm text-gray-700 transition-colors bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                ← Voltar
            </a>
        </div>

        {{-- Alertas de erro --}}
        @if ($errors->any())
            <div class="p-4 mb-6 border-l-4 border-red-400 rounded-r-lg bg-red-50">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Erro ao agendar férias:</h3>
                        <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('rh.ferias.store', $funcionario) }}" method="POST">
            @csrf

            <div class="p-6 space-y-6 bg-white border border-gray-100 shadow-sm rounded-xl">

                {{-- Informações do Funcionário --}}
                <div class="p-5 bg-gradient-to-r from-indigo-50 to-blue-50 rounded-xl">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500">Nome:</span>
                            <p class="font-semibold text-gray-900">{{ $funcionario->nome_completo }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500">Cargo:</span>
                            <p class="font-semibold text-gray-900">{{ $funcionario->cargo?->titulo ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500">Departamento:</span>
                            <p class="font-semibold text-gray-900">{{ $funcionario->departamento?->nome ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500">Admissão:</span>
                            <p class="font-semibold text-gray-900">
                                {{ $funcionario->contrato?->data_admissao
                                    ? \Carbon\Carbon::parse($funcionario->contrato->data_admissao)->format('d/m/Y')
                                    : 'N/A' }}
                            </p>
                        </div>
                        @if ($funcionario->periodo_aquisitivo_inicio && $funcionario->periodo_aquisitivo_fim)
                            <div>
                                <span class="text-gray-500">Período Aquisitivo:</span>
                                <p class="font-semibold text-gray-900">
                                    {{ \Carbon\Carbon::parse($funcionario->periodo_aquisitivo_inicio)->format('d/m/Y') }}
                                    →
                                    {{ \Carbon\Carbon::parse($funcionario->periodo_aquisitivo_fim)->format('d/m/Y') }}
                                </p>
                            </div>
                            <div>
                                <span class="text-gray-500">Vencimento:</span>
                                <p
                                    class="font-semibold {{ $funcionario->ferias_vencidas ? 'text-red-600' : 'text-gray-900' }}">
                                    {{ $funcionario->ferias_vencimento
                                        ? \Carbon\Carbon::parse($funcionario->ferias_vencimento)->format('d/m/Y')
                                        : 'N/A' }}
                                    @if ($funcionario->ferias_vencidas)
                                        <span class="ml-1 text-xs text-red-500">(Vencida - Dobro)</span>
                                    @endif
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Tipo de Período --}}
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-700">📋 Tipo de Período</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label
                            class="relative flex items-center p-4 transition-colors border-2 cursor-pointer rounded-xl hover:bg-gray-50"
                            :class="tipo === 'programada' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200'">
                            <input type="radio" name="tipo" value="programada" x-model="tipo" class="sr-only">
                            <input type="radio" name="status" value="aprovada" x-model="status" class="sr-only" checked>

                            <div class="flex items-center gap-3">
                                <div class="flex items-center justify-center w-10 h-10 text-xl bg-indigo-100 rounded-lg">📅
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">Programada</p>
                                    <p class="text-xs text-gray-500">Agendamento futuro</p>
                                </div>
                            </div>
                            <div x-show="tipo === 'programada'" class="absolute text-indigo-600 top-2 right-2">✅</div>
                        </label>

                        <label
                            class="relative flex items-center p-4 transition-colors border-2 cursor-pointer rounded-xl hover:bg-gray-50"
                            :class="tipo === 'efetiva' ? 'border-green-500 bg-green-50' : 'border-gray-200'">
                            <input type="radio" name="tipo" value="efetiva" x-model="tipo" class="sr-only">
                            <div class="flex items-center gap-3">
                                <div class="flex items-center justify-center w-10 h-10 text-xl bg-green-100 rounded-lg">✅
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">Efetiva</p>
                                    <p class="text-xs text-gray-500">Já gozada (retroativo)</p>
                                </div>
                            </div>
                            <div x-show="tipo === 'efetiva'" class="absolute text-green-600 top-2 right-2">✅</div>
                        </label>
                    </div>
                    @error('tipo')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Datas --}}
                <div>
                    <label class="block mb-3 text-sm font-medium text-gray-700">📅 Período de Férias</label>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-1 text-xs font-medium text-gray-500">
                                Data de Início <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="data_inicio" x-model="dataInicio" @change="atualizarDataFim()"
                                value="{{ old('data_inicio') }}"
                                min="{{ $funcionario->contrato?->data_admissao ? \Carbon\Carbon::parse($funcionario->contrato->data_admissao)->addYear()->format('Y-m-d') : '' }}"
                                class="block w-full px-4 py-3 text-sm transition-all border border-gray-200 input-focus rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200"
                                required />
                            @error('data_inicio')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block mb-1 text-xs font-medium text-gray-500">
                                Data de Fim <span class="text-red-500">*</span>
                                <button type="button" @click="resetarDataFim()"
                                    class="ml-2 text-xs font-normal text-indigo-600 hover:text-indigo-800">
                                    🔄 30 dias
                                </button>
                            </label>
                            <input type="date" name="data_fim" x-model="dataFim" @change="calcularDuracao()"
                                value="{{ old('data_fim') }}"
                                class="block w-full px-4 py-3 text-sm transition-all border border-gray-200 input-focus rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200"
                                required />
                            @error('data_fim')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Cards Informativos --}}
                <div class="grid grid-cols-3 gap-3">
                    <div class="p-3 text-center bg-blue-50 rounded-xl">
                        <span class="text-xs font-medium text-blue-600">📅 Dias de Férias</span>
                        <p class="text-2xl font-bold text-blue-700" x-text="duracao + ' dias'"></p>
                    </div>
                    <div class="p-3 text-center bg-green-50 rounded-xl">
                        <span class="text-xs font-medium text-green-600">📅 Retorno</span>
                        <p class="text-lg font-bold text-green-700" x-text="dataRetorno"></p>
                    </div>
                    <div class="p-3 text-center bg-purple-50 rounded-xl">
                        <span class="text-xs font-medium text-purple-600">💵 Abono (1/3)</span>
                        <p class="text-lg font-bold text-purple-700" x-text="abonoDias + ' dias'"></p>
                    </div>
                </div>

                {{-- Abono Pecuniário --}}
                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                    <input type="hidden" name="abono_pecuniario" value="0">
                    <input id="abono_pecuniario" type="checkbox" name="abono_pecuniario" value="1" x-model="abono"
                        @change="calcularDuracao()"
                        class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" />
                    <label for="abono_pecuniario" class="text-sm text-gray-700 cursor-pointer">
                        <span class="font-medium">Abono Pecuniário</span>
                        <span class="ml-1 text-gray-400">(vender 1/3 das férias = <span
                                x-text="abonoDias + ' dias'"></span>)</span>
                    </label>
                </div>

                {{-- Observação --}}
                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-700">📝 Observação</label>
                    <textarea name="observacao" rows="3"
                        class="block w-full px-4 py-3 text-sm transition-all border border-gray-200 resize-none input-focus rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200"
                        placeholder="Ex: Férias coletivas, observações importantes...">{{ old('observacao') }}</textarea>
                    @error('observacao')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Botões --}}
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ url()->previous() }}"
                        class="px-5 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-sm font-medium transition-colors">
                        Cancelar
                    </a>
                    <button type="submit"
                        class="px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:from-indigo-700 hover:to-purple-700 text-sm font-semibold shadow-md hover:shadow-lg transition-all">
                        💾 Agendar Férias
                    </button>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            function agendarFeriasForm() {
                return {
                    tipo: '{{ old('tipo', 'programada') }}',
                    dataInicio: '{{ old('data_inicio') }}',
                    dataFim: '{{ old('data_fim') }}',
                    abono: {{ old('abono_pecuniario') ? 'true' : 'false' }},
                    duracao: 0,
                    status: 'aprovada',

                    init() {
                        if (this.dataInicio && this.dataFim) {
                            this.calcularDuracao();
                        }
                    },

                    atualizarDataFim() {
                        if (!this.dataInicio) return;

                        const inicio = new Date(this.dataInicio + 'T12:00:00');
                        const fim = new Date(inicio);
                        fim.setDate(fim.getDate() + 29); // 30 dias corridos

                        const ano = fim.getFullYear();
                        const mes = String(fim.getMonth() + 1).padStart(2, '0');
                        const dia = String(fim.getDate()).padStart(2, '0');

                        this.dataFim = `${ano}-${mes}-${dia}`;
                        this.calcularDuracao();
                    },

                    resetarDataFim() {
                        this.atualizarDataFim();
                    },

                    calcularDuracao() {
                        if (!this.dataInicio || !this.dataFim) {
                            this.duracao = 0;
                            return;
                        }

                        const inicio = new Date(this.dataInicio + 'T12:00:00');
                        const fim = new Date(this.dataFim + 'T12:00:00');

                        const diff = Math.round((fim - inicio) / (1000 * 60 * 60 * 24)) + 1;
                        this.duracao = diff > 0 ? diff : 0;
                    },

                    get dataRetorno() {
                        if (!this.dataFim) return '--/--/----';

                        const fim = new Date(this.dataFim + 'T12:00:00');
                        fim.setDate(fim.getDate() + 1);

                        return fim.toLocaleDateString('pt-BR');
                    },

                    get abonoDias() {
                        return Math.floor(this.duracao / 3);
                    }
                }
            }
        </script>
    @endpush
@endsection
