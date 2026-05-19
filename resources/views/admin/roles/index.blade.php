{{-- resources/views/admin/roles/index.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">🔑 Perfis (Roles)</h1>
                <p class="mt-1 text-sm text-gray-500">Gerenciamento de perfis de acesso</p>
            </div>
            <a href="{{ route('admin.roles.create') }}"
                class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm font-semibold">
                + Novo Perfil
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($roles as $role)
                <div class="bg-white rounded-lg shadow p-5 hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ ucfirst($role->name) }}</h3>
                            <p class="text-sm text-gray-500">{{ $role->users_count }} usuário(s)</p>
                        </div>
                        <span
                            class="px-2 py-0.5 text-xs rounded-full font-semibold
                        @if ($role->name === 'admin') bg-red-100 text-red-700
                        @elseif($role->name === 'rh') bg-indigo-100 text-indigo-700
                        @else bg-gray-100 text-gray-700 @endif">
                            {{ $role->permissions_count }} permissões
                        </span>
                    </div>

                    {{-- Permissões resumidas --}}
                    <div class="flex flex-wrap gap-1 mb-4">
                        @foreach ($role->permissions->take(5) as $permission)
                            <span class="px-2 py-0.5 text-xs bg-gray-100 text-gray-600 rounded">
                                {{ $permission->name }}
                            </span>
                        @endforeach
                        @if ($role->permissions->count() > 5)
                            <span class="text-xs text-gray-400">+{{ $role->permissions->count() - 5 }}</span>
                        @endif
                    </div>

                    {{-- Ações --}}
                    <div class="flex gap-2">
                        <a href="{{ route('admin.roles.edit', $role) }}"
                            class="flex-1 text-center px-3 py-1.5 text-xs bg-indigo-50 text-indigo-700 rounded hover:bg-indigo-100">
                            ✏️ Editar
                        </a>
                        @if ($role->name !== 'admin')
                            <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="flex-1"
                                onsubmit="return confirm('Excluir perfil {{ $role->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="w-full px-3 py-1.5 text-xs bg-red-50 text-red-700 rounded hover:bg-red-100">
                                    🗑️ Excluir
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-3 bg-white rounded-lg shadow p-12 text-center text-gray-400">
                    Nenhum perfil criado ainda.
                </div>
            @endforelse
        </div>
    </div>
@endsection
