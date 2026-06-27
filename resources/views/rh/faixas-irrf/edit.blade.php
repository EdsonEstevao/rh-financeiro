@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Editar Tabela IRRF') }}
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('rh.faixa-irrf.update', $tabela) }}" x-data="{
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
                            <div>
                                <x-input-label for="ano_vigencia" value="Ano de Vigência *" />
                                <x-text-input id="ano_vigencia" name="ano_vigencia" type="number" min="2000"
                                    max="2100" class="mt-1 block w-full"
                                    value="{{ old('ano_vigencia', $tabela->ano_vigencia) }}" required />
                                <x-input-error :messages="$errors->get('ano_vigencia')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="vigencia_inicio" value="Início da Vigência *" />
                                <x-text-input id="vigencia_inicio" name="vigencia_inicio" type="date"
                                    class="mt-1 block w-full"
                                    value="{{ old('vigencia_inicio', $tabela->vigencia_inicio?->format('Y-m-d')) }}"
                                    required />
                                <x-input-error :messages="$errors->get('vigencia_inicio')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="vigencia_fim" value="Fim da Vigência" />
                                <x-text-input id="vigencia_fim" name="vigencia_fim" type="date" class="mt-1 block w-full"
                                    value="{{ old('vigencia_fim', $tabela->vigencia_fim?->format('Y-m-d')) }}" />
                                <x-input-error :messages="$errors->get('vigencia_fim')" class="mt-2" />
                            </div>

                            <div class="flex items-center mt-6">
                                <input id="ativo" name="ativo" type="checkbox" value="1"
                                    {{ old('ativo', $tabela->ativo) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                <x-input-label for="ativo" value="Tabela ativa" class="ml-2" />
                            </div>
                        </div>

                        <div class="mb-6">
                            <x-input-label for="descricao" value="Descrição" />
                            <x-text-input id="descricao" name="descricao" type="text" maxlength="255"
                                class="mt-1 block w-full" value="{{ old('descricao', $tabela->descricao) }}" />
                            <x-input-error :messages="$errors->get('descricao')" class="mt-2" />
                        </div>

                        <!-- Deduções IRRF -->
                        <div
                            class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                            <div>
                                <x-input-label for="deducao_dependente" value="Dedução por Dependente (R$)" />
                                <x-text-input id="deducao_dependente" name="deducao_dependente" type="number"
                                    step="0.01" min="0" class="mt-1 block w-full"
                                    value="{{ old('deducao_dependente', $tabela->deducao_dependente) }}" />
                                <x-input-error :messages="$errors->get('deducao_dependente')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="deducao_pensao" value="Dedução de Pensão (R$)" />
                                <x-text-input id="deducao_pensao" name="deducao_pensao" type="number" step="0.01"
                                    min="0" class="mt-1 block w-full"
                                    value="{{ old('deducao_pensao', $tabela->deducao_pensao) }}" />
                                <x-input-error :messages="$errors->get('deducao_pensao')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Faixas -->
                        <div class="mb-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium text-gray-900">Faixas de IRRF</h3>
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
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Teto
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
                            <a href="{{ route('rh.faixa-irrf.index') }}"
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
