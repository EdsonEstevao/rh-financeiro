@extends('layouts.app')

@section('content')
    <div class="py-6">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-end mb-4 items-center">

                @can('funcionarios.create')
                    <a href="{{ route('rh.funcionarios.create') }}"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Novo Funcionário
                    </a>
                @endcan
            </div>
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif



            <!-- Filtros -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Buscar</label>
                            <input type="text" name="search" value="{{ request('search') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                placeholder="Nome do funcionário...">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Departamento</label>
                            <select name="departamento_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="">Todos</option>
                                @foreach ($departamentos as $dept)
                                    <option value="{{ $dept->id }}"
                                        {{ request('departamento_id') == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-end space-x-2">
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Filtrar
                            </button>
                            <a href="{{ route('rh.funcionarios.index') }}"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Limpar
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Lista de Funcionários -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase">Nome</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase">CPF</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase">Departamento</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase">Cargo</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase">Admissão</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase">Salário</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($funcionarios as $funcionario)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900">{{ $funcionario->nome_completo }}</div>
                                        <div class="text-sm text-gray-500">{{ $funcionario->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-sm">{{ $funcionario->cpf }}</td>
                                    <td class="px-6 py-4 text-sm">{{ $funcionario->departamento->nome ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm">{{ $funcionario->cargo->titulo ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm">{{ $funcionario->data_admissao?->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        {{ $funcionario->formatarMoeda($funcionario->salario_base) }}</td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            {{ $funcionario->ativo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $funcionario->ativo ? 'Ativo' : 'Inativo' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm space-x-2">
                                        <a href="{{ route('rh.funcionarios.show', $funcionario) }}"
                                            class="text-blue-600 hover:text-blue-900">Ver</a>
                                        @can('funcionarios.edit')
                                            <a href="{{ route('rh.funcionarios.edit', $funcionario) }}"
                                                class="text-green-600 hover:text-green-900">Editar</a>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                        Nenhum funcionário encontrado.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($funcionarios->hasPages())
                    <div class="px-6 py-3 border-t">
                        {{ $funcionarios->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
