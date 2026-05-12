@extends('layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center">
                <a href="{{ route('rh.folhas.index') }}" class="text-gray-400 hover:text-gray-600 mr-4">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold leading-tight text-gray-900">Nova Folha de Pagamento</h1>
                    <p class="mt-1 text-sm text-gray-600">Crie uma nova folha para uma competência específica</p>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <form action="{{ route('rh.folhas.store') }}" method="POST" x-data="{ competencia: '' }">
                @csrf

                <div class="px-4 py-5 sm:p-6">
                    <div class="grid grid-cols-1 gap-6">
                        <!-- Competência -->
                        <div>
                            <label for="competencia" class="block text-sm font-medium text-gray-700">
                                Competência
                            </label>
                            <div class="mt-1">
                                <input type="month" id="competencia" name="competencia" x-model="competencia"
                                    value="{{ old('competencia', now()->format('Y-m')) }}" required
                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                            @error('competencia')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Preview -->
                        <div x-show="competencia" class="bg-gray-50 rounded-md p-4">
                            <h4 class="text-sm font-medium text-gray-900 mb-2">Preview da Competência:</h4>
                            <p class="text-sm text-gray-600"
                                x-text="new Date(competencia + '-01').toLocaleDateString('pt-BR', { month: 'long', year: 'numeric' })">
                            </p>
                        </div>
                    </div>
                </div>

                <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                    <button type="button" onclick="window.location='{{ route('rh.folhas.index') }}'"
                        class="mr-3 bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Criar Folha
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
