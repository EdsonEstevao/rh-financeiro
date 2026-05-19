{{-- resources/views/admin/roles/edit.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Editar Perfil</h1>
                <p class="mt-1 text-sm text-gray-500">{{ ucfirst($role->name) }}</p>
            </div>
            <a href="{{ route('admin.roles.index') }}"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50">
                ← Voltar
            </a>
        </div>

        <form action="{{ route('admin.roles.update', $role) }}" method="POST"
            class="bg-white rounded-lg shadow p-6 space-y-6">
            @csrf
            @method('PUT')

            {{-- Nome --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Nome do Perfil <span
                        class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $role->name) }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                    required {{ $role->name === 'admin' ? 'readonly' : '' }}>
                @error('name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Permissões agrupadas --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">Permissões</label>

                @foreach ($groupedPermissions as $module => $perms)
                    <div class="mb-4 border rounded-lg overflow-hidden" x-data="{ open: {{ $perms->whereIn('id', $rolePermissions)->count() > 0 ? 'true' : 'false' }} }">
                        <button type="button" @click="open = !open"
                            class="w-full flex items-center justify-between px-4 py-2.5 bg-gray-50 hover:bg-gray-100 text-sm font-medium text-gray-700">
                            <span class="flex items-center gap-2">
                                <svg class="h-4 w-4 transition-transform" :class="open ? 'rotate-90' : ''" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                                {{ ucfirst($module) }}
                            </span>
                            <span class="text-xs text-gray-400">
                                {{ $perms->whereIn('id', $rolePermissions)->count() }}/{{ $perms->count() }}
                            </span>
                        </button>
                        <div x-show="open" class="p-3 grid grid-cols-2 gap-2">
                            @foreach ($perms as $permission)
                                <label
                                    class="flex items-center gap-2 text-sm cursor-pointer hover:bg-gray-50 p-1.5 rounded">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                        {{ in_array($permission->id, old('permissions', $rolePermissions)) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                    <span class="text-gray-700">{{ $permission->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Botões --}}
            <div class="flex justify-end gap-3 pt-4 border-t">
                <a href="{{ route('admin.roles.index') }}"
                    class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 text-sm">
                    Cancelar
                </a>
                <button type="submit"
                    class="px-6 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm font-semibold">
                    💾 Salvar Alterações
                </button>
            </div>
        </form>
    </div>
@endsection
