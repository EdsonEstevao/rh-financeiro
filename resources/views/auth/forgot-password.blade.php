{{-- resources/views/auth/forgot-password.blade.php --}}
<x-guest-layout>
    {{-- Ícone --}}
    <div class="flex justify-center mb-6">
        <div class="w-16 h-16 bg-amber-50 rounded-full flex items-center justify-center ring-4 ring-amber-50">
            <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
            </svg>
        </div>
    </div>

    <div class="text-center mb-6">
        <h3 class="text-lg font-semibold text-gray-800">Redefinir Senha</h3>
        <p class="text-sm text-gray-500 mt-2">
            Informe seu email corporativo e enviaremos um link para redefinir sua senha.
        </p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email Corporativo</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                placeholder="nome@empresa.com.br"
                class="input-focus block w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-sm transition-all">
            @error('email')
                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit"
            class="w-full py-3 px-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 text-sm">
            Enviar Link de Redefinição
        </button>
    </form>

    <div class="mt-6 text-center">
        <a href="{{ route('login') }}"
            class="inline-flex items-center gap-1 text-sm text-indigo-600 hover:text-indigo-800 font-medium transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Voltar ao login
        </a>
    </div>
</x-guest-layout>
