{{-- resources/views/errors/500.blade.php --}}
@extends('errors.layout')

@section('title', '500 - Erro Interno')

@section('content')
    <div class="w-full max-w-lg text-center error-card">
        {{-- Ilustração --}}
        <div class="w-32 h-32 mx-auto mb-6">
            <svg viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="100" cy="100" r="70" fill="#fce7f3" stroke="#ec4899" stroke-width="8" />
                <circle cx="100" cy="100" r="25" fill="#fce7f3" stroke="#ec4899" stroke-width="6" />
                @for ($i = 0; $i < 8; $i++)
                    <rect x="92" y="22" width="16" height="20" rx="4" fill="#fce7f3" stroke="#ec4899"
                        stroke-width="4" transform="rotate({{ $i * 45 }} 100 100)" />
                @endfor
                <text x="100" y="112" text-anchor="middle" font-size="35" font-weight="bold" fill="#ec4899">!</text>
            </svg>
        </div>

        {{-- Código do erro --}}
        <h1 class="mb-2 font-extrabold text-pink-500 text-8xl">500</h1>

        {{-- Título --}}
        <h2 class="mb-3 text-2xl font-bold text-gray-900">Erro Interno do Servidor</h2>

        {{-- Mensagem --}}
        <p class="mb-8 leading-relaxed text-gray-500">
            Ocorreu um erro inesperado. Nossa equipe foi notificada.<br>
            Por favor, tente novamente em alguns minutos.
        </p>

        {{-- Botões --}}
        <div class="flex flex-col justify-center gap-3 sm:flex-row">
            <button onclick="location.reload()"
                class="px-6 py-3 font-medium text-gray-700 transition-colors border-2 border-gray-300 rounded-xl hover:bg-gray-50">
                Tentar Novamente
            </button>
            <a href="{{ route('dashboard') }}"
                class="px-6 py-3 font-medium text-white transition-colors bg-pink-600 shadow-lg rounded-xl hover:bg-pink-700 shadow-pink-200">
                Ir para Dashboard
            </a>
        </div>
    </div>
@endsection
