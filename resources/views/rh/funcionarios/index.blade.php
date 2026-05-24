@extends('layouts.app')

@section('content')
    <div class="py-6">

        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            {{-- Header --}}
            <div class="flex items-center justify-between mb-6">

                <h1 class="text-2xl font-bold text-gray-900">Funcionários </h1>
                @can('funcionarios.create')
                    <a href="{{ route('rh.funcionarios.create') }}"
                        class="px-4 py-2 font-bold text-white bg-blue-500 rounded hover:bg-blue-700">
                        Novo Funcionário
                    </a>
                @endcan


            </div>



            <!-- Filtros -->
            <div class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="GET" class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Buscar</label>
                            <input type="text" name="search" value="{{ request('search') }}"
                                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm"
                                placeholder="Nome do funcionário...">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Departamento</label>
                            <select name="departamento_id" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm">
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
                                class="px-4 py-2 font-bold text-white bg-blue-500 rounded hover:bg-blue-700">
                                Filtrar
                            </button>
                            <a href="{{ route('rh.funcionarios.index') }}"
                                class="px-4 py-2 font-bold text-white bg-gray-500 rounded hover:bg-gray-700">
                                Limpar
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Lista de Funcionários -->
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 font-medium text-left text-gray-500 uppercase">Nome</th>
                                <th class="px-6 py-3 font-medium text-left text-gray-500 uppercase">CPF</th>
                                <th class="px-6 py-3 font-medium text-left text-gray-500 uppercase">Departamento</th>
                                <th class="px-6 py-3 font-medium text-left text-gray-500 uppercase">Cargo</th>
                                <th class="px-6 py-3 font-medium text-left text-gray-500 uppercase">Admissão</th>
                                <th class="px-6 py-3 font-medium text-left text-gray-500 uppercase">Salário</th>
                                <th class="px-6 py-3 font-medium text-left text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 font-medium text-left text-gray-500 uppercase">Ações</th>
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
                                    <td class="px-6 py-4 space-x-2 text-sm">
                                        {{-- <a href="{{ route('rh.funcionarios.show', $funcionario) }}"
                                            class="text-blue-600 hover:text-blue-900">Ver</a>
                                        @can('funcionarios.edit')
                                            <a href="{{ route('rh.funcionarios.edit', $funcionario) }}"
                                                class="text-green-600 hover:text-green-900">Editar</a>
                                        @endcan --}}
                                        {{-- ✅ Demitir - só aparece para ativos --}}
                                        {{-- @if ($funcionario->ativo)
                                            <a href="{{ route('rh.funcionarios.demitir.form', $funcionario) }}"
                                                class="text-red-400 hover:text-red-600" title="Demitir"
                                                onclick="return confirm('Deseja realmente demitir {{ $funcionario->nome_completo }}?')">
                                                🗑️
                                            </a>
                                        @endif --}}
                                        {{-- Dropdown de ações --}}
                                        <div class="relative" x-data="{ open: false }">
                                            <button @click="open = !open"
                                                class="px-3 py-1.5 border rounded-lg text-sm hover:bg-gray-50 flex items-center gap-1">
                                                Ações
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 9l-7 7-7-7" />
                                                </svg>
                                            </button>

                                            <div x-show="open" x-transition:enter="transition ease-out duration-100"
                                                x-transition:enter-start="transform opacity-0 scale-95"
                                                x-transition:enter-end="transform opacity-100 scale-100"
                                                x-transition:leave="transition ease-in duration-75"
                                                x-transition:leave-start="transform opacity-100 scale-100"
                                                x-transition:leave-end="transform opacity-0 scale-95"
                                                @click.away="open = false" x-cloak
                                                class="absolute right-0 z-50 w-48 py-1 mt-2 bg-white border rounded-lg shadow-lg">

                                                <a href="{{ route('rh.funcionarios.show', $funcionario) }}"
                                                    class="flex items-center gap-2 px-4 py-2 text-sm hover:bg-gray-50">
                                                    👁️ Visualizar
                                                </a>

                                                <a href="{{ route('rh.funcionarios.edit', $funcionario) }}"
                                                    class="flex items-center gap-2 px-4 py-2 text-sm hover:bg-gray-50">
                                                    ✏️ Editar
                                                </a>

                                                @if ($funcionario->ativo)
                                                    <div class="my-1 border-t"></div>
                                                    <a href="{{ route('rh.funcionarios.demitir.form', $funcionario) }}"
                                                        class="flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                                        🗑️ Demitir
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
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
