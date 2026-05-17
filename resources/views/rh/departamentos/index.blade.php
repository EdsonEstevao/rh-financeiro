{{-- resources/views/rh/departamentos/index.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

        {{-- Cabeçalho --}}
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Departamentos</h1>
                <p class="mt-1 text-sm text-gray-500">
                    {{ $departamentos->total() }} departamento(s) cadastrado(s)
                </p>
            </div>

            @can('departamentos.manage')
                <a href="{{ route('rh.departamentos.create') }}"
                    class="inline-flex items-center gap-1.5 rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition-colors">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path
                            d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                    </svg>
                    Novo Departamento
                </a>
            @endcan
        </div>

        {{-- Alertas --}}
        @if (session('success'))
            <div
                class="mb-4 flex items-center gap-3 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700 ring-1 ring-green-600/20">
                <svg class="h-5 w-5 flex-shrink-0 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"
                        clip-rule="evenodd" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div
                class="mb-4 flex items-center gap-3 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700 ring-1 ring-red-600/20">
                <svg class="h-5 w-5 flex-shrink-0 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z"
                        clip-rule="evenodd" />
                </svg>
                {{ session('error') }}
            </div>
        @endif

        {{-- Tabela --}}
        <div class="rounded-lg bg-white shadow ring-1 ring-black/5 overflow-hidden">
            @if ($departamentos->isEmpty())
                <div class="px-6 py-16 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" />
                    </svg>
                    <p class="mt-4 text-sm text-gray-500">Nenhum departamento cadastrado.</p>
                    @can('departamentos.manage')
                        <a href="{{ route('rh.departamentos.create') }}"
                            class="mt-4 inline-flex items-center gap-1 text-sm font-medium text-indigo-600 hover:text-indigo-500">
                            Cadastrar primeiro departamento →
                        </a>
                    @endcan
                </div>
            @else
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">
                                Nome
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wide">
                                Funcionários
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wide">
                                Status
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wide">
                                Criado em
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wide">
                                Ações
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @foreach ($departamentos as $departamento)
                            <tr class="hover:bg-gray-50 transition-colors">

                                {{-- Nome --}}
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                    {{ $departamento->nome }}
                                </td>

                                {{-- Qtd Funcionários --}}
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-0.5 text-xs font-medium text-indigo-700">
                                        {{ $departamento->funcionarios_count ?? $departamento->funcionarios()->count() }}
                                    </span>
                                </td>

                                {{-- Status --}}
                                <td class="px-6 py-4 text-center">
                                    @if ($departamento->ativo)
                                        <span
                                            class="inline-flex items-center gap-1 rounded-full bg-green-50 px-2.5 py-0.5 text-xs font-semibold text-green-700 ring-1 ring-green-600/20">
                                            <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                                            Ativo
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-semibold text-gray-500 ring-1 ring-gray-400/20">
                                            <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span>
                                            Inativo
                                        </span>
                                    @endif
                                </td>

                                {{-- Data --}}
                                <td class="px-6 py-4 text-center text-sm text-gray-500">
                                    {{ $departamento->created_at->format('d/m/Y') }}
                                </td>

                                {{-- Ações --}}
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">

                                        @can('departamentos.manage')
                                            <a href="{{ route('rh.departamentos.edit', $departamento) }}"
                                                class="inline-flex items-center gap-1 rounded-md bg-white px-2.5 py-1.5 text-xs font-medium text-gray-700 ring-1 ring-gray-300 hover:bg-gray-50 transition-colors"
                                                title="Editar">
                                                <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path
                                                        d="M2.695 14.763l-1.262 3.154a.5.5 0 00.65.65l3.155-1.262a4 4 0 001.343-.885L17.5 5.5a2.121 2.121 0 00-3-3L3.58 13.42a4 4 0 00-.885 1.343z" />
                                                </svg>
                                                Editar
                                            </a>
                                        @endcan

                                        @can('departamentos.manage')
                                            <form action="{{ route('rh.departamentos.destroy', $departamento) }}" method="POST"
                                                x-data
                                                @submit.prevent="
                                                    if (confirm('Excluir o departamento \'{{ addslashes($departamento->nome) }}\'?\nEsta ação não pode ser desfeita.'))
                                                        $el.submit()
                                                ">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="inline-flex items-center gap-1 rounded-md bg-white px-2.5 py-1.5 text-xs font-medium text-red-600 ring-1 ring-red-300 hover:bg-red-50 transition-colors"
                                                    title="Excluir">
                                                    <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd"
                                                            d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                    Excluir
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Paginação --}}
                @if ($departamentos->hasPages())
                    <div class="border-t border-gray-200 px-6 py-4">
                        {{ $departamentos->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
@endsection
