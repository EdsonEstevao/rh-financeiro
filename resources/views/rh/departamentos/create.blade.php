{{-- resources/views/rh/departamentos/create.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

        {{-- Breadcrumb --}}
        <div class="mb-6">
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-1">
                <a href="{{ route('rh.departamentos.index') }}"
                    class="hover:text-indigo-600 transition-colors">Departamentos</a>
                <span>/</span>
                <span class="text-gray-700 font-medium">Novo</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">Novo Departamento</h1>
        </div>

        {{-- Card do formulário --}}
        <div class="rounded-lg bg-white shadow ring-1 ring-black/5 overflow-hidden">
            <div class="border-b border-gray-100 bg-gray-50 px-6 py-3">
                <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">
                    Informações do Departamento
                </h2>
            </div>

            <form action="{{ route('rh.departamentos.store') }}" method="POST" novalidate class="p-6">
                @csrf

                @include('rh.departamentos._form')

                {{-- Ações --}}
                <div class="mt-6 flex items-center justify-end gap-3 border-t border-gray-100 pt-5">
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
                        Salvar Departamento
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
