@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6 sm:flex sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold leading-tight text-gray-900">Folha de Pagamento</h1>
                <p class="mt-1 text-sm text-gray-600">Lista de funcionários para processamento da folha</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('rh.folha-pagamento.calcular') }}"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 7h6m0 10v-3m-6 3v-3m-6 3h18M5 5h14a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2z" />
                    </svg>
                    Calcular Folha
                </a>
            </div>
        </div>

        <!-- Messages -->
        @if (session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-md">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-md">
                {{ session('error') }}
            </div>
        @endif

        <!-- Filters -->
        <div class="mb-6 bg-white shadow rounded-lg p-4">
            <form method="GET" action="{{ route('rh.folha-pagamento.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="departamento_id" class="block text-sm font-medium text-gray-700">Departamento</label>
                        <select name="departamento_id" id="departamento_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Todos</option>
                            @foreach ($departamentos as $departamento)
                                <option value="{{ $departamento->id }}"
                                    {{ request('departamento_id') == $departamento->id ? 'selected' : '' }}>
                                    {{ $departamento->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="cargo_id" class="block text-sm font-medium text-gray-700">Cargo</label>
                        <select name="cargo_id" id="cargo_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Todos</option>
                            @foreach ($cargos as $cargo)
                                <option value="{{ $cargo->id }}"
                                    {{ request('cargo_id') == $cargo->id ? 'selected' : '' }}>
                                    {{ $cargo->titulo }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="local_trabalho" class="block text-sm font-medium text-gray-700">Local de
                            Trabalho</label>
                        <input type="text" name="local_trabalho" id="local_trabalho"
                            value="{{ request('local_trabalho') }}" list="locaisTrabalho"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Digite o local">
                        <datalist id="locaisTrabalho">
                            @foreach ($locaisTrabalho as $local)
                                <option value="{{ $local }}">
                            @endforeach
                        </datalist>
                    </div>

                    <div class="flex items-end">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="apenas_ativos" value="1"
                                {{ request('apenas_ativos') ? 'checked' : '' }}
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-600">Apenas funcionários ativos</span>
                        </label>
                    </div>
                </div>

                <div class="flex justify-end space-x-2">
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        Filtrar
                    </button>

                    <a href="{{ route('rh.folha-pagamento.index') }}"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Limpar Filtros
                    </a>
                </div>
            </form>
        </div>

        <!-- Totalizadores -->
        <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white shadow rounded-lg p-4">
                <p class="text-sm text-gray-500">Total de Funcionários</p>
                <p class="text-2xl font-bold text-gray-900">{{ $totalizadores['total_funcionarios'] ?? 0 }}</p>
            </div>

            <div class="bg-white shadow rounded-lg p-4">
                <p class="text-sm text-gray-500">Salário Base Total</p>
                <p class="text-2xl font-bold text-gray-900">
                    R$ {{ number_format($totalizadores['total_salario_base'] ?? 0, 2, ',', '.') }}
                </p>
            </div>

            <div class="bg-white shadow rounded-lg p-4">
                <p class="text-sm text-gray-500">Média Salarial</p>
                <p class="text-2xl font-bold text-gray-900">
                    R$ {{ number_format($totalizadores['media_salarial'] ?? 0, 2, ',', '.') }}
                </p>
            </div>

            <div class="bg-white shadow rounded-lg p-4">
                <p class="text-sm text-gray-500">Funcionários Ativos</p>
                <p class="text-2xl font-bold text-green-600">{{ $totalizadores['total_ativos'] ?? 0 }}</p>
            </div>
        </div>

        <!-- Funcionários List -->
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Funcionário
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Departamento
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Cargo
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Local de Trabalho
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Salário Base
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ações
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($funcionarios as $funcionario)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div
                                                class="h-10 w-10 rounded-full bg-gray-400 flex items-center justify-center">
                                                <span class="text-white font-medium text-sm">
                                                    {{ substr($funcionario->nome_completo, 0, 1) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $funcionario->nome_completo }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $funcionario->cpf }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $funcionario->departamento->nome ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $funcionario->cargo->titulo ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $funcionario->local_trabalho ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium text-right">
                                    R$ {{ number_format($funcionario->salario_base, 2, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if ($funcionario->ativo)
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Ativo
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Inativo
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('rh.funcionarios.show', $funcionario) }}"
                                        class="text-indigo-600 hover:text-indigo-900 mr-3">
                                        Ver
                                    </a>
                                    <a href="{{ route('rh.funcionarios.edit', $funcionario) }}"
                                        class="text-blue-600 hover:text-blue-900">
                                        Editar
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum funcionário encontrado</h3>
                                    <p class="mt-1 text-sm text-gray-500">Nenhum funcionário atende aos filtros
                                        selecionados.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if ($funcionarios->hasPages())
            <div class="mt-6">
                {{ $funcionarios->links() }}
            </div>
        @endif
    </div>
@endsection
