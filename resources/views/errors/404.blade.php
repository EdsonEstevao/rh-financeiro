{{-- resources/views/errors/404.blade.php --}}
@extends('errors.layout')

@section('title', '404 - Página Não Encontrada')

@section('content')
    <div class="w-full max-w-lg text-center error-card">
        {{-- Ilustração --}}
        <div class="w-32 h-32 mx-auto mb-6">
            <svg viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="85" cy="85" r="55" fill="#fef3c7" stroke="#f59e0b" stroke-width="8" />
                <line x1="125" y1="125" x2="170" y2="170" stroke="#f59e0b" stroke-width="12"
                    stroke-linecap="round" />
                <text x="85" y="100" text-anchor="middle" font-size="50" font-weight="bold" fill="#f59e0b">?</text>
                <rect x="145" y="20" width="20" height="25" rx="3" fill="#fef3c7" stroke="#f59e0b"
                    stroke-width="3" transform="rotate(20 155 32)" />
                <rect x="160" y="50" width="15" height="20" rx="2" fill="#fef3c7" stroke="#f59e0b"
                    stroke-width="3" transform="rotate(-15 167 60)" />
            </svg>
        </div>

        {{-- Código do erro --}}
        <h1 class="mb-2 font-extrabold text-8xl text-amber-500">404</h1>

        {{-- Título --}}
        <h2 class="mb-3 text-2xl font-bold text-gray-900">Página Não Encontrada</h2>

        {{-- Mensagem --}}
        <p class="mb-8 leading-relaxed text-gray-500">
            A página que você está procurando não existe,<br>
            foi movida ou está temporariamente indisponível.
        </p>

        {{-- Botões --}}
        <div class="flex flex-col justify-center gap-3 sm:flex-row">
            <a href="{{ url()->previous() }}"
                class="px-6 py-3 font-medium text-gray-700 transition-colors border-2 border-gray-300 rounded-xl hover:bg-gray-50">
                ← Voltar
            </a>
            <a href="{{ route('dashboard') }}"
                class="h-12 font-medium text-white transition-colors bg-indigo-600 shadow-lg rounded-xl hover:bg-indigo-700 shadow-indigo-200">
                <img src="{{ asset('images/logo2.png') }}" alt="Construfor"
                    class="h-12 w-auto mx-auto rounded-xl shadow-md">
            </a>
        </div>

        {{-- Links úteis --}}
        <div class="p-4 mt-8 bg-white border border-gray-100 rounded-xl">
            <p class="mb-3 text-xs font-medium tracking-wider text-gray-400 uppercase">Links úteis</p>
            <div class="flex flex-wrap justify-center gap-2">
                <a href="{{ route('dashboard') }}"
                    class="px-3 py-1 text-sm text-indigo-600 rounded-lg hover:text-indigo-800 bg-indigo-50">Dashboard</a>
                @can('funcionarios.view')
                    <a href="{{ route('rh.funcionarios.index') }}"
                        class="px-3 py-1 text-sm text-indigo-600 rounded-lg hover:text-indigo-800 bg-indigo-50">Funcionários</a>
                @endcan
                @can('ferias.view')
                    <a href="{{ route('rh.ferias.dashboard') }}"
                        class="px-3 py-1 text-sm text-indigo-600 rounded-lg hover:text-indigo-800 bg-indigo-50">Férias</a>
                @endcan
            </div>
        </div>
    </div>
@endsection
