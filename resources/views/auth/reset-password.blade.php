{{-- resources/views/auth/reset-password.blade.php --}}
<x-guest-layout>
    <div class="flex justify-center mb-6">
        <div class="w-16 h-16 bg-green-50 rounded-full flex items-center justify-center ring-4 ring-green-50">
            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
        </div>
    </div>

    <div class="text-center mb-6">
        <h3 class="text-lg font-semibold text-gray-800">Nova Senha</h3>
        <p class="text-sm text-gray-500 mt-2">Crie uma senha forte e segura</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required
                autofocus readonly
                class="input-focus block w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-500 text-sm">
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Nova Senha</label>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                placeholder="Mínimo 8 caracteres"
                class="input-focus block w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-sm transition-all">
            @error('password')
                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">Confirmar
                Senha</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required
                autocomplete="new-password" placeholder="Repita a nova senha"
                class="input-focus block w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-sm transition-all">
        </div>

        <button type="submit"
            class="w-full py-3 px-4 bg-gradient-to-r from-green-600 to-emerald-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 text-sm">
            Redefinir Senha
        </button>
    </form>
</x-guest-layout>
