@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Cabeçalho --}}
            <div class="flex flex-col gap-4 mb-6 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="flex items-center gap-2 mb-1 text-sm text-gray-500">
                        <a href="{{ route('rh.faixa-inss.index') }}" class="hover:text-indigo-600">Tabelas INSS</a>
                        <span>/</span>
                        <span class="font-medium text-gray-700">Editar Tabela</span>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900">Editar Tabela INSS</h1>
                    <p class="mt-1 text-sm text-gray-500">Preencha os dados da tabela. Campos marcados com <span
                            class="text-red-500">*</span> são obrigatórios.</p>
                </div>

                <a href="{{ route('rh.faixa-inss.index') }}"
                    class="inline-flex items-center gap-1.5 rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 self-start sm:self-auto">
                    <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M17 10a.75.75 0 01-.75.75H5.612l4.158 3.96a.75.75 0 11-1.04 1.08l-5.5-5.25a.75.75 0 010-1.08l5.5-5.25a.75.75 0 111.04 1.08L5.612 9.25H16.25A.75.75 0 0117 10z"
                            clip-rule="evenodd" />
                    </svg>
                    Voltar
                </a>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('rh.faixa-inss.update', $tabela) }}" x-data="{
                        faixas: {{ json_encode(
                            $tabela->faixas->map(
                                fn($f) => [
                                    'ordem' => $f->ordem,
                                    'teto' => $f->teto,
                                    'aliquota' => $f->aliquota,
                                    'deducao' => $f->deducao,
                                ],
                            ),
                        ) }},
                        adicionarFaixa() {
                            this.faixas.push({
                                ordem: this.faixas.length + 1,
                                teto: '',
                                aliquota: '',
                                deducao: ''
                            });
                        },
                        removerFaixa(index) {
                            this.faixas.splice(index, 1);
                            this.renumerar();
                        },
                        renumerar() {
                            this.faixas.forEach((faixa, index) => {
                                faixa.ordem = index + 1;
                            });
                        }
                    }">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                            <!-- Ano -->
                            <div>
                                <x-input-label for="ano_vigencia" value="Ano de Vigência *" />
                                <x-text-input id="ano_vigencia" name="ano_vigencia" type="number" min="2000"
                                    max="2100" class="mt-1 block w-full"
                                    value="{{ old('ano_vigencia', $tabela->ano_vigencia) }}" required />
                                <x-input-error :messages="$errors->get('ano_vigencia')" class="mt-2" />
                            </div>

                            <!-- Vigência Início -->
                            <div>
                                <x-input-label for="vigencia_inicio" value="Início da Vigência *" />
                                <x-text-input id="vigencia_inicio" name="vigencia_inicio" type="date"
                                    class="mt-1 block w-full"
                                    value="{{ old('vigencia_inicio', $tabela->vigencia_inicio?->format('Y-m-d')) }}"
                                    required />
                                <x-input-error :messages="$errors->get('vigencia_inicio')" class="mt-2" />
                            </div>

                            <!-- Vigência Fim -->
                            <div>
                                <x-input-label for="vigencia_fim" value="Fim da Vigência" />
                                <x-text-input id="vigencia_fim" name="vigencia_fim" type="date" class="mt-1 block w-full"
                                    value="{{ old('vigencia_fim', $tabela->vigencia_fim?->format('Y-m-d')) }}" />
                                <x-input-error :messages="$errors->get('vigencia_fim')" class="mt-2" />
                            </div>

                            <!-- Ativo -->
                            <div class="flex items-center mt-6">
                                <input id="ativo" name="ativo" type="checkbox" value="1"
                                    {{ old('ativo', $tabela->ativo) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                <x-input-label for="ativo" value="Tabela ativa" class="ml-2" />
                            </div>
                        </div>

                        <!-- Descrição -->
                        <div class="mb-6">
                            <x-input-label for="descricao" value="Descrição" />
                            <x-text-input id="descricao" name="descricao" type="text" maxlength="255"
                                class="mt-1 block w-full" value="{{ old('descricao', $tabela->descricao) }}" />
                            <x-input-error :messages="$errors->get('descricao')" class="mt-2" />
                        </div>

                        {{-- Salário Família --}}
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">💰 Salário Família</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="limite_salario_familia" value="Teto para direito (R$)" />
                                    <x-text-input id="limite_salario_familia" name="limite_salario_familia" type="number"
                                        step="0.01" min="0" class="mt-1 block w-full"
                                        value="{{ old('limite_salario_familia', $tabela->limite_salario_familia ?? '') }}"
                                        required />
                                    <p class="mt-1 text-xs text-gray-500">Valor atual: R$ 1.980,38</p>
                                    <x-input-error :messages="$errors->get('limite_salario_familia')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="valor_salario_familia" value="Cota por dependente (R$)" />
                                    <x-text-input id="valor_salario_familia" name="valor_salario_familia" type="number"
                                        step="0.01" min="0" class="mt-1 block w-full"
                                        value="{{ old('valor_salario_familia', $tabela->valor_salario_familia ?? '') }}"
                                        required />
                                    <p class="mt-1 text-xs text-gray-500">Valor atual: R$ 67,54</p>
                                    <x-input-error :messages="$errors->get('valor_salario_familia')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Faixas -->
                        <div class="mb-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium text-gray-900">Faixas de Contribuição</h3>
                                <button type="button" @click="adicionarFaixa"
                                    class="inline-flex items-center px-3 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                    + Adicionar Faixa
                                </button>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                Ordem</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                Teto
                                                (R$) *</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                Alíquota (%) *</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                Dedução (R$)</th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                                                Ação</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        <template x-for="(faixa, index) in faixas" :key="index">
                                            <tr>
                                                <td class="px-4 py-3">
                                                    <x-text-input type="number" min="1"
                                                        x-bind:name="'faixas[' + index + '][ordem]'" x-model="faixa.ordem"
                                                        class="block w-20" readonly />
                                                </td>
                                                <td class="px-4 py-3">
                                                    <x-text-input type="number" step="0.01" min="0"
                                                        x-bind:name="'faixas[' + index + '][teto]'" x-model="faixa.teto"
                                                        required />
                                                </td>
                                                <td class="px-4 py-3">
                                                    <x-text-input type="number" step="0.0001" min="0"
                                                        max="1" x-bind:name="'faixas[' + index + '][aliquota]'"
                                                        x-model="faixa.aliquota" required />
                                                </td>
                                                <td class="px-4 py-3">
                                                    <x-text-input type="number" step="0.01" min="0"
                                                        x-bind:name="'faixas[' + index + '][deducao]'"
                                                        x-model="faixa.deducao" />
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <button type="button" @click="removerFaixa(index)"
                                                        class="text-red-600 hover:text-red-900 font-medium text-sm">
                                                        Remover
                                                    </button>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>

                            <x-input-error :messages="$errors->get('faixas')" class="mt-2" />

                            <p x-show="faixas.length === 0" class="mt-4 text-sm text-gray-500 text-center">
                                Nenhuma faixa adicionada. Clique em "Adicionar Faixa".
                            </p>
                        </div>

                        <!-- Botões -->
                        <div class="flex items-center justify-end gap-4">
                            <a href="{{ route('rh.faixa-inss.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                Cancelar
                            </a>
                            <x-primary-button>
                                Atualizar Tabela
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
