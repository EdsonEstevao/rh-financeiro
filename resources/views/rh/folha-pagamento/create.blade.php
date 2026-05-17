@extends('layouts.app')

@section('content')
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Nova Folha de Pagamento</h1>
                <p class="mt-1 text-sm text-gray-600">Preencha os dados para criar uma nova folha</p>
            </div>
            <a href="{{ route('rh.folha-pagamento.index') }}"
                class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 shadow-sm">
                <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Voltar
            </a>
        </div>

        <form action="{{ route('rh.folha-pagamento.store') }}" method="POST" autocomplete="off" novalidate>
            @csrf

            @include('rh.folha-pagamento._form')

            {{-- Botões --}}
            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('rh.folha-pagamento.index') }}"
                    class="px-5 py-2.5 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Cancelar
                </a>
                <button type="submit"
                    class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 shadow-sm">
                    <svg class="inline -ml-1 mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Salvar Folha
                </button>
            </div>
        </form>
    </div>
@endsection
