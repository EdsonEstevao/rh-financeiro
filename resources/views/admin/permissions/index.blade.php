{{-- resources/views/admin/permissions/index.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">🔒 Permissões</h1>
                <p class="mt-1 text-sm text-gray-500">Gerenciamento de permissões do sistema</p>
            </div>
            <a href="{{ route('admin.permissions.create') }}"
                class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm font-semibold">
                + Nova Permissão
            </a>
        </div>

        @foreach ($permissions as $module => $perms)
            <div class="bg-white rounded-lg shadow mb-6 overflow-hidden">
                <div class="px-6 py-3 bg-gray-50 border-b">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase">{{ $module }}</h3>
                </div>
                <div class="divide-y">
                    @foreach ($perms as $permission)
                        <div class="px-6 py-3 flex items-center justify-between hover:bg-gray-50">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $permission->name }}</p>
                                <p class="text-xs text-gray-400">{{ $permission->roles_count }} perfil(is) associado(s)</p>
                            </div>
                            <div class="flex gap-2">
                                <a href="{{ route('admin.permissions.edit', $permission) }}"
                                    class="text-indigo-600 hover:text-indigo-900 text-sm">✏️</a>
                                <form action="{{ route('admin.permissions.destroy', $permission) }}" method="POST"
                                    onsubmit="return confirm('Excluir permissão {{ $permission->name }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 text-sm">🗑️</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
@endsection
