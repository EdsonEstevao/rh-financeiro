<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard RH
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Cards de Resumo -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-blue-50">
                        <div class="text-3xl font-bold text-blue-600">
                            {{ \App\Models\Domain\RH\Funcionario::ativos()->count() }}
                        </div>
                        <div class="text-blue-600 text-sm font-medium">Funcionários Ativos</div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-green-50">
                        <div class="text-3xl font-bold text-green-600">
                            {{ \App\Models\Domain\RH\Departamento::ativos()->count() }}
                        </div>
                        <div class="text-green-600 text-sm font-medium">Departamentos</div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-yellow-50">
                        <div class="text-3xl font-bold text-yellow-600">
                            {{ \App\Models\Domain\RH\Funcionario::feriasVencendo(30)->count() }}
                        </div>
                        <div class="text-yellow-600 text-sm font-medium">Férias Vencendo</div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-red-50">
                        <div class="text-3xl font-bold text-red-600">
                            {{ \App\Models\Domain\RH\Funcionario::feriasVencidas()->count() }}
                        </div>
                        <div class="text-red-600 text-sm font-medium">Férias Vencidas</div>
                    </div>
                </div>
            </div>

            <!-- Links Rápidos -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Ações Rápidas</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @can('funcionarios.gerenciar')
                            <a href="{{ route('rh.funcionarios.create') }}"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-center">
                                Novo Funcionário
                            </a>
                        @endcan

                        @can('funcionarios.visualizar')
                            <a href="{{ route('rh.funcionarios.index') }}"
                                class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-center">
                                Lista Funcionários
                            </a>
                        @endcan

                        @can('folha.visualizar')
                            <a href="{{ route('rh.folha-pagamento.index') }}"
                                class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded text-center">
                                Folha Pagamento
                            </a>
                        @endcan

                        @can('departamentos.gerenciar')
                            <a href="{{ route('rh.departamentos.index') }}"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-center">
                                Departamentos
                            </a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
