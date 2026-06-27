@props(['faixasInss' => collect(), 'faixasIrrf' => collect()])

<div x-data="faixasModal()" x-cloak>
    {{-- Botão para abrir o modal --}}
    <button type="button" @click="open()"
        class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
        </svg>
        <span>Tabela de Faixas</span>
    </button>

    {{-- Modal --}}
    <div x-show="isOpen" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center p-4"
        style="display: none;">

        {{-- Overlay --}}
        <div class="fixed inset-0 bg-gray-900/60" @click="close()"></div>

        {{-- Conteúdo --}}
        <div @click.outside="close()"
            class="relative bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col">

            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">
                    Tabelas de Faixas Vigentes
                </h3>
                <button type="button" @click="close()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Tabs --}}
            <div class="flex border-b border-gray-200 px-6">
                <button type="button" @click="tab = 'inss'"
                    :class="tab === 'inss' ? 'border-indigo-600 text-indigo-600' :
                        'border-transparent text-gray-500 hover:text-gray-700'"
                    class="px-4 py-3 text-sm font-medium border-b-2 transition">
                    INSS
                </button>
                <button type="button" @click="tab = 'irrf'"
                    :class="tab === 'irrf' ? 'border-indigo-600 text-indigo-600' :
                        'border-transparent text-gray-500 hover:text-gray-700'"
                    class="px-4 py-3 text-sm font-medium border-b-2 transition">
                    IRRF
                </button>
            </div>

            {{-- Body --}}
            <div class="overflow-y-auto p-6 flex-1">

                {{-- Tabela INSS --}}
                <div x-show="tab === 'inss'">
                    <p class="text-sm text-gray-600 mb-4">
                        Contribuição previdenciária do empregado — tabela progressiva.
                    </p>
                    <div class="overflow-x-auto rounded-lg border border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left font-medium text-gray-600">Ordem</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-600">Teto (R$)</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-600">Alíquota</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-600">Dedução (R$)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @forelse($faixasInss as $faixa)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2">{{ $faixa->ordem }}</td>
                                        <td class="px-4 py-2">R$ {{ number_format($faixa->teto, 2, ',', '.') }}</td>
                                        <td class="px-4 py-2">{{ number_format($faixa->aliquota * 100, 2, ',', '.') }}%
                                        </td>
                                        <td class="px-4 py-2">R$ {{ number_format($faixa->deducao, 2, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-6 text-center text-gray-500">
                                            Nenhuma faixa de INSS cadastrada.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Tabela IRRF --}}
                <div x-show="tab === 'irrf'">
                    <p class="text-sm text-gray-600 mb-4">
                        Imposto de Renda Retido na Fonte — tabela progressiva mensal.
                    </p>
                    <div class="overflow-x-auto rounded-lg border border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left font-medium text-gray-600">Ordem</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-600">Teto (R$)</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-600">Alíquota</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-600">Dedução (R$)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @forelse($faixasIrrf as $faixa)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2">{{ $faixa->ordem }}</td>
                                        <td class="px-4 py-2">R$ {{ number_format($faixa->teto, 2, ',', '.') }}</td>
                                        <td class="px-4 py-2">{{ number_format($faixa->aliquota * 100, 2, ',', '.') }}%
                                        </td>
                                        <td class="px-4 py-2">R$ {{ number_format($faixa->deducao, 2, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-6 text-center text-gray-500">
                                            Nenhuma faixa de IRRF cadastrada.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex justify-end px-6 py-3 border-t border-gray-200 bg-gray-50">
                <button type="button" @click="close()"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition">
                    Fechar
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        function faixasModal() {
            return {
                isOpen: false,
                tab: 'inss',
                open() {
                    this.isOpen = true;
                },
                close() {
                    this.isOpen = false;
                },
            }
        }
    </script>
@endpush
