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
</head>

<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen">
        <nav class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 justify-between">
                    <!-- LEFT -->
                    <div class="flex">
                        <!-- Logo -->
                        <div class="flex shrink-0 items-center">
                            <a href="{{ route('dashboard') }}" class="text-lg font-semibold text-indigo-600">
                                RH & Financeiro
                            </a>
                        </div>

                        <!-- Desktop Links -->
                        <div class="hidden sm:ml-8 sm:flex sm:items-stretch sm:space-x-6">
                            <a href="{{ route('dashboard') }}"
                                class="inline-flex items-center border-b-2 px-1 pt-1 text-sm font-medium
                               @if (request()->routeIs('dashboard')) border-indigo-600 text-gray-900
                               @else border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 @endif">
                                Dashboard
                            </a>

                            @can('rh.dashboard.view')
                                <!-- RH dropdown (DESKTOP) -->
                                <div class="relative flex items-stretch" x-data="{ open: false }">
                                    <button type="button" @click="open = !open"
                                        class="inline-flex items-center border-b-2 px-1 pt-1 text-sm font-medium
                                            @if (request()->routeIs('rh.*')) border-indigo-600 text-gray-900
                                            @else border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 @endif">
                                        RH
                                        <svg class="ml-2 h-4 w-4 text-current" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>

                                    <div x-show="open" x-cloak x-transition @click.away="open = false"
                                        class="absolute left-0 top-full z-50 mt-2 w-56 rounded-md bg-white shadow-lg ring-1 ring-black/5">
                                        <div class="py-1">
                                            <a href="{{ route('rh.dashboard') }}"
                                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                                Dashboard RH
                                            </a>

                                            @can('folha.view')
                                                <a href="{{ route('rh.folha-pagamento.index') }}"
                                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                                    Folha de Pagamento
                                                </a>
                                            @endcan

                                            @can('funcionarios.view')
                                                <a href="{{ route('rh.funcionarios.index') }}"
                                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                                    Funcionários
                                                </a>
                                            @endcan
                                            <!-- Cargos -->
                                            @can('cargos.manage')
                                                <a href="{{ route('rh.cargos.index') }}"
                                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                                    Cargos
                                                </a>
                                            @endcan
                                            <!-- Departamentos -->
                                            @can('departamentos.view')
                                                <a href="{{ route('rh.departamentos.index') }}"
                                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                                    Departamentos
                                                </a>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            @endcan

                            @can('financeiro.dashboard.view')
                                <!-- Financeiro dropdown (DESKTOP) -->
                                <div class="relative flex items-stretch" x-cloak x-data="{ open: false }">
                                    <button type="button" @click="open = !open"
                                        class="inline-flex items-center border-b-2 px-1 pt-1 text-sm font-medium
                                            @if (request()->routeIs('financeiro.*')) border-indigo-600 text-gray-900
                                            @else border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 @endif">
                                        Financeiro
                                        <svg class="ml-2 h-4 w-4 text-current" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>

                                    <div x-show="open" x-transition @click.away="open = false"
                                        class="absolute left-0 top-full z-50 mt-2 w-56 rounded-md bg-white shadow-lg ring-1 ring-black/5">
                                        <div class="py-1">
                                            <a href="{{ route('financeiro.dashboard') }}"
                                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                                Dashboard Financeiro
                                            </a>

                                            @can('boletos.view')
                                                <a href="#"
                                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                                    Boletos
                                                </a>
                                            @endcan

                                            @can('cartoes.view')
                                                <a href="#"
                                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                                    Cartões
                                                </a>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            @endcan
                        </div>
                    </div>

                    <!-- RIGHT -->
                    <div class="flex items-center gap-3">
                        <!-- Role badge -->
                        <span
                            class="hidden sm:inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-0.5 text-xs font-medium text-indigo-700 ring-1 ring-inset ring-indigo-100">
                            {{ auth()->user()->getRoleNames()->first() ?? 'Usuário' }}
                        </span>

                        <!-- User dropdown (DESKTOP) -->
                        <div class="relative hidden sm:block" x-clock x-data="{ open: false }">
                            <button type="button" @click="open = !open"
                                class="flex items-center gap-2 rounded-md px-2 py-1 text-sm text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-600">
                                    <span class="text-sm font-semibold text-white">
                                        {{ mb_substr(auth()->user()->name, 0, 1) }}
                                    </span>
                                </div>
                                <span class="hidden lg:block">{{ auth()->user()->name }}</span>
                                <svg class="h-4 w-4 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>

                            <div x-show="open" x-transition @click.away="open = false"
                                class="absolute right-0 z-50 mt-2 w-56 rounded-md bg-white shadow-lg ring-1 ring-black/5">
                                <div class="px-4 py-3 border-b border-gray-100">
                                    <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
                                </div>
                                <div class="py-1">
                                    <a href="{{ route('profile.edit') }}"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                        Perfil
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                            Sair
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Mobile menu button -->
                        <div class="sm:hidden" x-data="{ openMobile: false }">
                            <button type="button" @click="openMobile = !openMobile"
                                class="inline-flex items-center justify-center rounded-md p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path x-show="!openMobile" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    <path x-show="openMobile" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>

                            <!-- Mobile panel -->
                            <div x-show="openMobile" x-transition
                                class="absolute left-0 right-0 top-16 z-50 border-b border-gray-200 bg-white shadow-sm">
                                <div class="max-w-7xl mx-auto px-4 py-3 space-y-1">
                                    <a href="{{ route('dashboard') }}"
                                        class="block rounded-md px-3 py-2 text-sm font-medium
                                       @if (request()->routeIs('dashboard')) bg-indigo-50 text-indigo-700
                                       @else text-gray-700 hover:bg-gray-50 @endif">
                                        Dashboard
                                    </a>

                                    @can('rh.dashboard.view')
                                        <div class="pt-2">
                                            <p class="px-3 text-xs font-semibold uppercase tracking-wide text-gray-400">RH
                                            </p>
                                            <a href="{{ route('rh.dashboard') }}"
                                                class="block rounded-md px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">Dashboard
                                                RH</a>

                                            @can('folha.view')
                                                <a href="{{ route('rh.folha-pagamento.index') }}"
                                                    class="block rounded-md px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">Folha
                                                    de Pagamento</a>
                                            @endcan
                                        </div>
                                    @endcan

                                    @can('financeiro.dashboard.view')
                                        <div class="pt-2">
                                            <p class="px-3 text-xs font-semibold uppercase tracking-wide text-gray-400">
                                                Financeiro</p>
                                            <a href="{{ route('financeiro.dashboard') }}"
                                                class="block rounded-md px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">Dashboard
                                                Financeiro</a>

                                            @can('boletos.view')
                                                <a href="#"
                                                    class="block rounded-md px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">Boletos</a>
                                            @endcan

                                            @can('cartoes.view')
                                                <a href="#"
                                                    class="block rounded-md px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">Cartões</a>
                                            @endcan
                                        </div>
                                    @endcan

                                    <div class="pt-3 border-t border-gray-100">
                                        <div class="px-3 py-2">
                                            <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}
                                            </p>
                                            <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
                                        </div>
                                        <a href="{{ route('profile.edit') }}"
                                            class="block rounded-md px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">Perfil</a>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit"
                                                class="block w-full text-left rounded-md px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                                Sair
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /Mobile -->

                    </div>
                </div>
            </div>
        </nav>

        <main class="py-6">
            @yield('content')
        </main>
    </div>
</body>

</html>
