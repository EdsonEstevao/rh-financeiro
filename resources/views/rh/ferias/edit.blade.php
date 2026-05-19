{{-- resources/views/rh/ferias/edit.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6" x-data="feriasForm()">

        {{-- Header --}}
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Editar Período de Férias</h1>
                <p class="mt-1 text-sm text-gray-500">
                    Funcionário: <span class="font-semibold">{{ $periodo->funcionario->nome_completo }}</span>
                </p>
            </div>
            <a href="{{ route('rh.ferias.index') }}"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50">
                ← Voltar
            </a>
        </div>

        {{-- ✅ Alertas de erro --}}
        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Erro ao atualizar período:</h3>
                        <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('rh.ferias.update', $periodo) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="bg-white rounded-lg shadow p-6 space-y-6">

                {{-- Informações do Funcionário --}}
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500">Nome:</span>
                            <p class="font-semibold">{{ $periodo->funcionario->nome_completo }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500">Cargo:</span>
                            <p class="font-semibold">{{ $periodo->funcionario->cargo?->titulo ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500">Admissão:</span>
                            {{-- ✅ CORRIGIDO: data_admissao está no contrato --}}
                            <p class="font-semibold">
                                {{ $periodo->funcionario->contrato?->data_admissao
                                    ? \Carbon\Carbon::parse($periodo->funcionario->contrato->data_admissao)->format('d/m/Y')
                                    : 'N/A' }}
                            </p>
                        </div>
                        <div>
                            <span class="text-gray-500">Período:</span>
                            <p class="font-semibold">{{ $periodo->numero_periodo }}º período</p>
                        </div>
                    </div>
                </div>

                {{-- ✅ NOVO: Tipo de Período --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">📋 Tipo de Período</label>
                    <select name="tipo"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="prevista" @selected(old('tipo', $periodo->tipo) === 'prevista')>📌 Prevista (gerada na admissão)</option>
                        <option value="programada" @selected(old('tipo', $periodo->tipo) === 'programada')>📅 Programada (agendada pelo RH)</option>
                        <option value="efetiva" @selected(old('tipo', $periodo->tipo) === 'efetiva')>✅ Efetiva (já gozada)</option>
                    </select>
                    @error('tipo')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">
                        ⚠️ Períodos do tipo "Prevista" são gerados automaticamente e não validam sobreposição.
                    </p>
                </div>

                {{-- Datas --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">📅 Período de Férias</label>
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Data de Início <span
                                    class="text-red-500">*</span></label>
                            <input type="date" name="data_inicio" x-model="dataInicio" @change="atualizarDataFim()"
                                value="{{ old('data_inicio', $periodo->data_inicio->format('Y-m-d')) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required />

                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">
                                Data de Fim <span class="text-red-500">*</span>
                                <button type="button" @click="resetarDataFim()"
                                    class="text-xs text-indigo-600 hover:text-indigo-800 ml-2">
                                    🔄 30 dias padrão
                                </button>
                            </label>
                            <input type="date" name="data_fim" x-model="dataFim" @change="calcularDuracao()"
                                value="{{ old('data_fim', $periodo->data_fim->format('Y-m-d')) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required />
                            @error('data_fim')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-span-2 ">
                            @error('data_inicio')
                                <p class="mt-1 text-xs text-red-600 bg-red-50 rounded-lg p-3 border-l-4 border-red-300">
                                    {{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Duração e Informações --}}
                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-blue-50 rounded-lg p-3 text-center">
                        <span class="text-xs text-blue-600">📅 Dias de Férias</span>
                        <p class="text-2xl font-bold text-blue-700" x-text="duracao + ' dias'"></p>
                    </div>
                    <div class="bg-green-50 rounded-lg p-3 text-center">
                        <span class="text-xs text-green-600">📅 Retorno ao Trabalho</span>
                        <p class="text-lg font-bold text-green-700" x-text="dataRetorno"></p>
                    </div>
                    <div class="bg-purple-50 rounded-lg p-3 text-center">
                        <span class="text-xs text-purple-600">💵 Abono Pecuniário</span>
                        <p class="text-lg font-bold text-purple-700" x-text="abonoDias + ' dias'"></p>
                    </div>
                </div>

                {{-- Abono Pecuniário --}}
                <div class="flex items-center gap-3">
                    <input type="hidden" name="abono_pecuniario" value="0">
                    <input id="abono_pecuniario" type="checkbox" name="abono_pecuniario" value="1"
                        @change="calcularDuracao()"
                        {{ old('abono_pecuniario', $periodo->abono_pecuniario) ? 'checked' : '' }}
                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                    <label for="abono_pecuniario" class="text-sm text-gray-700">
                        Abono Pecuniário (vender 1/3 das férias = <span x-text="abonoDias + ' dias'"></span>)
                    </label>
                </div>

                {{-- Status --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Status <span
                            class="text-red-500">*</span></label>
                    <select name="status"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="planejada" @selected(old('status', $periodo->status) === 'planejada')>📋 Planejada</option>
                        <option value="aprovada" @selected(old('status', $periodo->status) === 'aprovada')>✅ Aprovada</option>
                        <option value="gozada" @selected(old('status', $periodo->status) === 'gozada')>🏖️ Gozada</option>
                        <option value="cancelada" @selected(old('status', $periodo->status) === 'cancelada')>❌ Cancelada</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Observação --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Observação</label>
                    <textarea name="observacao" rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                        placeholder="Observações sobre este período de férias...">{{ old('observacao', $periodo->observacao) }}</textarea>
                    @error('observacao')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Botões --}}
                <div class="flex justify-end gap-3 pt-4 border-t">
                    <a href="{{ route('rh.ferias.index') }}"
                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 text-sm">
                        Cancelar
                    </a>
                    <button type="submit"
                        class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm font-semibold">
                        💾 Salvar Alterações
                    </button>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            function feriasForm() {
                return {
                    dataInicio: '{{ old('data_inicio', $periodo->data_inicio->format('Y-m-d')) }}',
                    dataFim: '{{ old('data_fim', $periodo->data_fim->format('Y-m-d')) }}',
                    duracao: {{ $periodo->data_inicio->diffInDays($periodo->data_fim) + 1 }},

                    init() {
                        this.calcularDuracao();
                    },

                    atualizarDataFim() {
                        if (!this.dataInicio) return;

                        const inicio = new Date(this.dataInicio + 'T12:00:00');
                        const fim = new Date(inicio);
                        fim.setDate(fim.getDate() + 29);

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
