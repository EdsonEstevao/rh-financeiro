{{-- resources/views/rh/departamentos/edit.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

        {{-- Breadcrumb --}}
        <div class="mb-6">
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-1">
                <a href="{{ route('rh.departamentos.index') }}"
                    class="hover:text-indigo-600 transition-colors">Departamentos</a>
                <span>/</span>
                <span class="text-gray-700 font-medium">Editar</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">
                Editar: {{ $departamento->nome }}
            </h1>
        </div>

        {{-- Card do formulário --}}
        <div class="rounded-lg bg-white shadow ring-1 ring-black/5 overflow-hidden">
            <div class="border-b border-gray-100 bg-gray-50 px-6 py-3">
                <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">
                    Informações do Departamento
                </h2>
            </div>

            <form action="{{ route('rh.departamentos.update', $departamento) }}" method="POST" novalidate class="p-6">
                @csrf
                @method('PUT')

                @include('rh.departamentos._form')

                {{-- Ações --}}
                <div class="mt-6 flex items-center justify-between gap-3 border-t border-gray-100 pt-5">

                    {{-- Info de metadados --}}
                    <p class="text-xs text-gray-400">
                        Criado em {{ $departamento->created_at->format('d/m/Y') }}
                        • Atualizado {{ $departamento->updated_at->diffForHumans() }}
                    </p>

                    <div class="flex items-center gap-3">
                        <a href="{{ route('rh.departamentos.index') }}"
                            class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                            Cancelar
                        </a>
                        <button type="submit"
                            class="inline-flex items-center gap-1.5 rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition-colors">
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"
                                    clip-rule="evenodd" />
                            </svg>
                            Atualizar Departamento
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Card de perigo --}}
        @can('rh.departamentos.delete')
            <div class="mt-6 rounded-lg bg-white shadow ring-1 ring-red-200 overflow-hidden">
                <div class="border-b border-red-100 bg-red-50 px-6 py-3">
                    <h2 class="text-sm font-semibold text-red-700 uppercase tracking-wide">
                        Zona de Perigo
                    </h2>
                </div>
                <div class="p-6 flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm font-medium text-gray-900">Excluir este departamento</p>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Só é possível excluir departamentos sem funcionários vinculados.
                            Esta ação é irreversível.
                        </p>
                    </div>
                    <form action="{{ route('rh.departamentos.destroy', $departamento) }}" method="POST" x-data
                        @submit.prevent="
                            if (confirm('Excluir o departamento \'{{ addslashes($departamento->nome) }}\'?\nEsta ação não pode ser desfeita.'))
                                $el.submit()
                        ">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="inline-flex items-center gap-1.5 rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 transition-colors">
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z"
                                    clip-rule="evenodd" />
                            </svg>
                            Excluir Departamento
                        </button>
                    </form>
                </div>
            </div>
        @endcan
    </div>
@endsection
