@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6" x-data="{ tab: 'pessoal' }">

        {{-- Cabeçalho --}}
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-sm text-gray-500 mb-1">
                    <a href="{{ route('rh.funcionarios.index') }}" class="hover:text-indigo-600">Funcionários</a>
                    <span>/</span>
                    <span class="text-gray-700 font-medium">Novo Funcionário</span>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">Cadastrar Funcionário</h1>
                <p class="mt-1 text-sm text-gray-500">Preencha os dados do novo colaborador. Campos marcados com <span
                        class="text-red-500">*</span> são obrigatórios.</p>
            </div>

            <a href="{{ route('rh.funcionarios.index') }}"
                class="inline-flex items-center gap-1.5 rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 self-start sm:self-auto">
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M17 10a.75.75 0 01-.75.75H5.612l4.158 3.96a.75.75 0 11-1.04 1.08l-5.5-5.25a.75.75 0 010-1.08l5.5-5.25a.75.75 0 111.04 1.08L5.612 9.25H16.25A.75.75 0 0117 10z"
                        clip-rule="evenodd" />
                </svg>
                Voltar
            </a>
        </div>

        {{-- Flash / Erros --}}
        @if ($errors->any())
            <div class="mb-6 rounded-md border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                <div class="flex items-start gap-3">
                    <svg class="h-5 w-5 text-red-500 shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z"
                            clip-rule="evenodd" />
                    </svg>
                    <div>
                        <strong class="font-semibold">Corrija os erros antes de continuar:</strong>
                        <ul class="mt-1.5 list-disc list-inside space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('rh.funcionarios.store') }}">
            @csrf

            {{-- ABAS --}}
            <div class="mb-6 border-b border-gray-200 overflow-x-auto">
                <nav class="-mb-px flex gap-1 min-w-max">
                    @foreach ([
            ['key' => 'pessoal', 'label' => 'Dados Pessoais', 'icon' => 'M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z'],
            ['key' => 'endereco', 'label' => 'Endereço', 'icon' => 'M15 10.5a3 3 0 11-6 0 3 3 0 016 0z M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z'],
            ['key' => 'documentos', 'label' => 'Documentos', 'icon' => 'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z'],
            ['key' => 'contrato', 'label' => 'Contrato & Salário', 'icon' => 'M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z'],
            ['key' => 'beneficios', 'label' => 'Benefícios', 'icon' => 'M21 11.25v8.25a1.5 1.5 0 01-1.5 1.5H5.25a1.5 1.5 0 01-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 109.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1114.625 7.5H12m0 0V21m-8.625-9.75h18c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125h-18c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z'],
            ['key' => 'bancario', 'label' => 'Dados Bancários', 'icon' => 'M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z'],
            ['key' => 'adicionais', 'label' => 'Adicionais', 'icon' => 'M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
        ] as $aba)
                        <button type="button" @click="tab = '{{ $aba['key'] }}'"
                            :class="tab === '{{ $aba['key'] }}'
                                ?
                                'border-indigo-600 text-indigo-700 bg-indigo-50' :
                                'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="inline-flex items-center gap-2 border-b-2 px-4 py-3 text-sm font-medium whitespace-nowrap transition-colors">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $aba['icon'] }}" />
                            </svg>
                            {{ $aba['label'] }}
                        </button>
                    @endforeach
                </nav>
            </div>

            {{-- ============================================================ --}}
            {{-- ABA 1: DADOS PESSOAIS --}}
            {{-- ============================================================ --}}
            <div x-show="tab === 'pessoal'" x-transition>
                <div class="rounded-lg bg-white shadow ring-1 ring-black/5 p-6 space-y-6">
                    <h2 class="text-base font-semibold text-gray-900 border-b border-gray-100 pb-3">Informações Pessoais
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">

                        {{-- Nome Completo --}}
                        <div class="lg:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">
                                Nome Completo <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nome_completo" value="{{ old('nome_completo') }}"
                                placeholder="Ex: João da Silva Santos"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm @error('nome_completo') border-red-400 @enderror"
                                required />
                            @error('nome_completo')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- CPF --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                CPF <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="cpf" value="{{ old('cpf') }}" placeholder="000.000.000-00"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm @error('cpf') border-red-400 @enderror"
                                required />
                            @error('cpf')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- RG --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">RG</label>
                            <input type="text" name="rg" value="{{ old('rg') }}" placeholder="0000000"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                            @error('rg')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Órgão Expedidor --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Órgão Expedidor RG</label>
                            <input type="text" name="orgao_expedidor_rg" value="{{ old('orgao_expedidor_rg') }}"
                                placeholder="SSP/RO"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                            @error('orgao_expedidor_rg')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Data de Nascimento --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Data de Nascimento</label>
                            <input type="date" name="data_nascimento" value="{{ old('data_nascimento') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                            @error('data_nascimento')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Estado Civil --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Estado Civil</label>
                            <select name="estado_civil"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <option value="">Selecione...</option>
                                @foreach ([
            'solteiro' => 'Solteiro(a)',
            'casado' => 'Casado(a)',
            'divorciado' => 'Divorciado(a)',
            'viuvo' => 'Viúvo(a)',
            'uniao_estavel' => 'União Estável',
        ] as $val => $lbl)
                                    <option value="{{ $val }}" @selected(old('estado_civil') === $val)>{{ $lbl }}
                                    </option>
                                @endforeach
                            </select>
                            @error('estado_civil')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Gênero --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Gênero</label>
                            <select name="genero"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <option value="">Selecione...</option>
                                <option value="masculino" @selected(old('genero') === 'masculino')>Masculino</option>
                                <option value="feminino" @selected(old('genero') === 'feminino')>Feminino</option>
                                <option value="outro" @selected(old('genero') === 'outro')>Outro</option>
                            </select>
                            @error('genero')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Nacionalidade --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nacionalidade</label>
                            <input type="text" name="nacionalidade" value="{{ old('nacionalidade', 'Brasileiro(a)') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                            @error('nacionalidade')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Naturalidade --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Naturalidade</label>
                            <input type="text" name="naturalidade" value="{{ old('naturalidade') }}"
                                placeholder="Porto Velho/RO"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                            @error('naturalidade')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Telefone --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Telefone</label>
                            <input type="text" name="telefone" value="{{ old('telefone') }}"
                                placeholder="(69) 3000-0000"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                            @error('telefone')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Celular --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Celular</label>
                            <input type="text" name="celular" value="{{ old('celular') }}"
                                placeholder="(69) 9 9000-0000"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                            @error('celular')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- E-mail Corporativo --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">E-mail Corporativo</label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                placeholder="colaborador@empresa.com"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                            @error('email')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- E-mail Pessoal --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">E-mail Pessoal</label>
                            <input type="email" name="email_pessoal" value="{{ old('email_pessoal') }}"
                                placeholder="pessoal@gmail.com"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                            @error('email_pessoal')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Ativo --}}
                        <div class="flex items-center gap-3 sm:pt-6">
                            <input type="hidden" name="ativo" value="0">
                            <input id="ativo" type="checkbox" name="ativo" value="1"
                                @checked(old('ativo', true))
                                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                            <label for="ativo" class="text-sm font-medium text-gray-700">Funcionário ativo</label>
                        </div>
                    </div>

                    {{-- Observações --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Observações</label>
                        <textarea name="observacoes" rows="3" placeholder="Informações adicionais sobre o funcionário..."
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">{{ old('observacoes') }}</textarea>
                        @error('observacoes')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- ABA 2: ENDEREÇO --}}
            {{-- ============================================================ --}}
            <div x-show="tab === 'endereco'" x-transition>
                <div class="rounded-lg bg-white shadow ring-1 ring-black/5 p-6 space-y-6">
                    <h2 class="text-base font-semibold text-gray-900 border-b border-gray-100 pb-3">Endereço Residencial
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

                        {{-- CEP --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">CEP</label>
                            <input type="text" name="cep" value="{{ old('cep') }}" placeholder="00000-000"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                            @error('cep')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Logradouro --}}
                        <div class="lg:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Logradouro</label>
                            <input type="text" name="logradouro" value="{{ old('logradouro') }}"
                                placeholder="Rua, Avenida, Travessa..."
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                            @error('logradouro')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Número --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Número</label>
                            <input type="text" name="numero" value="{{ old('numero') }}" placeholder="123 / S/N"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                            @error('numero')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Complemento --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Complemento</label>
                            <input type="text" name="complemento" value="{{ old('complemento') }}"
                                placeholder="Apto 10, Bloco B..."
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                            @error('complemento')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Bairro --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Bairro</label>
                            <input type="text" name="bairro" value="{{ old('bairro') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                            @error('bairro')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Cidade --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Cidade</label>
                            <input type="text" name="cidade" value="{{ old('cidade', 'Porto Velho') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                            @error('cidade')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Estado --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Estado (UF)</label>
                            <select name="estado"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <option value="">UF</option>
                                @foreach (['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'] as $uf)
                                    <option value="{{ $uf }}" @selected(old('estado', 'RO') === $uf)>{{ $uf }}
                                    </option>
                                @endforeach
                            </select>
                            @error('estado')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- ABA 3: DOCUMENTOS --}}
            {{-- ============================================================ --}}
            <div x-show="tab === 'documentos'" x-transition>
                <div class="rounded-lg bg-white shadow ring-1 ring-black/5 p-6 space-y-8">

                    {{-- CTPS --}}
                    <div>
                        <h2 class="text-base font-semibold text-gray-900 border-b border-gray-100 pb-3 mb-5">
                            Carteira de Trabalho (CTPS)
                        </h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Número CTPS</label>
                                <input type="text" name="ctps_numero" value="{{ old('ctps_numero') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                                @error('ctps_numero')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Série</label>
                                <input type="text" name="ctps_serie" value="{{ old('ctps_serie') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                                @error('ctps_serie')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">UF CTPS</label>
                                <select name="ctps_uf"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    <option value="">UF</option>
                                    @foreach (['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'] as $uf)
                                        <option value="{{ $uf }}" @selected(old('ctps_uf', 'RO') === $uf)>
                                            {{ $uf }}</option>
                                    @endforeach
                                </select>
                                @error('ctps_uf')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Data de Emissão</label>
                                <input type="date" name="ctps_data_emissao" value="{{ old('ctps_data_emissao') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                                @error('ctps_data_emissao')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Outros Documentos --}}
                    <div>
                        <h2 class="text-base font-semibold text-gray-900 border-b border-gray-100 pb-3 mb-5">
                            Outros Documentos
                        </h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">PIS/PASEP</label>
                                <input type="text" name="pis_pasep" value="{{ old('pis_pasep') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                                @error('pis_pasep')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Título de Eleitor</label>
                                <input type="text" name="titulo_eleitor" value="{{ old('titulo_eleitor') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                                @error('titulo_eleitor')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Zona Eleitoral</label>
                                <input type="text" name="zona_eleitoral" value="{{ old('zona_eleitoral') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                                @error('zona_eleitoral')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Seção Eleitoral</label>
                                <input type="text" name="secao_eleitoral" value="{{ old('secao_eleitoral') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                                @error('secao_eleitoral')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Certificado Reservista</label>
                                <input type="text" name="certificado_reservista"
                                    value="{{ old('certificado_reservista') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                                @error('certificado_reservista')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- ABA 4: CONTRATO & SALÁRIO --}}
            {{-- ============================================================ --}}
            <div x-show="tab === 'contrato'" x-transition>
                <div class="rounded-lg bg-white shadow ring-1 ring-black/5 p-6 space-y-8">

                    {{-- Vínculo --}}
                    <div>
                        <h2 class="text-base font-semibold text-gray-900 border-b border-gray-100 pb-3 mb-5">
                            Vínculo Empregatício
                        </h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Departamento <span class="text-red-500">*</span>
                                </label>
                                <select name="departamento_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm @error('departamento_id') border-red-400 @enderror"
                                    required>
                                    <option value="">Selecione...</option>
                                    @foreach ($departamentos as $dep)
                                        <option value="{{ $dep->id }}" @selected(old('departamento_id') == $dep->id)>
                                            {{ $dep->nome }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('departamento_id')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Cargo <span class="text-red-500">*</span>
                                </label>
                                <select name="cargo_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm @error('cargo_id') border-red-400 @enderror"
                                    required>
                                    <option value="">Selecione...</option>
                                    @foreach ($cargos as $cargo)
                                        <option value="{{ $cargo->id }}" @selected(old('cargo_id') == $cargo->id)>
                                            {{ $cargo->titulo }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('cargo_id')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Local de Trabalho</label>
                                <input type="text" name="local_trabalho" value="{{ old('local_trabalho') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                                @error('local_trabalho')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tipo de Contrato</label>
                                <select name="tipo_contrato"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    <option value="">Selecione...</option>
                                    @foreach ([
            'clt' => 'CLT',
            'pj' => 'PJ',
            'estagio' => 'Estágio',
            'temporario' => 'Temporário',
            'autonomo' => 'Autônomo',
        ] as $val => $lbl)
                                        <option value="{{ $val }}" @selected(old('tipo_contrato') === $val)>
                                            {{ $lbl }}</option>
                                    @endforeach
                                </select>
                                @error('tipo_contrato')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tipo de Contratação</label>
                                <input type="text" name="tipo_contratacao" value="{{ old('tipo_contratacao') }}"
                                    placeholder="Ex: Integral, Parcial..."
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                                @error('tipo_contratacao')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Data de Admissão <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="data_admissao" value="{{ old('data_admissao') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm @error('data_admissao') border-red-400 @enderror"
                                    required />
                                @error('data_admissao')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Remuneração --}}
                    <div>
                        <h2 class="text-base font-semibold text-gray-900 border-b border-gray-100 pb-3 mb-5">Remuneração
                        </h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Salário Base <span class="text-red-500">*</span>
                                </label>
                                <div class="relative mt-1">
                                    <span
                                        class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 text-sm">R$</span>
                                    <input type="number" name="salario_base" step="0.01" min="0"
                                        value="{{ old('salario_base') }}" placeholder="0,00"
                                        class="block w-full rounded-md border-gray-300 pl-9 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm @error('salario_base') border-red-400 @enderror"
                                        required />
                                </div>
                                @error('salario_base')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Carga Horária Semanal (h)</label>
                                <input type="number" name="carga_horaria_semanal" min="1" max="44"
                                    value="{{ old('carga_horaria_semanal', 44) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                                @error('carga_horaria_semanal')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Qtd. Dependentes IR</label>
                                <input type="number" name="qtd_dependentes_ir" min="0"
                                    value="{{ old('qtd_dependentes_ir', 0) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                                @error('qtd_dependentes_ir')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Qtd. Dependentes Salário
                                    Família</label>
                                <input type="number" name="qtd_dependentes_salario_familia" min="0"
                                    value="{{ old('qtd_dependentes_salario_familia', 0) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                                @error('qtd_dependentes_salario_familia')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Horários --}}
                    <div>
                        <h2 class="text-base font-semibold text-gray-900 border-b border-gray-100 pb-3 mb-5">
                            Horário de Trabalho
                        </h2>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-5">
                            @foreach ([['horario_entrada', 'Entrada', '08:00'], ['horario_saida', 'Saída', '17:00'], ['horario_almoco_inicio', 'Início Almoço', '12:00'], ['horario_almoco_fim', 'Fim Almoço', '13:00']] as [$campo, $label, $default])
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">{{ $label }}</label>
                                    <input type="time" name="{{ $campo }}"
                                        value="{{ old($campo, $default) }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                                    @error($campo)
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Diarista --}}
                    <div>
                        <h2 class="text-base font-semibold text-gray-900 border-b border-gray-100 pb-3 mb-5">
                            Modalidade Diarista
                        </h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5" x-data="{ diarista: {{ old('eh_diarista') ? 'true' : 'false' }} }">
                            <div class="flex items-center gap-3">
                                <input type="hidden" name="eh_diarista" value="0">
                                <input id="eh_diarista" type="checkbox" name="eh_diarista" value="1"
                                    x-model="diarista" @checked(old('eh_diarista'))
                                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                                <label for="eh_diarista" class="text-sm font-medium text-gray-700">É diarista?</label>
                            </div>
                            <div x-show="diarista" x-transition>
                                <label class="block text-sm font-medium text-gray-700">Valor da Diária</label>
                                <div class="relative mt-1">
                                    <span
                                        class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 text-sm">R$</span>
                                    <input type="number" name="valor_diaria" step="0.01" min="0"
                                        value="{{ old('valor_diaria', '0.00') }}" placeholder="0,00"
                                        class="block w-full rounded-md border-gray-300 pl-9 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                                </div>
                                @error('valor_diaria')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- ABA 5: BENEFÍCIOS --}}
            {{-- ============================================================ --}}
            <div x-show="tab === 'beneficios'" x-transition>
                <div class="rounded-lg bg-white shadow ring-1 ring-black/5 p-6 space-y-6">
                    <h2 class="text-base font-semibold text-gray-900 border-b border-gray-100 pb-3">Benefícios</h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

                        {{-- Vale Transporte --}}
                        <div class="rounded-lg border border-gray-200 p-4 space-y-3" x-data="{ ativo: {{ old('vale_transporte') ? 'true' : 'false' }} }">
                            <div class="flex items-center gap-3">
                                <input type="hidden" name="vale_transporte" value="0">
                                <input id="vale_transporte" type="checkbox" name="vale_transporte" value="1"
                                    x-model="ativo" @checked(old('vale_transporte'))
                                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                                <label for="vale_transporte" class="text-sm font-semibold text-gray-700">Vale
                                    Transporte</label>
                            </div>
                            <div x-show="ativo" x-transition>
                                <label class="block text-sm font-medium text-gray-700">Valor mensal</label>
                                <div class="relative mt-1">
                                    <span
                                        class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 text-sm">R$</span>
                                    <input type="number" name="valor_vale_transporte" step="0.01" min="0"
                                        value="{{ old('valor_vale_transporte', '0.00') }}" placeholder="0,00"
                                        class="block w-full rounded-md border-gray-300 pl-9 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                                </div>
                                @error('valor_vale_transporte')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Vale Alimentação --}}
                        <div class="rounded-lg border border-gray-200 p-4 space-y-3" x-data="{ ativo: {{ old('vale_alimentacao') ? 'true' : 'false' }} }">
                            <div class="flex items-center gap-3">
                                <input type="hidden" name="vale_alimentacao" value="0">
                                <input id="vale_alimentacao" type="checkbox" name="vale_alimentacao" value="1"
                                    x-model="ativo" @checked(old('vale_alimentacao'))
                                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                                <label for="vale_alimentacao" class="text-sm font-semibold text-gray-700">Vale
                                    Alimentação</label>
                            </div>
                            <div x-show="ativo" x-transition>
                                <label class="block text-sm font-medium text-gray-700">Valor mensal</label>
                                <div class="relative mt-1">
                                    <span
                                        class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 text-sm">R$</span>
                                    <input type="number" name="valor_vale_alimentacao" step="0.01" min="0"
                                        value="{{ old('valor_vale_alimentacao', '0.00') }}" placeholder="0,00"
                                        class="block w-full rounded-md border-gray-300 pl-9 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                                </div>
                                @error('valor_vale_alimentacao')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Vale Extra --}}
                        <div class="rounded-lg border border-gray-200 p-4 space-y-3">
                            <p class="text-sm font-semibold text-gray-700">Vale Extra</p>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Valor</label>
                                <div class="relative mt-1">
                                    <span
                                        class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 text-sm">R$</span>
                                    <input type="number" name="vale_extra" step="0.01" min="0"
                                        value="{{ old('vale_extra', '0.00') }}" placeholder="0,00"
                                        class="block w-full rounded-md border-gray-300 pl-9 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                                </div>
                                @error('vale_extra')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Planos de Saúde --}}
                        <div class="rounded-lg border border-gray-200 p-4 space-y-4">
                            <p class="text-sm font-semibold text-gray-700">Planos de Saúde</p>
                            <div class="flex items-center gap-3">
                                <input type="hidden" name="plano_saude" value="0">
                                <input id="plano_saude" type="checkbox" name="plano_saude" value="1"
                                    @checked(old('plano_saude'))
                                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                                <label for="plano_saude" class="text-sm text-gray-700">Plano de Saúde</label>
                            </div>
                            <div class="flex items-center gap-3">
                                <input type="hidden" name="plano_odontologico" value="0">
                                <input id="plano_odontologico" type="checkbox" name="plano_odontologico" value="1"
                                    @checked(old('plano_odontologico'))
                                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                                <label for="plano_odontologico" class="text-sm text-gray-700">Plano Odontológico</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- ABA 6: DADOS BANCÁRIOS --}}
            {{-- ============================================================ --}}
            <div x-show="tab === 'bancario'" x-transition>
                <div class="rounded-lg bg-white shadow ring-1 ring-black/5 p-6 space-y-6">
                    <h2 class="text-base font-semibold text-gray-900 border-b border-gray-100 pb-3">Dados Bancários</h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Código do Banco</label>
                            <input type="text" name="banco_codigo" value="{{ old('banco_codigo') }}"
                                placeholder="001"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                            @error('banco_codigo')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="lg:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Nome do Banco</label>
                            <input type="text" name="banco_nome" value="{{ old('banco_nome') }}"
                                placeholder="Ex: Banco do Brasil, Caixa..."
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                            @error('banco_nome')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Agência</label>
                            <input type="text" name="agencia" value="{{ old('agencia') }}" placeholder="0000-0"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                            @error('agencia')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Conta</label>
                            <input type="text" name="conta" value="{{ old('conta') }}" placeholder="00000-0"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                            @error('conta')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tipo de Conta</label>
                            <select name="tipo_conta"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <option value="">Selecione...</option>
                                @foreach ([
            'corrente' => 'Corrente',
            'poupanca' => 'Poupança',
            'salario' => 'Conta Salário',
        ] as $val => $lbl)
                                    <option value="{{ $val }}" @selected(old('tipo_conta') === $val)>
                                        {{ $lbl }}</option>
                                @endforeach
                            </select>
                            @error('tipo_conta')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- ABA 7: ADICIONAIS --}}
            {{-- ============================================================ --}}
            <div x-show="tab === 'adicionais'" x-transition>
                <div class="rounded-lg bg-white shadow ring-1 ring-black/5 p-6 space-y-8">

                    {{-- Proventos --}}
                    <div>
                        <h2 class="text-base font-semibold text-gray-900 border-b border-gray-100 pb-3 mb-5">Proventos</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                            @foreach ([['gratificacao_provento', 'Gratificação / Provento'], ['hora_extra', 'Hora Extra (R$)'], ['dsr_hora_extra', 'DSR Hora Extra (R$)'], ['salario_familia', 'Salário Família (R$)']] as [$campo, $label])
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">{{ $label }}</label>
                                    <div class="relative mt-1">
                                        <span
                                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 text-sm">R$</span>
                                        <input type="number" name="{{ $campo }}" step="0.01" min="0"
                                            value="{{ old($campo, '0.00') }}" placeholder="0,00"
                                            class="block w-full rounded-md border-gray-300 pl-9 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                                    </div>
                                    @error($campo)
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endforeach

                            {{-- Sexto Dia Útil --}}
                            <div class="flex items-center gap-3 sm:pt-6">
                                <input type="hidden" name="sexto_dia_util_mes" value="0">
                                <input id="sexto_dia_util_mes" type="checkbox" name="sexto_dia_util_mes" value="1"
                                    @checked(old('sexto_dia_util_mes'))
                                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                                <label for="sexto_dia_util_mes" class="text-sm font-medium text-gray-700">
                                    6º Dia Útil do Mês
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Descontos --}}
                    <div>
                        <h2 class="text-base font-semibold text-gray-900 border-b border-gray-100 pb-3 mb-5">Descontos</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                            @foreach ([['desconto_inss_8_porcento', 'Desconto INSS 8% (R$)', true], ['faltas', 'Faltas (dias)', false], ['dsr_faltas', 'DSR Faltas (R$)', true], ['desconto_faltas', 'Desconto Faltas (R$)', true]] as [$campo, $label, $moeda])
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">{{ $label }}</label>
                                    <div class="relative mt-1">
                                        @if ($moeda)
                                            <span
                                                class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 text-sm">R$</span>
                                        @endif
                                        <input type="number" name="{{ $campo }}" step="0.01" min="0"
                                            value="{{ old($campo, '0.00') }}" placeholder="0,00"
                                            class="block w-full rounded-md border-gray-300 {{ $moeda ? 'pl-9' : '' }} shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                                    </div>
                                    @error($campo)
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Info --}}
                    <div class="rounded-lg bg-blue-50 border border-blue-200 p-4 flex items-start gap-3">
                        <svg class="h-5 w-5 text-blue-500 shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5H9z"
                                clip-rule="evenodd" />
                        </svg>
                        <div>
                            <h3 class="text-sm font-semibold text-blue-700">Informação</h3>
                            <p class="text-sm text-blue-700">
                                Os campos com <span class="font-semibold">(*)</span> são obrigatórios.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- BARRA DE AÇÕES FIXA NO RODAPÉ --}}
            {{-- ============================================================ --}}
            <div
                class="mt-6 flex items-center justify-between gap-3 rounded-lg bg-white px-6 py-4 shadow ring-1 ring-black/5">
                <a href="{{ route('rh.funcionarios.index') }}"
                    class="inline-flex items-center gap-1.5 rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M17 10a.75.75 0 01-.75.75H5.612l4.158 3.96a.75.75 0 11-1.04 1.08l-5.5-5.25a.75.75 0 010-1.08l5.5-5.25a.75.75 0 111.04 1.08L5.612 9.25H16.25A.75.75 0 0117 10z"
                            clip-rule="evenodd" />
                    </svg>
                    Cancelar
                </a>

                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-md bg-indigo-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z"
                            clip-rule="evenodd" />
                    </svg>
                    Cadastrar Funcionário
                </button>
            </div>

        </form>
    </div>
@endsection
