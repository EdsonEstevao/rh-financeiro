@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                📊 Tabela INSS → Nova Faixa
            </h2>
        </div>
    </div>
    <div class="py-6">

        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <form action="{{ route('rh.faixa-inss.store') }}" method="POST" class="p-6 space-y-5">
                    @csrf

                    {{-- Número da Faixa --}}
                    <div>
                        <x-input-label for="numero_faixa" value="Número da Faixa" />
                        <x-text-input id="numero_faixa" name="numero_faixa" type="number" min="1"
                            class="mt-1 block w-full" :value="old('numero_faixa')" required />
                        <x-input-error :messages="$errors->get('numero_faixa')" class="mt-1" />
                    </div>

                    {{-- Limites --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="limite_inferior" value="Limite Inferior (R$)" />
                            <x-text-input id="limite_inferior" name="limite_inferior" type="number" step="0.01"
                                min="0" class="mt-1 block w-full" :value="old('limite_inferior', '0.00')" required />
                            <x-input-error :messages="$errors->get('limite_inferior')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="limite_superior" value="Limite Superior (R$)" />
                            <x-text-input id="limite_superior" name="limite_superior" type="number" step="0.01"
                                min="0" class="mt-1 block w-full" :value="old('limite_superior')"
                                placeholder="Deixe vazio se for a última faixa" />
                            <x-input-error :messages="$errors->get('limite_superior')" class="mt-1" />
                        </div>
                    </div>

                    {{-- Alíquota e Parcela --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="aliquota" value="Alíquota (%)" />
                            <x-text-input id="aliquota" name="aliquota" type="number" step="0.01" min="0"
                                max="100" class="mt-1 block w-full" :value="old('aliquota')" required />
                            <x-input-error :messages="$errors->get('aliquota')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="parcela_deduzir" value="Parcela a Deduzir (R$)" />
                            <x-text-input id="parcela_deduzir" name="parcela_deduzir" type="number" step="0.01"
                                min="0" class="mt-1 block w-full" :value="old('parcela_deduzir', '0.00')" required />
                            <x-input-error :messages="$errors->get('parcela_deduzir')" class="mt-1" />
                        </div>
                    </div>

                    {{-- Vigência --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="vigencia_inicio" value="Início da Vigência" />
                            <x-text-input id="vigencia_inicio" name="vigencia_inicio" type="date"
                                class="mt-1 block w-full" :value="old('vigencia_inicio')" required />
                            <x-input-error :messages="$errors->get('vigencia_inicio')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="vigencia_fim" value="Fim da Vigência" />
                            <x-text-input id="vigencia_fim" name="vigencia_fim" type="date" class="mt-1 block w-full"
                                :value="old('vigencia_fim')" placeholder="Opcional" />
                            <x-input-error :messages="$errors->get('vigencia_fim')" class="mt-1" />
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="flex items-center gap-3">
                        <input id="ativa" name="ativa" type="checkbox" value="1"
                            {{ old('ativa', true) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                        <x-input-label for="ativa" value="Faixa ativa" />
                    </div>

                    {{-- Botões --}}
                    <div class="flex justify-end gap-3 pt-4 border-t dark:border-gray-700">
                        <a href="{{ route('rh.faixa-inss.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-gray-700 hover:bg-gray-300 transition">
                            Cancelar
                        </a>
                        <x-primary-button>💾 Salvar Faixa</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
