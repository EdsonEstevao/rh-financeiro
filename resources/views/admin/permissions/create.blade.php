{{-- resources/views/admin/permissions/create.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Nova Permissão</h1>

        <form action="{{ route('admin.permissions.store') }}" method="POST" class="bg-white rounded-lg shadow p-6">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Nome <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                    placeholder="Ex: modulo.acao" required>
                <p class="mt-1 text-xs text-gray-400">Formato: modulo.acao (ex: ferias.view, funcionarios.create)</p>
                @error('name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t">
                <a href="{{ route('admin.permissions.index') }}"
                    class="px-4 py-2 border rounded-md text-gray-700 hover:bg-gray-50 text-sm">Cancelar</a>
                <button type="submit"
                    class="px-6 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm font-semibold">💾
                    Criar</button>
            </div>
        </form>
    </div>
@endsection
