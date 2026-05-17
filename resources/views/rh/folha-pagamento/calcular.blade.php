{{-- resources/views/rh/folha-pagamento/calcular.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

        {{-- Breadcrumb --}}
        <div>
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-1">
                <a href="{{ route('rh.folha-pagamento.index') }}" class="hover:text-indigo-600 transition-colors">
                    Folha de Pagamento
                </a>
                <span>/</span>
                <span class="text-gray-700 font-medium">Calcular</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">Calcular Folha de Pagamento</h1>
            <p class="mt-1 text-sm text-gray-500">
                Selecione o funcionário e a competência para visualizar o cálculo.
            </p>
        </div>

        {{-- Card --}}
        <div class="rounded-lg bg-white shadow ring-1 ring-black/5 overflow-hidden">
            <div class="border-b border-gray-100 bg-gray-50 px-6 py-3">
                <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">
                    Parâmetros de Cálculo
                </h2>
            </div>

            <form method="GET" action="{{ route('rh.folha-pagamento.calcular') }}" class="p-6 space-y-5">

                {{-- Funcionário --}}
                <div>
                    <label for="funcionario_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Funcionário <span class="text-red-500">*</span>
                    </label>
                    <select id="funcionario_id" name="funcionario_id" required
                        class="block w-full rounded-md border-gray-300 text-sm shadow-sm
                           focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Selecione...</option>
                        @foreach ($funcionarios as $func)
                            <option value="{{ $func->id }}"
                                {{ request('funcionario_id') == $func->id ? 'selected' : '' }}>
                                {{ $func->nome_completo }}
                                — {{ optional($func->cargo)->titulo ?? 'Sem cargo' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Competência --}}
                <div>
                    <label for="competencia" class="block text-sm font-medium text-gray-700 mb-1">
                        Competência (Mês/Ano) <span class="text-red-500">*</span>
                    </label>
                    <input type="month" id="competencia" name="competencia" required
                        value="{{ request('competencia', now()->format('Y-m')) }}" max="{{ now()->format('Y-m') }}"
                        class="block w-full rounded-md border-gray-300 text-sm shadow-sm
                           focus:border-indigo-500 focus:ring-indigo-500" />
                    <p class="mt-1 text-xs text-gray-400">
                        Selecione o mês de referência da folha de pagamento.
                    </p>
                </div>

                {{-- Ações --}}
                <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-5">
                    <a href="{{ route('rh.folha-pagamento.index') }}"
                        class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit"
                        class="inline-flex items-center gap-1.5 rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition-colors">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M15.312 11.424a5.5 5.5 0 01-9.201 2.466l-.312-.311h2.433a.75.75 0 000-1.5H3.989a.75.75 0 00-.75.75v4.242a.75.75 0 001.5 0v-2.43l.31.31a7 7 0 0011.712-3.138.75.75 0 00-1.449-.39zm1.23-3.723a.75.75 0 00.219-.53V2.929a.75.75 0 00-1.5 0V5.36l-.31-.31A7 7 0 003.239 8.188a.75.75 0 101.448.389A5.5 5.5 0 0113.89 6.11l.311.31h-2.432a.75.75 0 000 1.5h4.243a.75.75 0 00.53-.219z"
                                clip-rule="evenodd" />
                        </svg>
                        Calcular
                    </button>
                </div>
            </form>
        </div>

        {{-- Info CLT --}}
        <div class="rounded-lg border border-blue-200 bg-blue-50 p-4">
            <div class="flex gap-3">
                <svg class="h-5 w-5 flex-shrink-0 text-blue-500 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z"
                        clip-rule="evenodd" />
                </svg>
                <div class="text-sm text-blue-700 space-y-1">
                    <p class="font-semibold">Cálculos aplicados:</p>
                    <ul class="list-disc list-inside space-y-0.5 text-blue-600">
                        <li>INSS — tabela progressiva vigente</li>
                        <li>IRRF — tabela progressiva com deduções de dependentes</li>
                        <li>Vale-Transporte — desconto de 6% sobre salário bruto</li>
                        <li>Horas extras e adicionais conforme CLT</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
