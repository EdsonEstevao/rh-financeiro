{{-- resources/views/errors/403.blade.php --}}
@extends('errors.layout')

@section('title', '403 - Não Autorizado')

@section('content')
    <div class="w-full max-w-lg text-center error-card">
        {{-- Ilustração --}}
        <div class="w-32 h-32 mx-auto mb-6">
            <svg viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="30" y="80" width="140" height="100" rx="15" fill="#fee2e2" stroke="#ef4444"
                    stroke-width="8" />
                <circle cx="100" cy="125" r="15" fill="#ef4444" />
                <rect x="95" y="130" width="10" height="25" rx="3" fill="#ef4444" />
                <path d="M70 40 L100 70 M130 40 L100 70" stroke="#ef4444" stroke-width="8" stroke-linecap="round" />
            </svg>
        </div>

        {{-- Código do erro --}}
        <h1 class="mb-2 font-extrabold text-red-500 text-8xl">403</h1>

        {{-- Título --}}
        <h2 class="mb-3 text-2xl font-bold text-gray-900">Acesso Não Autorizado</h2>

        {{-- Mensagem --}}
        <p class="mb-8 leading-relaxed text-gray-500">
            Você não tem permissão para acessar esta página.<br>
            Entre em contato com o administrador do sistema se acredita que isso é um erro.
        </p>

        {{-- Botões --}}
        <div class="flex flex-col justify-center gap-3 sm:flex-row">
            <a href="{{ url()->previous() }}"
                class="px-6 py-3 font-medium text-gray-700 transition-colors border-2 border-gray-300 rounded-xl hover:bg-gray-50">
                ← Voltar
            </a>
            <a href="{{ route('dashboard') }}"
                class="px-6 py-3 font-medium text-white transition-colors bg-indigo-600 shadow-lg rounded-xl hover:bg-indigo-700 shadow-indigo-200">
                Ir para Dashboard
            </a>
        </div>

        {{-- Informação adicional --}}
        @auth
            <div class="p-4 mt-8 text-left bg-white border border-gray-100 rounded-xl">
                <p class="mb-2 text-xs font-medium tracking-wider text-gray-400 uppercase">Detalhes da requisição</p>
                <div class="space-y-1 text-sm text-gray-500">
                    <p><strong>Usuário:</strong> {{ auth()->user()->name }}</p>
                    <p><strong>Email:</strong> {{ auth()->user()->email }}</p>
                    <p><strong>URL:</strong> {{ request()->url() }}</p>
                    <p><strong>Data/Hora:</strong> {{ now()->format('d/m/Y H:i:s') }}</p>
                </div>
            </div>
        @endauth
    </div>
@endsection
