{{-- resources/views/admin/users/edit.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Editar Usuário</h1>
                <p class="mt-1 text-sm text-gray-500">{{ $user->name }} • {{ $user->email }}</p>
            </div>
            <a href="{{ route('admin.users.index') }}"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50">
                ← Voltar
            </a>
        </div>

        <form action="{{ route('admin.users.update', $user) }}" method="POST"
            class="bg-white rounded-lg shadow p-6 space-y-6">
            @csrf
            @method('PUT')

            {{-- Nome --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Nome <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                    required>
                @error('name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                    required>
                @error('email')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Senha (opcional na edição) --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nova Senha</label>
                    <input type="password" name="password"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                        placeholder="Deixe em branco para manter">
                    @error('password')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Confirmar Nova Senha</label>
                    <input type="password" name="password_confirmation"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                        placeholder="Deixe em branco para manter">
                </div>
            </div>

            {{-- Perfis --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Perfis (Roles)</label>
                <div class="grid grid-cols-2 gap-3">
                    @foreach ($roles as $role)
                        <label class="flex items-center gap-2 p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                                {{ in_array($role->id, old('roles', $userRoles)) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                            <span class="text-sm font-medium">{{ ucfirst($role->name) }}</span>
                        </label>
                    @endforeach
                </div>
                @error('roles')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Botões --}}
            <div class="flex justify-end gap-3 pt-4 border-t">
                <a href="{{ route('admin.users.index') }}"
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
