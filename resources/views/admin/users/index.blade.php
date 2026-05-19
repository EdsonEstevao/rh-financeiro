{{-- resources/views/admin/users/index.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

        {{-- Header --}}
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">👥 Usuários</h1>
                <p class="mt-1 text-sm text-gray-500">Gerenciamento de usuários do sistema</p>
            </div>
            <a href="{{ route('admin.users.create') }}"
                class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm font-semibold">
                + Novo Usuário
            </a>
        </div>

        {{-- Filtro de busca --}}
        <div class="bg-white rounded-lg shadow p-4 mb-6">
            <form method="GET" class="flex gap-3">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Buscar por nome ou email..."
                    class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm">
                <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 text-sm">
                    🔍 Buscar
                </button>
                @if (request('search'))
                    <a href="{{ route('admin.users.index') }}" class="px-4 py-2 text-red-600 hover:text-red-800 text-sm">
                        Limpar
                    </a>
                @endif
            </form>
        </div>

        {{-- Tabela --}}
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Nome</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Email</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Perfis</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Criado em</th>
                        <th class="px-6 py-3 text-center font-medium text-gray-500">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium">{{ $user->name }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $user->email }}</td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @foreach ($user->roles as $role)
                                        <span
                                            class="px-2 py-0.5 text-xs rounded-full font-semibold
                                        @if ($role->name === 'admin') bg-red-100 text-red-700
                                        @elseif($role->name === 'rh') bg-indigo-100 text-indigo-700
                                        @elseif($role->name === 'financeiro') bg-emerald-100 text-emerald-700
                                        @else bg-gray-100 text-gray-700 @endif">
                                            {{ $role->name }}
                                        </span>
                                    @endforeach
                                    @if ($user->roles->isEmpty())
                                        <span class="text-gray-400 text-xs">Sem perfil</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-500">{{ $user->created_at->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center gap-2">
                                    <a href="{{ route('admin.users.edit', $user) }}"
                                        class="text-indigo-600 hover:text-indigo-900">✏️</a>
                                    @if ($user->id !== auth()->id())
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                            onsubmit="return confirm('Excluir usuário {{ $user->name }}?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">🗑️</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                Nenhum usuário encontrado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginação --}}
        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>
@endsection
