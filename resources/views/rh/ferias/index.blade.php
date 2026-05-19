{{-- resources/views/rh/ferias/index.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">

        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">📅 Períodos de Férias</h1>
                <p class="mt-1 text-sm text-gray-500">Gerencie todos os períodos de férias</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('rh.ferias.dashboard') }}"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 text-sm">
                    📊 Dashboard
                </a>
            </div>
        </div>

        {{-- Filtros --}}
        <div class="bg-white rounded-lg shadow mb-6 p-4">
            <form action="{{ route('rh.ferias.index') }}" method="GET" class="flex gap-4 items-end">
                <div>
                    <label class="block text-xs font-medium text-gray-700">Status</label>
                    <select name="status"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">Todos</option>
                        <option value="planejada" @selected($status === 'planejada')>Planejada</option>
                        <option value="aprovada" @selected($status === 'aprovada')>Aprovada</option>
                        <option value="gozada" @selected($status === 'gozada')>Gozada</option>
                        <option value="cancelada" @selected($status === 'cancelada')>Cancelada</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700">Funcionário</label>
                    <input type="text" name="funcionario" value="{{ request('funcionario') }}" placeholder="Nome..."
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>
                <div>
                    <button type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm">
                        🔍 Filtrar
                    </button>
                </div>
            </form>
        </div>

        {{-- Tabela --}}
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left">Funcionário</th>
                            <th class="px-4 py-3 text-left">Cargo</th>
                            <th class="px-4 py-3 text-left">Período</th>
                            <th class="px-4 py-3 text-left">Início</th>
                            <th class="px-4 py-3 text-left">Fim</th>
                            <th class="px-4 py-3 text-center">Dias</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($periodos as $periodo)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium">{{ $periodo->funcionario->nome_completo }}</td>
                                <td class="px-4 py-3 text-gray-500">{{ $periodo->funcionario->cargo?->titulo }}</td>
                                <td class="px-4 py-3">{{ $periodo->numero_periodo }}º período</td>
                                <td class="px-4 py-3">{{ $periodo->data_inicio->format('d/m/Y') }}</td>
                                <td class="px-4 py-3">{{ $periodo->data_fim->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 text-center">
                                    {{ $periodo->data_inicio->diffInDays($periodo->data_fim) + 1 }} dias
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span
                                        class="px-2 py-1 text-xs rounded-full
                                @if ($periodo->status === 'gozada') bg-green-100 text-green-800
                                @elseif($periodo->status === 'aprovada') bg-blue-100 text-blue-800
                                @elseif($periodo->status === 'planejada') bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800 @endif">
                                        {{ ucfirst($periodo->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <a href="{{ route('rh.ferias.edit', $periodo) }}"
                                        class="text-indigo-600 hover:text-indigo-900 font-medium">
                                        ✏️ Editar
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                    Nenhum período de férias encontrado
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t">
                {{ $periodos->links() }}
            </div>
        </div>
    </div>
@endsection
