@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Cargos</h1>
                <p class="mt-1 text-sm text-gray-500">Gerencie os cargos cadastrados no sistema.</p>
            </div>

            @can('cargos.manage')
                <a href="{{ route('rh.cargos.create') }}"
                    class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                    Novo cargo
                </a>
            @endcan
        </div>

        {{-- Flash messages --}}
        <div class="mt-6 space-y-3">
            @if (session('success'))
                <div class="rounded-md border border-green-200 bg-green-50 p-4 text-sm text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="rounded-md border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                    {{ session('error') }}
                </div>
            @endif
        </div>

        <div class="mt-6 overflow-hidden rounded-lg bg-white shadow ring-1 ring-black/5">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Título
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Status
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Ações
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse ($cargos as $cargo)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                    {{ $cargo->titulo }}
                                </td>

                                <td class="px-6 py-4 text-sm">
                                    @if ($cargo->ativo)
                                        <span
                                            class="inline-flex items-center rounded-full bg-green-50 px-2.5 py-0.5 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-100">
                                            Ativo
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center rounded-full bg-gray-50 px-2.5 py-0.5 text-xs font-medium text-gray-700 ring-1 ring-inset ring-gray-100">
                                            Inativo
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-right text-sm">
                                    <div class="inline-flex items-center gap-2">
                                        @can('cargos.manage')
                                            <a href="{{ route('rh.cargos.edit', $cargo) }}"
                                                class="rounded-md px-3 py-1.5 text-sm font-medium text-indigo-700 hover:bg-indigo-50">
                                                Editar
                                            </a>

                                            <form action="{{ route('rh.cargos.destroy', $cargo) }}" method="POST"
                                                onsubmit="return confirm('Confirma excluir este cargo?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="rounded-md px-3 py-1.5 text-sm font-medium text-red-700 hover:bg-red-50">
                                                    Excluir
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-10 text-center text-sm text-gray-500">
                                    Nenhum cargo encontrado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($cargos->hasPages())
                <div class="border-t border-gray-200 px-6 py-3">
                    {{ $cargos->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
