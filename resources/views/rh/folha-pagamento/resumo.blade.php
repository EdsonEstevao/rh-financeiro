@extends('layouts.app')
@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">Resumo da Folha de Pagamento</h1>
                <p class="mt-1 text-sm text-gray-500">Resumo geral da folha de pagamento.</p>
            </div>
        </div>


        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                {{-- Cards de Resumo --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-blue-50">
                            <div class="text-3xl font-bold text-blue-600">{{ $resumo['total_funcionarios'] }}</div>
                            <div class="text-blue-600 text-sm font-medium">Total de Funcionários</div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-green-50">
                            <div class="text-2xl font-bold text-green-600">R$
                                {{ number_format($resumo['folha_bruta'], 2, ',', '.') }}</div>
                            <div class="text-green-600 text-sm font-medium">Folha Bruta</div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-red-50">
                            <div class="text-2xl font-bold text-red-600">R$
                                {{ number_format($resumo['total_descontos'], 2, ',', '.') }}</div>
                            <div class="text-red-600 text-sm font-medium">Total Descontos</div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-purple-50">
                            <div class="text-2xl font-bold text-purple-600">R$
                                {{ number_format($resumo['folha_liquida'], 2, ',', '.') }}</div>
                            <div class="text-purple-600 text-sm font-medium">Folha Líquida</div>
                        </div>
                    </div>
                </div>

                {{-- Detalhamento por Departamento --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-semibold mb-4">Detalhamento por Departamento</h3>

                        <div class="overflow-x-auto">
                            <table class="min-w-full table-auto">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase">Departamento
                                        </th>
                                        <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase">Funcionários
                                        </th>
                                        <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase">Folha Bruta</th>
                                        <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase">Total Descontos
                                        </th>
                                        <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase">Folha Líquida
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @php
                                        $funcionariosPorDepartamento = $funcionarios->groupBy('departamento.nome');
                                    @endphp
                                    @foreach ($funcionariosPorDepartamento as $departamento => $funcs)
                                        <tr>
                                            <td class="px-6 py-4 font-medium">{{ $departamento }}</td>
                                            <td class="px-6 py-4">{{ $funcs->count() }}</td>
                                            <td class="px-6 py-4">R$
                                                {{ number_format($funcs->sum('salario_bruto'), 2, ',', '.') }}</td>
                                            <td class="px-6 py-4 text-red-600">R$
                                                {{ number_format($funcs->sum('total_descontos'), 2, ',', '.') }}</td>
                                            <td class="px-6 py-4 font-semibold text-green-600">R$
                                                {{ number_format($funcs->sum('salario_liquido'), 2, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
