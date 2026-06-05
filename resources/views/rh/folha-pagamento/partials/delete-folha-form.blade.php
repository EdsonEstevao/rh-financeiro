<!-- Modal de confirmação de exclusão centralizada -->
<section class="items-center justify-center space-y-6">
    {{-- Modal único que recebe os dados via Alpine.js --}}
    <div x-data="{
        folhaId: null,
        folhaNome: '',
        folhaCompetencia: ''
    }"
        x-on:open-delete-modal.window="
        folhaId = $event.detail.id;
        folhaNome = $event.detail.nome;
        folhaCompetencia = $event.detail.competencia;
        $dispatch('open-modal', 'delete-folha-modal');
    ">
        <x-modal name="delete-folha-modal" focusable>
            <form method="POST" :action="`/rh/folha-pagamento/${folhaId}`" class="p-6">
                @csrf
                @method('DELETE')

                {{-- Ícone de alerta --}}
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>

                <h2 class="mt-4 text-lg font-medium text-center text-gray-900">
                    Excluir Folha de Pagamento?
                </h2>

                <div class="mt-3 text-center">
                    <p class="text-sm font-medium text-gray-700" x-text="folhaNome"></p>
                    <p class="text-sm text-gray-500">
                        Competência: <span x-text="folhaCompetencia"></span>
                    </p>
                </div>

                <p class="py-2 mt-4 text-sm text-center text-red-600 rounded-lg bg-red-50">
                    ⚠️ Esta ação não pode ser desfeita. Todos os lançamentos serão perdidos.
                </p>

                <div class="flex justify-center gap-3 mt-6">
                    <x-secondary-button type="button" x-on:click="$dispatch('close')">
                        Cancelar
                    </x-secondary-button>
                    <x-danger-button type="submit">
                        Sim, Excluir Folha
                    </x-danger-button>
                </div>
            </form>
        </x-modal>
    </div>

</section>
