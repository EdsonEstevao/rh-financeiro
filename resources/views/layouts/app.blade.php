{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'RH e Financeiro') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Estilos adicionais por página --}}
    @stack('styles')
</head>

<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen">

        {{-- ============================================================
         NAVBAR
    ============================================================ --}}
        <nav class="bg-white border-b border-gray-200 sticky top-0 z-40">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 justify-between">

                    {{-- ── LEFT ─────────────────────────────────────── --}}
                    <div class="flex items-stretch">

                        {{-- Logo --}}
                        <div class="flex shrink-0 items-center pr-6 border-r border-gray-100">
                            <a href="{{ route('dashboard') }}"
                                class="flex items-center gap-2 text-lg font-bold text-indigo-600 hover:text-indigo-700 transition-colors">
                                {{-- Ícone SVG --}}
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" />
                                </svg>
                                <span class="hidden sm:block">RH & Financeiro</span>
                            </a>
                        </div>

                        {{-- ── LINKS DESKTOP ─────────────────────────── --}}
                        <div class="hidden sm:flex sm:items-stretch sm:ml-6 sm:space-x-1">

                            {{-- Dashboard --}}
                            <a href="{{ route('dashboard') }}"
                                class="inline-flex items-center px-3 border-b-2 text-sm font-medium transition-colors
                                {{ request()->routeIs('dashboard')
                                    ? 'border-indigo-600 text-gray-900'
                                    : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}">
                                Dashboard
                            </a>

                            {{-- ── DROPDOWN RH ─────────────────────── --}}
                            @canany(['rh.dashboard.view', 'funcionarios.view', 'folha.view', 'departamentos.view',
                                'cargos.view', 'cargos.manage', 'folha.reports'])
                                <div class="relative flex items-stretch" x-data="{ open: false }">

                                    <button type="button" @click="open = !open" @keydown.escape="open = false"
                                        class="inline-flex items-center gap-1.5 px-3 border-b-2 text-sm font-medium transition-colors
                                        {{ request()->routeIs('rh.*')
                                            ? 'border-indigo-600 text-gray-900'
                                            : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}">
                                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path
                                                d="M10 9a3 3 0 100-6 3 3 0 000 6zM6 8a2 2 0 11-4 0 2 2 0 014 0zM1.49 15.326a.78.78 0 01-.358-.442 3 3 0 014.308-3.516 6.484 6.484 0 00-1.905 3.959c-.023.222-.014.442.025.654a4.97 4.97 0 01-2.07-.655zM16.44 15.98a4.97 4.97 0 002.07-.654.78.78 0 00.357-.442 3 3 0 00-4.308-3.517 6.484 6.484 0 011.907 3.96 2.32 2.32 0 01-.026.654zM18 8a2 2 0 11-4 0 2 2 0 014 0zM5.304 16.19a.844.844 0 01-.277-.71 5 5 0 019.947 0 .843.843 0 01-.277.71A6.975 6.975 0 0110 18a6.974 6.974 0 01-4.696-1.81z" />
                                        </svg>
                                        RH
                                        <svg class="h-3.5 w-3.5 transition-transform duration-200"
                                            :class="open ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>

                                    {{-- Painel dropdown RH --}}
                                    <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100"
                                        x-transition:enter-start="opacity-0 scale-95"
                                        x-transition:enter-end="opacity-100 scale-100"
                                        x-transition:leave="transition ease-in duration-75"
                                        x-transition:leave-start="opacity-100 scale-100"
                                        x-transition:leave-end="opacity-0 scale-95" @click.away="open = false"
                                        class="absolute left-0 top-full z-50 mt-1 w-60 rounded-lg bg-white shadow-lg ring-1 ring-black/5 focus:outline-none">

                                        {{-- Cabeçalho do dropdown --}}
                                        <div class="px-3 py-2 border-b border-gray-100">
                                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">
                                                Recursos Humanos
                                            </p>
                                        </div>

                                        <div class="py-1">
                                            {{-- Dashboard RH --}}
                                            @can('rh.dashboard.view')
                                                <a href="{{ route('rh.dashboard') }}"
                                                    class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 transition-colors
                                                    {{ request()->routeIs('rh.dashboard') ? 'bg-indigo-50 text-indigo-700 font-medium' : '' }}">
                                                    <svg class="h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                        <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z" />
                                                        <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z" />
                                                    </svg>
                                                    Dashboard RH
                                                </a>
                                            @endcan

                                            {{-- Funcionários --}}
                                            @can('funcionarios.view')
                                                <a href="{{ route('rh.funcionarios.index') }}"
                                                    class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 transition-colors
                                                    {{ request()->routeIs('rh.funcionarios.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : '' }}">
                                                    <svg class="h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                        <path
                                                            d="M10 9a3 3 0 100-6 3 3 0 000 6zM6 8a2 2 0 11-4 0 2 2 0 014 0zM1.49 15.326a.78.78 0 01-.358-.442 3 3 0 014.308-3.516 6.484 6.484 0 00-1.905 3.959c-.023.222-.014.442.025.654a4.97 4.97 0 01-2.07-.655zM16.44 15.98a4.97 4.97 0 002.07-.654.78.78 0 00.357-.442 3 3 0 00-4.308-3.517 6.484 6.484 0 011.907 3.96 2.32 2.32 0 01-.026.654zM18 8a2 2 0 11-4 0 2 2 0 014 0zM5.304 16.19a.844.844 0 01-.277-.71 5 5 0 019.947 0 .843.843 0 01-.277.71A6.975 6.975 0 0110 18a6.974 6.974 0 01-4.696-1.81z" />
                                                    </svg>
                                                    Funcionários
                                                </a>
                                            @endcan

                                            <!-- Ferias funcionario -->
                                            @can('ferias.view')
                                                <a href="{{ route('rh.ferias.dashboard') }}"
                                                    class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm transition-colors
                                                {{ request()->routeIs('rh.ferias.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-700 hover:bg-gray-50' }}">
                                                    <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd"
                                                            d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                    Férias
                                                </a>
                                            @endcan


                                            {{-- Folha de Pagamento --}}
                                            @can('folha.view')
                                                <a href="{{ route('rh.folha-pagamento.index') }}"
                                                    class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 transition-colors
                                                    {{ request()->routeIs('rh.folha-pagamento.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : '' }}">
                                                    <svg class="h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd"
                                                            d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                    Folha de Pagamento
                                                </a>
                                            @endcan

                                            {{-- Relatórios RH --}}
                                            @can('folha.reports')
                                                <a href="{{ route('rh.folha-pagamento.resumo') }}"
                                                    class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 transition-colors
                                                    {{ request()->routeIs('rh.folha-pagamento.resumo') ? 'bg-indigo-50 text-indigo-700 font-medium' : '' }}">
                                                    <svg class="h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                        <path
                                                            d="M15.5 2A1.5 1.5 0 0014 3.5v13a1.5 1.5 0 001.5 1.5h1a1.5 1.5 0 001.5-1.5v-13A1.5 1.5 0 0016.5 2h-1zM9.5 6A1.5 1.5 0 008 7.5v9A1.5 1.5 0 009.5 18h1a1.5 1.5 0 001.5-1.5v-9A1.5 1.5 0 0010.5 6h-1zM3.5 10A1.5 1.5 0 002 11.5v5A1.5 1.5 0 003.5 18h1A1.5 1.5 0 006 16.5v-5A1.5 1.5 0 004.5 10h-1z" />
                                                    </svg>
                                                    Relatórios RH
                                                </a>
                                            @endcan
                                            @can('folha.reports')
                                                <a href="{{ route('rh.folha-pagamento.resumo-geral') }}"
                                                    class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm transition-colors
                                                {{ request()->routeIs('rh.folha-pagamento.resumo-geral') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-700 hover:bg-gray-50' }}">
                                                    <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                        <path
                                                            d="M15.5 2A1.5 1.5 0 0014 3.5v13a1.5 1.5 0 001.5 1.5h1a1.5 1.5 0 001.5-1.5v-13A1.5 1.5 0 0016.5 2h-1zM9.5 6A1.5 1.5 0 008 7.5v9A1.5 1.5 0 009.5 18h1a1.5 1.5 0 001.5-1.5v-9A1.5 1.5 0 0010.5 6h-1zM3.5 10A1.5 1.5 0 002 11.5v5A1.5 1.5 0 003.5 18h1A1.5 1.5 0 006 16.5v-5A1.5 1.5 0 004.5 10h-1z" />
                                                    </svg>
                                                    can Relatorio Geral RH
                                                </a>
                                            @endcan


                                            {{-- Divisor --}}
                                            @canany(['departamentos.view', 'cargos.view', 'cargos.manage'])
                                                <div class="my-1 border-t border-gray-100"></div>
                                                <p
                                                    class="px-4 py-1 text-xs font-semibold text-gray-400 uppercase tracking-wide">
                                                    Configurações
                                                </p>
                                            @endcanany

                                            {{-- Departamentos --}}
                                            @can('departamentos.view')
                                                <a href="{{ route('rh.departamentos.index') }}"
                                                    class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 transition-colors
                                                    {{ request()->routeIs('rh.departamentos.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : '' }}">
                                                    <svg class="h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd"
                                                            d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V8a2 2 0 00-2-2h-5L9 4H4zm7 5a1 1 0 10-2 0v1H8a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V9z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                    Departamentos
                                                </a>
                                            @endcan

                                            {{-- Cargos --}}
                                            @canany(['cargos.view', 'cargos.manage'])
                                                <a href="{{ route('rh.cargos.index') }}"
                                                    class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 transition-colors
                                                    {{ request()->routeIs('rh.cargos.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : '' }}">
                                                    <svg class="h-4 w-4 text-gray-400" viewBox="0 0 20 20"
                                                        fill="currentColor">
                                                        <path fill-rule="evenodd"
                                                            d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z"
                                                            clip-rule="evenodd" />
                                                        <path
                                                            d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z" />
                                                    </svg>
                                                    Cargos
                                                </a>
                                            @endcanany
                                        </div>
                                    </div>
                                </div>
                            @endcanany

                            {{-- ── DROPDOWN FINANCEIRO ──────────────── --}}
                            @canany(['financeiro.dashboard.view', 'boletos.view', 'cartoes.view', 'financeiro.reports'])
                                <div class="relative flex items-stretch" x-data="{ open: false }">

                                    <button type="button" @click="open = !open" @keydown.escape="open = false"
                                        class="inline-flex items-center gap-1.5 px-3 border-b-2 text-sm font-medium transition-colors
                                        {{ request()->routeIs('financeiro.*')
                                            ? 'border-indigo-600 text-gray-900'
                                            : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}">
                                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path
                                                d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Financeiro
                                        <svg class="h-3.5 w-3.5 transition-transform duration-200"
                                            :class="open ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>

                                    {{-- Painel dropdown Financeiro --}}
                                    <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100"
                                        x-transition:enter-start="opacity-0 scale-95"
                                        x-transition:enter-end="opacity-100 scale-100"
                                        x-transition:leave="transition ease-in duration-75"
                                        x-transition:leave-start="opacity-100 scale-100"
                                        x-transition:leave-end="opacity-0 scale-95" @click.away="open = false"
                                        class="absolute left-0 top-full z-50 mt-1 w-60 rounded-lg bg-white shadow-lg ring-1 ring-black/5">

                                        <div class="px-3 py-2 border-b border-gray-100">
                                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">
                                                Financeiro
                                            </p>
                                        </div>

                                        <div class="py-1">
                                            {{-- Dashboard Financeiro --}}
                                            @can('financeiro.dashboard.view')
                                                <a href="{{ route('financeiro.dashboard') }}"
                                                    class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 transition-colors
                                                    {{ request()->routeIs('financeiro.dashboard') ? 'bg-emerald-50 text-emerald-700 font-medium' : '' }}">
                                                    <svg class="h-4 w-4 text-gray-400" viewBox="0 0 20 20"
                                                        fill="currentColor">
                                                        <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z" />
                                                        <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z" />
                                                    </svg>
                                                    Dashboard Financeiro
                                                </a>
                                            @endcan

                                            {{-- Boletos --}}
                                            @can('boletos.view')
                                                {{-- <a href="{{ route('financeiro.boletos.index') }}" --}}
                                                <a href="#"
                                                    class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 transition-colors
                                                    {{ request()->routeIs('financeiro.boletos.*') ? 'bg-emerald-50 text-emerald-700 font-medium' : '' }}">
                                                    <svg class="h-4 w-4 text-gray-400" viewBox="0 0 20 20"
                                                        fill="currentColor">
                                                        <path fill-rule="evenodd"
                                                            d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                    Boletos
                                                </a>
                                            @endcan

                                            {{-- Cartões --}}
                                            @can('cartoes.view')
                                                {{-- <a href="{{ route('financeiro.cartoes.index') }}" --}}
                                                <a href="#"
                                                    class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 transition-colors
                                                    {{ request()->routeIs('financeiro.cartoes.*') ? 'bg-emerald-50 text-emerald-700 font-medium' : '' }}">
                                                    <svg class="h-4 w-4 text-gray-400" viewBox="0 0 20 20"
                                                        fill="currentColor">
                                                        <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z" />
                                                        <path fill-rule="evenodd"
                                                            d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                    Cartões de Crédito
                                                </a>
                                            @endcan

                                            {{-- Relatórios Financeiros --}}
                                            @can('financeiro.reports')
                                                <div class="my-1 border-t border-gray-100"></div>
                                                {{-- <a href="{{ route('financeiro.relatorios.index') }}" --}}
                                                <a href="#"
                                                    class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 transition-colors
                                                    {{ request()->routeIs('financeiro.relatorios.*') ? 'bg-emerald-50 text-emerald-700 font-medium' : '' }}">
                                                    <svg class="h-4 w-4 text-gray-400" viewBox="0 0 20 20"
                                                        fill="currentColor">
                                                        <path
                                                            d="M15.5 2A1.5 1.5 0 0014 3.5v13a1.5 1.5 0 001.5 1.5h1a1.5 1.5 0 001.5-1.5v-13A1.5 1.5 0 0016.5 2h-1zM9.5 6A1.5 1.5 0 008 7.5v9A1.5 1.5 0 009.5 18h1a1.5 1.5 0 001.5-1.5v-9A1.5 1.5 0 0010.5 6h-1zM3.5 10A1.5 1.5 0 002 11.5v5A1.5 1.5 0 003.5 18h1A1.5 1.5 0 006 16.5v-5A1.5 1.5 0 004.5 10h-1z" />
                                                    </svg>
                                                    Relatórios
                                                </a>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            @endcanany

                            {{-- ── ADMIN ────────────────────────────── --}}
                            {{-- @role('admin')
                                <a href="#"
                                    class="inline-flex items-center gap-1.5 px-3 border-b-2 text-sm font-medium transition-colors
                                    {{ request()->routeIs('admin.*')
                                        ? 'border-indigo-600 text-gray-900'
                                        : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}">
                                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    Admin
                                </a>
                            @endrole --}}
                            @role('admin')
                                <div class="relative flex items-stretch" x-data="{ openAdmin: false }">
                                    <button type="button" @click="openAdmin = !openAdmin"
                                        @keydown.escape="openAdmin = false"
                                        class="inline-flex items-center gap-1.5 px-3 border-b-2 text-sm font-medium transition-colors
            {{ request()->routeIs('admin.*')
                ? 'border-red-600 text-gray-900'
                : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}">
                                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Admin
                                        <svg class="h-3.5 w-3.5 transition-transform duration-200"
                                            :class="openAdmin ? 'rotate-180' : ''" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>

                                    <div x-show="openAdmin" x-cloak x-transition @click.away="openAdmin = false"
                                        class="absolute left-0 top-full z-50 mt-1 w-56 rounded-lg bg-white shadow-lg ring-1 ring-black/5">
                                        <div class="px-3 py-2 border-b border-gray-100">
                                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">
                                                Administração</p>
                                        </div>
                                        <div class="py-1">
                                            <a href="{{ route('admin.users.index') }}"
                                                class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-red-700 transition-colors
                    {{ request()->routeIs('admin.users.*') ? 'bg-red-50 text-red-700 font-medium' : '' }}">
                                                👥 Usuários
                                            </a>
                                            <a href="{{ route('admin.roles.index') }}"
                                                class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-red-700 transition-colors
                    {{ request()->routeIs('admin.roles.*') ? 'bg-red-50 text-red-700 font-medium' : '' }}">
                                                🔑 Perfis
                                            </a>
                                            <a href="{{ route('admin.permissions.index') }}"
                                                class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-red-700 transition-colors
                    {{ request()->routeIs('admin.permissions.*') ? 'bg-red-50 text-red-700 font-medium' : '' }}">
                                                🔒 Permissões
                                            </a>
                                            {{-- Logs Activity --}}
                                            <a href="{{ route('admin.activity-logs.index') }}"
                                                class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm transition-colors
        {{ request()->routeIs('admin.activity-logs.*') ? 'bg-red-50 text-red-700 font-medium' : 'text-gray-700 hover:bg-gray-50' }}">
                                                <span class="text-lg">📜</span> Logs
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endrole

                        </div>
                    </div>

                    {{-- ── RIGHT ────────────────────────────────────── --}}
                    <div class="flex items-center gap-3">

                        {{-- Badge do perfil --}}
                        <span
                            class="hidden sm:inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset
                        @role('admin')        bg-red-50    text-red-700    ring-red-600/20
                        @else @role('rh')     bg-indigo-50 text-indigo-700 ring-indigo-600/20
                        @else @role('financeiro') bg-emerald-50 text-emerald-700 ring-emerald-600/20
                        @else @role('gerente')    bg-purple-50  text-purple-700  ring-purple-600/20
                        @else @role('consultor')  bg-yellow-50  text-yellow-700  ring-yellow-600/20
                        @else                     bg-gray-50    text-gray-600    ring-gray-500/20
                        @endrole @endrole @endrole @endrole @endrole">
                            {{ ucfirst(auth()->user()->getRoleNames()->first() ?? 'usuário') }}
                        </span>

                        {{-- User dropdown DESKTOP --}}
                        <div class="relative hidden sm:block" x-data="{ open: false }">
                            <button type="button" @click="open = !open" @keydown.escape="open = false"
                                class="flex items-center gap-2 rounded-lg px-2 py-1.5 text-sm text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors">
                                {{-- Avatar --}}
                                <div
                                    class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-600 text-white text-sm font-bold flex-shrink-0">
                                    {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                                </div>
                                <span class="hidden lg:block max-w-[120px] truncate font-medium">
                                    {{ auth()->user()->name }}
                                </span>
                                <svg class="h-4 w-4 text-gray-400 transition-transform duration-200"
                                    :class="open ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>

                            <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95" @click.away="open = false"
                                class="absolute right-0 z-50 mt-2 w-60 rounded-lg bg-white shadow-lg ring-1 ring-black/5">

                                {{-- Info do usuário --}}
                                <div class="flex items-center gap-3 px-4 py-3 border-b border-gray-100">
                                    <div
                                        class="flex h-9 w-9 items-center justify-center rounded-full bg-indigo-600 text-white text-sm font-bold flex-shrink-0">
                                        {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 truncate">
                                            {{ auth()->user()->name }}
                                        </p>
                                        <p class="text-xs text-gray-500 truncate">
                                            {{ auth()->user()->email }}
                                        </p>
                                    </div>
                                </div>

                                <div class="py-1">
                                    <a href="{{ route('profile.edit') }}"
                                        class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                        <svg class="h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Meu Perfil
                                    </a>

                                    <div class="my-1 border-t border-gray-100"></div>

                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                            class="flex w-full items-center gap-2.5 px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            Sair do sistema
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- ── MOBILE MENU BUTTON ───────────────────── --}}
                        <div class="sm:hidden" x-data="{ openMobile: false }">
                            <button type="button" @click="openMobile = !openMobile"
                                class="inline-flex items-center justify-center rounded-md p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors">
                                <span class="sr-only">Menu</span>
                                <svg x-show="!openMobile" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                                <svg x-show="openMobile" x-cloak class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>

                            {{-- ── PAINEL MOBILE ────────────────────── --}}
                            <div x-show="openMobile" x-cloak x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 -translate-y-2"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 translate-y-0"
                                x-transition:leave-end="opacity-0 -translate-y-2"
                                class="fixed inset-x-0 top-16 z-50 bg-white border-b border-gray-200 shadow-lg max-h-[calc(100vh-4rem)] overflow-y-auto">

                                <div class="max-w-7xl mx-auto px-4 py-3 space-y-0.5">

                                    {{-- Dashboard --}}
                                    <a href="{{ route('dashboard') }}"
                                        class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors
                                        {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">
                                        <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path
                                                d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                                        </svg>
                                        Dashboard
                                    </a>

                                    {{-- ── SEÇÃO RH MOBILE ─────────── --}}
                                    @canany(['rh.dashboard.view', 'funcionarios.view', 'folha.view',
                                        'departamentos.view', 'cargos.view', 'cargos.manage', 'ferias.view'])
                                        <div class="pt-3 pb-1">
                                            <p class="px-3 text-xs font-bold uppercase tracking-wider text-gray-400">
                                                Recursos Humanos
                                            </p>
                                        </div>

                                        @can('rh.dashboard.view')
                                            <a href="{{ route('rh.dashboard') }}"
                                                class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm transition-colors
                                                {{ request()->routeIs('rh.dashboard') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-700 hover:bg-gray-50' }}">
                                                <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z" />
                                                    <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z" />
                                                </svg>
                                                Dashboard RH
                                            </a>
                                        @endcan

                                        @can('funcionarios.view')
                                            <a href="{{ route('rh.funcionarios.index') }}"
                                                class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm transition-colors
                                                {{ request()->routeIs('rh.funcionarios.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-700 hover:bg-gray-50' }}">
                                                <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                    <path
                                                        d="M10 9a3 3 0 100-6 3 3 0 000 6zM6 8a2 2 0 11-4 0 2 2 0 014 0zM1.49 15.326a.78.78 0 01-.358-.442 3 3 0 014.308-3.516 6.484 6.484 0 00-1.905 3.959c-.023.222-.014.442.025.654a4.97 4.97 0 01-2.07-.655zM16.44 15.98a4.97 4.97 0 002.07-.654.78.78 0 00.357-.442 3 3 0 00-4.308-3.517 6.484 6.484 0 011.907 3.96 2.32 2.32 0 01-.026.654zM18 8a2 2 0 11-4 0 2 2 0 014 0zM5.304 16.19a.844.844 0 01-.277-.71 5 5 0 019.947 0 .843.843 0 01-.277.71A6.975 6.975 0 0110 18a6.974 6.974 0 01-4.696-1.81z" />
                                                </svg>
                                                Funcionários
                                            </a>
                                        @endcan

                                        <!-- Ferias funcionario -->
                                        @can('ferias.view')
                                            <a href="{{ route('rh.ferias.dashboard') }}"
                                                class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm transition-colors
                                                {{ request()->routeIs('rh.ferias.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-700 hover:bg-gray-50' }}">
                                                <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                Férias
                                            </a>
                                        @endcan

                                        @can('folha.view')
                                            <a href="{{ route('rh.folha-pagamento.index') }}"
                                                class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm transition-colors
                                                {{ request()->routeIs('rh.folha-pagamento.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-700 hover:bg-gray-50' }}">
                                                <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                Folha de Pagamento
                                            </a>
                                        @endcan

                                        @can('folha.reports')
                                            <a href="{{ route('rh.folha-pagamento.resumo') }}"
                                                class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm transition-colors
                                                {{ request()->routeIs('rh.folha-pagamento.resumo') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-700 hover:bg-gray-50' }}">
                                                <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                    <path
                                                        d="M15.5 2A1.5 1.5 0 0014 3.5v13a1.5 1.5 0 001.5 1.5h1a1.5 1.5 0 001.5-1.5v-13A1.5 1.5 0 0016.5 2h-1zM9.5 6A1.5 1.5 0 008 7.5v9A1.5 1.5 0 009.5 18h1a1.5 1.5 0 001.5-1.5v-9A1.5 1.5 0 0010.5 6h-1zM3.5 10A1.5 1.5 0 002 11.5v5A1.5 1.5 0 003.5 18h1A1.5 1.5 0 006 16.5v-5A1.5 1.5 0 004.5 10h-1z" />
                                                </svg>
                                                Relatórios RH
                                            </a>
                                        @endcan

                                        @can('departamentos.view')
                                            <a href="{{ route('rh.departamentos.index') }}"
                                                class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm transition-colors
                                                {{ request()->routeIs('rh.departamentos.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-700 hover:bg-gray-50' }}">
                                                <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V8a2 2 0 00-2-2h-5L9 4H4zm7 5a1 1 0 10-2 0v1H8a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V9z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                Departamentos
                                            </a>
                                        @endcan

                                        @canany(['cargos.view', 'cargos.manage'])
                                            <a href="{{ route('rh.cargos.index') }}"
                                                class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm transition-colors
                                                {{ request()->routeIs('rh.cargos.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-700 hover:bg-gray-50' }}">
                                                <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z"
                                                        clip-rule="evenodd" />
                                                    <path
                                                        d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z" />
                                                </svg>
                                                Cargos
                                            </a>
                                        @endcanany
                                    @endcanany

                                    {{-- ── SEÇÃO FINANCEIRO MOBILE ─── --}}
                                    @canany(['financeiro.dashboard.view', 'boletos.view', 'cartoes.view',
                                        'financeiro.reports'])
                                        <div class="pt-3 pb-1">
                                            <p class="px-3 text-xs font-bold uppercase tracking-wider text-gray-400">
                                                Financeiro
                                            </p>
                                        </div>

                                        @can('financeiro.dashboard.view')
                                            <a href="{{ route('financeiro.dashboard') }}"
                                                class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm transition-colors
                                                {{ request()->routeIs('financeiro.dashboard') ? 'bg-emerald-50 text-emerald-700 font-medium' : 'text-gray-700 hover:bg-gray-50' }}">
                                                <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z" />
                                                    <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z" />
                                                </svg>
                                                Dashboard Financeiro
                                            </a>
                                        @endcan

                                        @can('boletos.view')
                                            {{-- <a href="{{ route('financeiro.boletos.index') }}" --}}
                                            <a href="#"
                                                class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm transition-colors
                                                {{ request()->routeIs('financeiro.boletos.*') ? 'bg-emerald-50 text-emerald-700 font-medium' : 'text-gray-700 hover:bg-gray-50' }}">
                                                <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                Boletos
                                            </a>
                                        @endcan

                                        @can('cartoes.view')
                                            {{-- <a href="{{ route('financeiro.cartoes.index') }}" --}}
                                            <a href="#"
                                                class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm transition-colors
                                                {{ request()->routeIs('financeiro.cartoes.*') ? 'bg-emerald-50 text-emerald-700 font-medium' : 'text-gray-700 hover:bg-gray-50' }}">
                                                <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z" />
                                                    <path fill-rule="evenodd"
                                                        d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                Cartões de Crédito
                                            </a>
                                        @endcan

                                        @can('financeiro.reports')
                                            {{-- <a href="{{ route('financeiro.relatorios.index') }}" --}}
                                            <a href="#"
                                                class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm transition-colors
                                                {{ request()->routeIs('financeiro.relatorios.*') ? 'bg-emerald-50 text-emerald-700 font-medium' : 'text-gray-700 hover:bg-gray-50' }}">
                                                <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                    <path
                                                        d="M15.5 2A1.5 1.5 0 0014 3.5v13a1.5 1.5 0 001.5 1.5h1a1.5 1.5 0 001.5-1.5v-13A1.5 1.5 0 0016.5 2h-1zM9.5 6A1.5 1.5 0 008 7.5v9A1.5 1.5 0 009.5 18h1a1.5 1.5 0 001.5-1.5v-9A1.5 1.5 0 0010.5 6h-1zM3.5 10A1.5 1.5 0 002 11.5v5A1.5 1.5 0 003.5 18h1A1.5 1.5 0 006 16.5v-5A1.5 1.5 0 004.5 10h-1z" />
                                                </svg>
                                                Relatórios Financeiros
                                            </a>
                                        @endcan
                                    @endcanany

                                    {{-- Admin Mobile --}}
                                    {{-- @role('admin')
                                        <div class="pt-3 pb-1">
                                            <p class="px-3 text-xs font-bold uppercase tracking-wider text-gray-400">
                                                Administração
                                            </p>
                                        </div>
                                        <a href="#"
                                            class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm transition-colors
                                            {{ request()->routeIs('admin.*') ? 'bg-red-50 text-red-700 font-medium' : 'text-gray-700 hover:bg-gray-50' }}">
                                            <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            Usuários & Perfis
                                        </a>
                                    @endrole --}}
                                    @role('admin')
                                        <div class="pt-3 pb-1">
                                            <p class="px-3 text-xs font-bold uppercase tracking-wider text-gray-400">
                                                Administração</p>
                                        </div>
                                        <a href="{{ route('admin.users.index') }}"
                                            class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm transition-colors
        {{ request()->routeIs('admin.users.*') ? 'bg-red-50 text-red-700 font-medium' : 'text-gray-700 hover:bg-gray-50' }}">
                                            <span class="text-lg">👥</span> Usuários
                                        </a>
                                        <a href="{{ route('admin.roles.index') }}"
                                            class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm transition-colors
        {{ request()->routeIs('admin.roles.*') ? 'bg-red-50 text-red-700 font-medium' : 'text-gray-700 hover:bg-gray-50' }}">
                                            <span class="text-lg">🔑</span> Perfis
                                        </a>
                                        <a href="{{ route('admin.permissions.index') }}"
                                            class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm transition-colors
        {{ request()->routeIs('admin.permissions.*') ? 'bg-red-50 text-red-700 font-medium' : 'text-gray-700 hover:bg-gray-50' }}">
                                            <span class="text-lg">🔒</span> Permissões
                                        </a>
                                        {{-- Logs Activity --}}
                                        <a href="{{ route('admin.activity-logs.index') }}"
                                            class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm transition-colors
        {{ request()->routeIs('admin.activity-logs.*') ? 'bg-red-50 text-red-700 font-medium' : 'text-gray-700 hover:bg-gray-50' }}">
                                            <span class="text-lg">📜</span> Logs
                                        </a>
                                    @endrole

                                    {{-- Usuário mobile --}}
                                    <div class="mt-2 pt-3 border-t border-gray-100">
                                        <div class="flex items-center gap-3 px-3 py-2 mb-1">
                                            <div
                                                class="flex h-9 w-9 items-center justify-center rounded-full bg-indigo-600 text-white text-sm font-bold flex-shrink-0">
                                                {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-sm font-semibold text-gray-900 truncate">
                                                    {{ auth()->user()->name }}</p>
                                                <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}
                                                </p>
                                            </div>
                                        </div>
                                        <a href="{{ route('profile.edit') }}"
                                            class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                            <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            Meu Perfil
                                        </a>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit"
                                                class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                Sair do sistema
                                            </button>
                                        </form>
                                    </div>

                                </div>
                            </div>
                        </div>
                        {{-- /Mobile --}}

                    </div>
                    {{-- /RIGHT --}}

                </div>
            </div>
        </nav>
        {{-- /NAVBAR --}}

        {{-- Flash messages globais --}}
        @if (session('success') || session('error') || session('warning') || session('info'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 space-y-2">

                @if (session('success'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                        x-transition:leave="transition ease-in duration-300"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2"
                        class="flex items-center justify-between gap-3 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-800 ring-1 ring-green-600/20">
                        <div class="flex items-center gap-2">
                            <svg class="h-5 w-5 flex-shrink-0 text-green-500" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"
                                    clip-rule="evenodd" />
                            </svg>
                            {{ session('success') }}
                        </div>
                        <button @click="show = false" class="text-green-500 hover:text-green-700">
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path
                                    d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                            </svg>
                        </button>
                    </div>
                @endif

                @if (session('error'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                        x-transition:leave="transition ease-in duration-300"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2"
                        class="flex items-center justify-between gap-3 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-800 ring-1 ring-red-600/20">
                        <div class="flex items-center gap-2">
                            <svg class="h-5 w-5 flex-shrink-0 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z"
                                    clip-rule="evenodd" />
                            </svg>
                            {{ session('error') }}
                        </div>
                        <button @click="show = false" class="text-red-400 hover:text-red-600">
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path
                                    d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                            </svg>
                        </button>
                    </div>
                @endif

                @if (session('warning'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)"
                        x-transition:leave="transition ease-in duration-300"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2"
                        class="flex items-center justify-between gap-3 rounded-lg bg-yellow-50 px-4 py-3 text-sm text-yellow-800 ring-1 ring-yellow-600/20">
                        <div class="flex items-center gap-2">
                            <svg class="h-5 w-5 flex-shrink-0 text-yellow-500" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z"
                                    clip-rule="evenodd" />
                            </svg>
                            {{ session('warning') }}
                        </div>
                        <button @click="show = false" class="text-yellow-500 hover:text-yellow-700">
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path
                                    d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                            </svg>
                        </button>
                    </div>
                @endif

            </div>
        @endif

        {{-- CONTEÚDO PRINCIPAL --}}
        <main class="py-6">
            @yield('content')
        </main>

    </div>

    @stack('scripts')
</body>

</html>
