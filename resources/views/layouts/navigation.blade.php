<!-- Navigation Links -->
<div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
        {{ __('Dashboard') }}
    </x-nav-link>

    @can('rh.visualizar')
        <!-- Menu RH -->
        <div class="relative" x-data="{ open: false }">
            <button @click="open = ! open"
                class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                RH
                <svg class="ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                        clip-rule="evenodd" />
                </svg>
            </button>

            <div x-show="open" @click.away="open = false"
                class="absolute z-50 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5">
                <div class="py-1">
                    <a href="{{ route('rh.dashboard') }}"
                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Dashboard RH</a>
                    @can('funcionarios.visualizar')
                        <a href="{{ route('rh.funcionarios.index') }}"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Funcionários</a>
                    @endcan
                    @can('folha.visualizar')
                        <a href="{{ route('rh.folha-pagamento.index') }}"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Folha de Pagamento</a>
                    @endcan
                    @can('departamentos.gerenciar')
                        <a href="{{ route('rh.departamentos.index') }}"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Departamentos</a>
                    @endcan
                    @can('cargos.gerenciar')
                        <a href="{{ route('rh.cargos.index') }}"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Cargos</a>
                    @endcan
                </div>
            </div>
        </div>
    @endcan
</div>
