{{-- resources/views/rh/funcionarios/demitir.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="max-w-3xl px-4 py-6 mx-auto sm:px-6 lg:px-8">

        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">🗑️ Demitir Funcionário</h1>
                <p class="mt-1 text-sm text-gray-500">{{ $funcionario->nome_completo }}</p>
            </div>
            <a href="{{ route('rh.funcionarios.show', $funcionario) }}" class="text-sm text-gray-500 hover:text-gray-700">
                ← Voltar
            </a>
        </div>

        {{-- Informações --}}
        <div class="p-6 mb-6 bg-white border border-gray-100 shadow-sm rounded-xl">
            <h3 class="mb-4 font-semibold text-gray-800">Informações do Funcionário</h3>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-gray-500">Nome:</span>
                    <p class="font-medium">{{ $funcionario->nome_completo }}</p>
                </div>
                <div>
                    <span class="text-gray-500">Cargo:</span>
                    <p class="font-medium">{{ $funcionario->cargo?->titulo ?? 'N/A' }}</p>
                </div>
                <div>
                    <span class="text-gray-500">Admissão:</span>
                    <p class="font-medium">
                        {{ \Carbon\Carbon::parse($funcionario->contrato->data_admissao)->format('d/m/Y') }}</p>
                </div>
                <div>
                    <span class="text-gray-500">Tempo de Casa:</span>
                    <p class="font-medium">
                        {{ \Carbon\Carbon::parse($funcionario->contrato->data_admissao)->diffForHumans() }}</p>
                </div>
            </div>
        </div>

        {{-- Resumo de Férias --}}
        <div class="p-6 mb-6 border bg-amber-50 rounded-xl border-amber-200">
            <h3 class="mb-3 font-semibold text-amber-800">⚠️ Férias Rescisórias</h3>
            <div class="space-y-2 text-sm">
                @if ($funcionario->ferias_vencidas)
                    <p>🔴 <strong>Férias Vencidas:</strong> 30 dias (pagamento em dobro)</p>
                @endif
                <p>🟡 <strong>Férias Proporcionais:</strong> Serão calculadas na data da demissão</p>
                <p class="mt-2 text-xs text-amber-600">
                    Férias proporcionais: 1/12 avos por mês trabalhado (considera-se mês completo se > 14 dias)
                </p>
            </div>
        </div>

        {{-- Formulário --}}
        <form action="{{ route('rh.funcionarios.demitir', $funcionario) }}" method="POST"
            onsubmit="return confirm('Tem certeza que deseja demitir este funcionário? Esta ação não pode ser desfeita.')">
            @csrf

            <div class="p-6 space-y-4 bg-white border border-gray-100 shadow-sm rounded-xl">
                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-700">Data da Demissão <span
                            class="text-red-500">*</span></label>
                    <input type="date" name="data_demissao" value="{{ old('data_demissao', now()->format('Y-m-d')) }}"
                        min="{{ $funcionario->contrato->data_admissao }}" max="{{ now()->format('Y-m-d') }}"
                        class="block w-full border-gray-300 rounded-lg focus:border-red-500 focus:ring-red-500" required>
                    @error('data_demissao')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-700">Motivo <span
                            class="text-red-500">*</span></label>
                    <select name="motivo"
                        class="block w-full border-gray-300 rounded-lg focus:border-red-500 focus:ring-red-500" required>
                        <option value="">Selecione...</option>
                        <option value="Demissão sem justa causa">Sem justa causa</option>
                        <option value="Demissão por justa causa">Com justa causa</option>
                        <option value="Pedido de demissão">Pedido do funcionário</option>
                        <option value="Término de contrato">Término de contrato</option>
                        <option value="Aposentadoria">Aposentadoria</option>
                    </select>
                    @error('motivo')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t">
                    <a href="{{ route('rh.funcionarios.show', $funcionario) }}"
                        class="px-4 py-2 text-sm text-gray-700 border rounded-lg hover:bg-gray-50">
                        Cancelar
                    </a>
                    <button type="submit"
                        class="px-6 py-2 text-sm font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700">
                        Confirmar Demissão
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
