{{-- resources/views/rh/departamentos/_form.blade.php --}}
<div class="space-y-5">

    {{-- Nome --}}
    <div>
        <label for="nome" class="block text-sm font-medium text-gray-700">
            Nome do Departamento
            <span class="text-red-500">*</span>
        </label>
        <input type="text" id="nome" name="nome" value="{{ old('nome', $departamento->nome ?? '') }}"
            maxlength="255" required autofocus placeholder="Ex: Recursos Humanos"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm
                   focus:border-indigo-500 focus:ring-indigo-500
                   @error('nome') border-red-300 bg-red-50 @enderror" />
        @error('nome')
            <p class="mt-1 text-xs text-red-600 flex items-center gap-1">
                <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z"
                        clip-rule="evenodd" />
                </svg>
                {{ $message }}
            </p>
        @enderror
    </div>

    {{-- Status --}}
    <div>
        <span class="block text-sm font-medium text-gray-700 mb-2">Status</span>
        <label class="inline-flex items-center gap-3 cursor-pointer select-none">
            <input type="hidden" name="ativo" value="0" />
            <input type="checkbox" name="ativo" value="1"
                {{ old('ativo', $departamento->ativo ?? true) ? 'checked' : '' }}
                class="h-4 w-4 rounded border-gray-300 text-indigo-600
                       focus:ring-indigo-500 cursor-pointer" />
            <span class="text-sm text-gray-700">Departamento ativo</span>
        </label>
        <p class="mt-1 text-xs text-gray-400">
            Departamentos inativos não aparecem nas seleções de cadastro de funcionários.
        </p>
    </div>
</div>
