@php
    /** @var \App\Models\Domain\RH\Cargo|null $cargo */
    $isEdit = isset($cargo) && $cargo?->exists;
@endphp

<div class="space-y-6">
    <div>
        <label for="titulo" class="block text-sm font-medium text-gray-700">Título</label>
        <input id="titulo" name="titulo" type="text" value="{{ old('titulo', $cargo->titulo ?? '') }}" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            placeholder="Ex: Analista de RH" />
        @error('titulo')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex items-center gap-3">
        <input id="ativo" name="ativo" type="checkbox" value="1" @checked(old('ativo', $cargo->ativo ?? true))
            class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
        <label for="ativo" class="text-sm text-gray-700">Ativo</label>
    </div>

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('rh.cargos.index') }}"
            class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
            Cancelar
        </a>

        <button type="submit"
            class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
            {{ $isEdit ? 'Salvar alterações' : 'Criar cargo' }}
        </button>
    </div>
</div>
