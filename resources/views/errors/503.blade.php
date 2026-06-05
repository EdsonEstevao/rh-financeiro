{{-- resources/views/errors/503.blade.php --}}
@extends('errors.layout')

@section('title', '503 - Em Manutenção')

@section('content')
    <div class="w-full max-w-lg text-center error-card">
        {{-- Ilustração --}}
        <div class="w-32 h-32 mx-auto mb-6">
            <svg viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M40 160 L80 120 L95 135 L55 175 Z" fill="#e0e7ff" stroke="#6366f1" stroke-width="6"
                    stroke-linejoin="round" />
                <circle cx="45" cy="155" r="20" fill="#e0e7ff" stroke="#6366f1" stroke-width="6" />
                <circle cx="45" cy="155" r="8" fill="#6366f1" />
                <rect x="120" y="50" width="50" height="25" rx="5" fill="#e0e7ff" stroke="#6366f1"
                    stroke-width="6" transform="rotate(-30 145 62)" />
                <rect x="130" y="75" width="12" height="70" rx="4" fill="#c7d2fe" stroke="#6366f1"
                    stroke-width="5" transform="rotate(-30 136 110)" />
            </svg>
        </div>

        {{-- Código do erro --}}
        <h1 class="mb-2 font-extrabold text-indigo-500 text-8xl">503</h1>

        {{-- Título --}}
        <h2 class="mb-3 text-2xl font-bold text-gray-900">Em Manutenção</h2>

        {{-- Mensagem --}}
        <p class="mb-8 leading-relaxed text-gray-500">
            Estamos realizando melhorias no sistema.<br>
            Voltaremos em breve!
        </p>

        {{-- Estimativa --}}
        <div
            class="inline-flex items-center gap-2 px-4 py-2 mb-8 text-sm font-medium text-indigo-600 rounded-full bg-indigo-50">
            <span class="w-2 h-2 bg-indigo-500 rounded-full pulse-dot"></span>
            Previsão de retorno: em instantes
        </div>
    </div>
@endsection
