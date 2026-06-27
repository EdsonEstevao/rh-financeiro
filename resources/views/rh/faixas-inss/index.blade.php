@extends('layouts.app')
@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Tabelas INSS
    </h2>
@endsection

@section('content')
    <div class="py-12" x-data="inssIndex({{ Js::from($faixasPorTabela) }})">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <!-- Mensagens -->
            @if (session('success'))
                <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)"
                    class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    <span class="block sm:inline">{{ session('success') }}</span>
                    <button @click="show = false" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                        <span class="text-green-700 font-bold">&times;</span>
                    </button>
                </div>
            @endif

            @if (session('error'))
                <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)"
                    class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <span class="block sm:inline">{{ session('error') }}</span>
                    <button @click="show = false" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                        <span class="text-red-700 font-bold">&times;</span>
                    </button>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium text-gray-900">Tabelas de INSS</h3>
                        @can('faixa-inss.create')
                            <a href="{{ route('rh.faixa-inss.create') }}"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                + Nova Tabela
                            </a>
                        @endcan
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Ano</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Descrição</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Vigência</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Faixas</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Ações</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($tabelas as $tabela)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $tabela->ano_vigencia }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            {{ $tabela->descricao ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $tabela->vigencia_inicio->format('d/m/Y') }}
                                            @if ($tabela->vigencia_fim)
                                                até {{ $tabela->vigencia_fim->format('d/m/Y') }}
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if ($tabela->ativo)
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Ativa
                                                </span>
                                            @else
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    Inativa
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            {{ $tabela->faixas->count() }} faixa(s)
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button type="button" @click="openModal({{ $tabela->id }})"
                                                class="text-blue-600 hover:text-blue-900" title="Visualizar faixas">
                                                👁 Faixas
                                            </button>
                                            @can('faixa-inss.edit')
                                                <a href="{{ route('rh.faixa-inss.edit', $tabela) }}"
                                                    class="text-indigo-600 hover:text-indigo-900 mr-3">
                                                    Editar
                                                </a>
                                            @endcan
                                            @can('faixa-inss.delete')
                                                <form action="{{ route('rh.faixa-inss.destroy', $tabela) }}" method="POST"
                                                    class="inline"
                                                    onsubmit="return confirm('Tem certeza que deseja remover esta tabela?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                                        Remover
                                                    </button>
                                                </form>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500">
                                            Nenhuma tabela INSS cadastrada.
                                            <a href="{{ route('rh.faixa-inss.create') }}"
                                                class="text-indigo-600 hover:underline ml-1">
                                                Cadastrar agora
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $tabelas->links() }}
                    </div>
                </div>
            </div>
        </div>
        {{-- 🔵 MODAL DE FAIXAS (único, fora do loop) --}}
        <div x-show="isModalOpen" x-transition.opacity.duration.200ms
            class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">

            {{-- Overlay --}}
            <div class="fixed inset-0 bg-gray-900/60" @click="closeModal()"></div>

            {{-- Card do modal --}}
            <div @click.outside="closeModal()"
                class="relative bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[85vh] overflow-hidden flex flex-col">

                {{-- Cabeçalho --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">
                        Faixas INSS — <span x-text="selectedTabelaDesc"></span>
                    </h3>
                    <button type="button" @click="closeModal()" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Corpo — tabela de faixas --}}
                <div class="overflow-y-auto p-6 flex-1">
                    <template x-if="selectedFaixas.length > 0">
                        <div class="overflow-x-auto rounded-lg border border-gray-200">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-medium text-gray-600">Ordem</th>
                                        <th class="px-4 py-3 text-right font-medium text-gray-600">Teto (R$)</th>
                                        <th class="px-4 py-3 text-right font-medium text-gray-600">Alíquota (%)</th>
                                        <th class="px-4 py-3 text-right font-medium text-gray-600">Dedução (R$)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    <template x-for="(faixa, i) in selectedFaixas" :key="i">
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3" x-text="faixa.ordem"></td>
                                            <td class="px-4 py-3 text-right" x-text="formatMoney(faixa.teto)"></td>
                                            <td class="px-4 py-3 text-right" x-text="formatPercent(faixa.aliquota)"></td>
                                            <td class="px-4 py-3 text-right" x-text="formatMoney(faixa.deducao)"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </template>
                    <template x-if="selectedFaixas.length === 0">
                        <p class="text-center text-gray-500 py-8">Nenhuma faixa cadastrada para esta tabela.</p>
                    </template>
                </div>

                {{-- Rodapé --}}
                <div class="flex justify-end px-6 py-3 border-t border-gray-200 bg-gray-50">
                    <button type="button" @click="closeModal()"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition">
                        Fechar
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        function inssIndex(faixasPorTabela) {
            return {
                isModalOpen: false,
                selectedFaixas: [],
                selectedTabelaDesc: '',

                openModal(tabelaId) {
                    const data = faixasPorTabela[tabelaId];
                    if (data) {
                        this.selectedFaixas = data.faixas;
                        this.selectedTabelaDesc = data.descricao;
                        this.isModalOpen = true;
                    }
                },

                closeModal() {
                    this.isModalOpen = false;
                    this.selectedFaixas = [];
                    this.selectedTabelaDesc = '';
                },

                formatMoney(value) {
                    return 'R$ ' + parseFloat(value).toLocaleString('pt-BR', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                },

                formatPercent(value) {
                    return (parseFloat(value) * 100).toLocaleString('pt-BR', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }) + '%';
                }
            }
        }
    </script>
