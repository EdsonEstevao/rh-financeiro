{{-- resources/views/errors/419.blade.php --}}
@extends('errors.layout')

@section('title', '419 - Sessão Expirada')

@section('content')
    <div class="w-full max-w-lg text-center error-card">
        {{-- Ilustração --}}
        <div class="w-32 h-32 mx-auto mb-6">
            <svg viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="100" cy="100" r="80" fill="#dbeafe" stroke="#3b82f6" stroke-width="8" />
                <circle cx="100" cy="100" r="5" fill="#3b82f6" />
                <line x1="100" y1="100" x2="100" y2="45" stroke="#3b82f6" stroke-width="8"
                    stroke-linecap="round" />
                <line x1="100" y1="100" x2="135" y2="115" stroke="#3b82f6" stroke-width="8"
                    stroke-linecap="round" />
                <circle cx="100" cy="175" r="3" fill="#93c5fd" class="pulse-dot" />
                <circle cx="115" cy="180" r="2" fill="#93c5fd" class="pulse-dot" />
                <circle cx="85" cy="178" r="2.5" fill="#93c5fd" class="pulse-dot" />
            </svg>
        </div>

        {{-- Código do erro --}}
        <h1 class="mb-2 font-extrabold text-blue-500 text-8xl">419</h1>

        {{-- Título --}}
        <h2 class="mb-3 text-2xl font-bold text-gray-900">Sessão Expirada</h2>

        {{-- Mensagem --}}
        <p class="mb-8 leading-relaxed text-gray-500">
            Sua sessão expirou por inatividade ou segurança.<br>
            Por favor, faça login novamente para continuar.
        </p>

        {{-- Botão --}}
        <a href="{{ route('login') }}"
            class="inline-flex items-center gap-2 px-8 py-3 font-medium text-white transition-colors bg-blue-600 shadow-lg rounded-xl hover:bg-blue-700 shadow-blue-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
            </svg>
            Fazer Login
        </a>
    </div>
@endsection
