@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                📊 Tabela INSS — Faixas de Contribuição
            </h2>
            @can('faixa-inss.create')
                <a href="{{ route('rh.faixa-inss.create') }}"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 transition">
                    + Nova Faixa
                </a>
            @endcan
        </div>
    </div>

    <div class="py-6">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Flash Messages --}}
            @if (session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                    class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Faixa</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Limite Inferior
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Limite Superior
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Alíquota</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Parcela Deduzir
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vigência</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($faixas as $faixa)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                                    <td class="px-4 py-3 text-sm">{{ $faixa->numero_faixa }}ª</td>
                                    <td class="px-4 py-3 text-sm">R$
                                        {{ number_format($faixa->limite_inferior, 2, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        {{ $faixa->limite_superior ? 'R$ ' . number_format($faixa->limite_superior, 2, ',', '.') : '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm">{{ number_format($faixa->aliquota, 1, ',', '.') }}%</td>
                                    <td class="px-4 py-3 text-sm">R$
                                        {{ number_format($faixa->parcela_deduzir, 2, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-sm whitespace-nowrap">
                                        {{ $faixa->vigencia_inicio ? $faixa->vigencia_inicio->format('d/m/Y') : null }}
                                        @if ($faixa->vigencia_fim)
                                            → {{ $faixa->vigencia_fim->format('d/m/Y') }}
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span
                                            class="inline-flex px-2 py-1 text-xs rounded-full font-medium
                                        {{ $faixa->ativa ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $faixa->ativa ? 'Ativa' : 'Inativa' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex justify-center gap-2">
                                            @can('faixa-inss.edit')
                                                <a href="{{ route('rh.faixa-inss.edit', $faixa) }}"
                                                    class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400">
                                                    ✏️
                                                </a>
                                            @endcan
                                            @can('faixa-inss.delete')
                                                <button x-data
                                                    @click="if(confirm('Tem certeza que deseja excluir esta faixa?')) {
                                                document.getElementById('delete-form-{{ $faixa->id }}').submit();
                                            }"
                                                    class="text-red-600 hover:text-red-900 dark:text-red-400">
                                                    🗑️
                                                </button>
                                                <form id="delete-form-{{ $faixa->id }}"
                                                    action="{{ route('rh.faixa-inss.destroy', $faixa) }}" method="POST"
                                                    class="hidden">
                                                    @csrf @method('DELETE')
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                        Nenhuma faixa cadastrada.
                                        <a href="{{ route('rh.faixa-inss.create') }}"
                                            class="text-blue-600 underline">Cadastrar agora</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $faixas->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
