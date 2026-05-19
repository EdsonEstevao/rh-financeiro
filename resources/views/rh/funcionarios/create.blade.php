@extends('layouts.app')

@section('content')
    <div class="px-4 py-6 mx-auto max-w-7xl sm:px-6 lg:px-8" x-data="{ tab: 'pessoal' }">

        {{-- Cabeçalho --}}
        <div class="flex flex-col gap-4 mb-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="flex items-center gap-2 mb-1 text-sm text-gray-500">
                    <a href="{{ route('rh.funcionarios.index') }}" class="hover:text-indigo-600">Funcionários</a>
                    <span>/</span>
                    <span class="font-medium text-gray-700">Novo Funcionário</span>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">Cadastrar Funcionário</h1>
                <p class="mt-1 text-sm text-gray-500">Preencha os dados do novo colaborador. Campos marcados com <span
                        class="text-red-500">*</span> são obrigatórios.</p>
            </div>

            <a href="{{ route('rh.funcionarios.index') }}"
                class="inline-flex items-center gap-1.5 rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 self-start sm:self-auto">
                <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M17 10a.75.75 0 01-.75.75H5.612l4.158 3.96a.75.75 0 11-1.04 1.08l-5.5-5.25a.75.75 0 010-1.08l5.5-5.25a.75.75 0 111.04 1.08L5.612 9.25H16.25A.75.75 0 0117 10z"
                        clip-rule="evenodd" />
                </svg>
                Voltar
            </a>
        </div>

        {{-- Flash / Erros --}}
        @if ($errors->any())
            <div class="p-4 mb-6 text-sm text-red-800 border border-red-200 rounded-md bg-red-50">
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
            <div class="mb-6 overflow-x-auto border-b border-gray-200">
                <nav class="flex gap-1 -mb-px min-w-max">
                    @foreach ([
            ['key' => 'pessoal', 'label' => 'Dados Pessoais', 'icon' => 'M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z'],
            ['key' => 'endereco', 'label' => 'Endereço', 'icon' => 'M15 10.5a3 3 0 11-6 0 3 3 0 016 0z M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z'],
            ['key' => 'contato', 'label' => 'Contatos', 'icon' => 'M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z'],
            ['key' => 'documentos', 'label' => 'Documentos', 'icon' => 'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z'],
            ['key' => 'contrato', 'label' => 'Contrato & Salário', 'icon' => 'M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['key' => 'beneficios', 'label' => 'Benefícios', 'icon' => 'M21 11.25v8.25a1.5 1.5 0 01-1.5 1.5H5.25a1.5 1.5 0 01-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 109.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1114.625 7.5H12m0 0V21m-8.625-9.75h18c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125h-18c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z'],
            ['key' => 'bancario', 'label' => 'Dados Bancários', 'icon' => 'M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z'],
            ['key' => 'dependentes', 'label' => 'Dependentes', 'icon' => 'M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z'],
        ] as $aba)
                        <button type="button" @click="tab = '{{ $aba['key'] }}'"
                            :class="tab === '{{ $aba['key'] }}'
                                ?
                                'border-indigo-600 text-indigo-700 bg-indigo-50' :
                                'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="inline-flex items-center gap-2 px-4 py-3 text-sm font-medium transition-colors border-b-2 whitespace-nowrap">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $aba['icon'] }}" />
                            </svg>
                            {{ $aba['label'] }}
                        </button>
                    @endforeach
                </nav>
            </div>

            {{-- ============================================================ --}}
            {{-- ABA 1: DADOS PESSOAIS (tabela funcionarios) --}}
            {{-- ============================================================ --}}
            <div x-show="tab === 'pessoal'" x-transition>
                <div class="p-6 space-y-6 bg-white rounded-lg shadow ring-1 ring-black/5">
                    <h2 class="pb-3 text-base font-semibold text-gray-900 border-b border-gray-100">Informações Pessoais
                    </h2>

                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">

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

                        {{-- Data de Nascimento --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Data de Nascimento</label>
                            <input type="date" name="data_nascimento" value="{{ old('data_nascimento') }}"
                                class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            @error('data_nascimento')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Estado Civil --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Estado Civil</label>
                            <select name="estado_civil"
                                class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Selecione...</option>
                                @foreach (['solteiro' => 'Solteiro(a)', 'casado' => 'Casado(a)', 'divorciado' => 'Divorciado(a)', 'viuvo' => 'Viúvo(a)', 'uniao_estavel' => 'União Estável'] as $val => $lbl)
                                    <option value="{{ $val }}" @selected(old('estado_civil') === $val)>{{ $lbl }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Gênero --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Gênero</label>
                            <select name="genero"
                                class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Selecione...</option>
                                <option value="masculino" @selected(old('genero') === 'masculino')>Masculino</option>
                                <option value="feminino" @selected(old('genero') === 'feminino')>Feminino</option>
                                <option value="outro" @selected(old('genero') === 'outro')>Outro</option>
                            </select>
                        </div>

                        {{-- Nacionalidade --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nacionalidade</label>
                            <input type="text" name="nacionalidade" value="{{ old('nacionalidade', 'Brasileiro(a)') }}"
                                class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>

                        {{-- Naturalidade --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Naturalidade</label>
                            <input type="text" name="naturalidade" value="{{ old('naturalidade') }}"
                                placeholder="Porto Velho/RO"
                                class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>

                        {{-- Ativo --}}
                        <div class="flex items-center gap-3 sm:pt-6">
                            <input type="hidden" name="ativo" value="0">
                            <input id="ativo" type="checkbox" name="ativo" value="1" @checked(old('ativo', true))
                                class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" />
                            <label for="ativo" class="text-sm font-medium text-gray-700">Funcionário ativo</label>
                        </div>
                    </div>

                    {{-- Observações --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Observações</label>
                        <textarea name="observacoes" rows="3" placeholder="Informações adicionais sobre o funcionário..."
                            class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('observacoes') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- ABA 2: ENDEREÇO (tabela funcionario_enderecos) --}}
            {{-- ============================================================ --}}
            <div x-show="tab === 'endereco'" x-transition>
                <div class="p-6 space-y-6 bg-white rounded-lg shadow ring-1 ring-black/5">
                    <h2 class="pb-3 text-base font-semibold text-gray-900 border-b border-gray-100">Endereço Residencial
                    </h2>

                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">CEP</label>
                            <input type="text" name="cep" value="{{ old('cep') }}" placeholder="00000-000"
                                class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>
                        <div class="lg:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Logradouro</label>
                            <input type="text" name="logradouro" value="{{ old('logradouro') }}"
                                placeholder="Rua, Avenida, Travessa..."
                                class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Número</label>
                            <input type="text" name="numero" value="{{ old('numero') }}" placeholder="123"
                                class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Complemento</label>
                            <input type="text" name="complemento" value="{{ old('complemento') }}"
                                placeholder="Apto 10, Bloco B..."
                                class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Bairro</label>
                            <input type="text" name="bairro" value="{{ old('bairro') }}"
                                class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Cidade</label>
                            <input type="text" name="cidade" value="{{ old('cidade', 'Porto Velho') }}"
                                class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Estado (UF)</label>
                            <select name="estado"
                                class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">UF</option>
                                @foreach (['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'] as $uf)
                                    <option value="{{ $uf }}" @selected(old('estado', 'RO') === $uf)>{{ $uf }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- ABA 3: CONTATOS (tabela funcionario_contatos) --}}
            {{-- ============================================================ --}}
            <div x-show="tab === 'contato'" x-transition>
                <div class="p-6 space-y-6 bg-white rounded-lg shadow ring-1 ring-black/5">
                    <h2 class="pb-3 text-base font-semibold text-gray-900 border-b border-gray-100">Contatos</h2>

                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Telefone</label>
                            <input type="text" name="telefone" value="{{ old('telefone') }}"
                                placeholder="(69) 3000-0000"
                                class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Celular</label>
                            <input type="text" name="celular" value="{{ old('celular') }}"
                                placeholder="(69) 9 9000-0000"
                                class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">E-mail Corporativo</label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                placeholder="colaborador@empresa.com"
                                class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">E-mail Pessoal</label>
                            <input type="email" name="email_pessoal" value="{{ old('email_pessoal') }}"
                                placeholder="pessoal@gmail.com"
                                class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- ABA 4: DOCUMENTOS (tabela funcionario_documentos) --}}
            {{-- ============================================================ --}}
            <div x-show="tab === 'documentos'" x-transition>
                <div class="p-6 space-y-8 bg-white rounded-lg shadow ring-1 ring-black/5">
                    {{-- Documentos Pessoais --}}
                    <div>
                        <h2 class="pb-3 mb-5 text-base font-semibold text-gray-900 border-b border-gray-100">Documentos
                            Pessoais</h2>
                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">CPF <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="cpf" value="{{ old('cpf') }}"
                                    placeholder="000.000.000-00"
                                    class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">RG</label>
                                <input type="text" name="rg" value="{{ old('rg') }}" placeholder="0000000"
                                    class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Órgão Expedidor RG</label>
                                <input type="text" name="orgao_expedidor_rg" value="{{ old('orgao_expedidor_rg') }}"
                                    placeholder="SSP/RO"
                                    class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Título de Eleitor</label>
                                <input type="text" name="titulo_eleitor" value="{{ old('titulo_eleitor') }}"
                                    class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Zona Eleitoral</label>
                                <input type="text" name="zona_eleitoral" value="{{ old('zona_eleitoral') }}"
                                    class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Seção Eleitoral</label>
                                <input type="text" name="secao_eleitoral" value="{{ old('secao_eleitoral') }}"
                                    class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            </div>
                        </div>
                    </div>

                    {{-- CTPS --}}
                    <div>
                        <h2 class="pb-3 mb-5 text-base font-semibold text-gray-900 border-b border-gray-100">Carteira de
                            Trabalho (CTPS)</h2>
                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Número CTPS</label>
                                <input type="text" name="ctps_numero" value="{{ old('ctps_numero') }}"
                                    class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Série</label>
                                <input type="text" name="ctps_serie" value="{{ old('ctps_serie') }}"
                                    class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">UF CTPS</label>
                                <select name="ctps_uf"
                                    class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">UF</option>
                                    @foreach (['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'] as $uf)
                                        <option value="{{ $uf }}" @selected(old('ctps_uf') === $uf)>
                                            {{ $uf }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Data de Emissão</label>
                                <input type="date" name="ctps_data_emissao" value="{{ old('ctps_data_emissao') }}"
                                    class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            </div>
                        </div>
                    </div>

                    {{-- Outros --}}
                    <div>
                        <h2 class="pb-3 mb-5 text-base font-semibold text-gray-900 border-b border-gray-100">Outros
                            Documentos</h2>
                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">PIS/PASEP</label>
                                <input type="text" name="pis_pasep" value="{{ old('pis_pasep') }}"
                                    class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Certificado Reservista</label>
                                <input type="text" name="certificado_reservista"
                                    value="{{ old('certificado_reservista') }}"
                                    class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- ABA 5: CONTRATO & SALÁRIO (tabela funcionario_contratos) --}}
            {{-- ============================================================ --}}
            <div x-show="tab === 'contrato'" x-transition>
                <div class="p-6 space-y-8 bg-white rounded-lg shadow ring-1 ring-black/5">
                    {{-- Vínculo --}}
                    <div>
                        <h2 class="pb-3 mb-5 text-base font-semibold text-gray-900 border-b border-gray-100">Vínculo
                            Empregatício</h2>
                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Departamento <span
                                        class="text-red-500">*</span></label>
                                <select name="departamento_id"
                                    class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required>
                                    <option value="">Selecione...</option>
                                    @foreach ($departamentos as $dep)
                                        <option value="{{ $dep->id }}" @selected(old('departamento_id') == $dep->id)>
                                            {{ $dep->nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Cargo <span
                                        class="text-red-500">*</span></label>
                                <select name="cargo_id"
                                    class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required>
                                    <option value="">Selecione...</option>
                                    @foreach ($cargos as $cargo)
                                        <option value="{{ $cargo->id }}" @selected(old('cargo_id') == $cargo->id)>
                                            {{ $cargo->titulo }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Local de Trabalho</label>
                                <input type="text" name="local_trabalho" value="{{ old('local_trabalho') }}"
                                    class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tipo de Contratação (Regime)</label>
                                <select name="tipo_contratacao"
                                    class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Selecione...</option>
                                    @foreach (['clt' => 'CLT', 'pj' => 'PJ', 'autonomo' => 'Autônomo', 'avulso' => 'Avulso', 'estatutario' => 'Estatutário'] as $val => $lbl)
                                        <option value="{{ $val }}" @selected(old('tipo_contratacao', 'clt') === $val)>
                                            {{ $lbl }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tipo de Contrato (Prazo)</label>
                                <select name="tipo_contrato"
                                    class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Selecione...</option>
                                    @foreach (['indeterminado' => 'Indeterminado', 'determinado' => 'Determinado', 'experiencia' => 'Experiência', 'intermitente' => 'Intermitente', 'temporario' => 'Temporário', 'aprendiz' => 'Aprendiz', 'estagio' => 'Estágio'] as $val => $lbl)
                                        <option value="{{ $val }}" @selected(old('tipo_contrato', 'indeterminado') === $val)>
                                            {{ $lbl }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Data de Admissão <span
                                        class="text-red-500">*</span></label>
                                <input type="date" name="data_admissao" value="{{ old('data_admissao') }}"
                                    class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required />
                            </div>
                        </div>
                    </div>

                    {{-- Remuneração --}}
                    {{-- <div>
                        <h2 class="pb-3 mb-5 text-base font-semibold text-gray-900 border-b border-gray-100">Remuneração
                        </h2>
                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Salário Base <span
                                        class="text-red-500">*</span></label>
                                <div class="relative mt-1">
                                    <span
                                        class="absolute inset-y-0 left-0 flex items-center pl-3 text-sm text-gray-500 pointer-events-none">R$</span>
                                    <input type="number" name="salario_base" step="0.01" min="0"
                                        value="{{ old('salario_base') }}" placeholder="0,00"
                                        class="block w-full text-sm border-gray-300 rounded-md shadow-sm pl-9 focus:border-indigo-500 focus:ring-indigo-500"
                                        required />
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Carga Horária Semanal (h)</label>
                                <input type="number" name="carga_horaria_semanal" min="1" max="44"
                                    value="{{ old('carga_horaria_semanal', 44) }}"
                                    class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Qtd. Dependentes Salário
                                    Família</label>
                                <input type="number" name="qtd_dependentes_salario_familia" min="0"
                                    value="{{ old('qtd_dependentes_salario_familia', 0) }}"
                                    class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            </div>
                        </div>
                    </div> --}}
                    {{-- Remuneração --}}
                    @php
                        $tipoRemuneracaoOld = old(
                            'tipo_remuneracao',
                            $funcionario->contrato?->tipo_remuneracao ?? 'mensal',
                        );
                    @endphp

                    <div x-data="{
                        tipoRemuneracao: '{{ $tipoRemuneracaoOld }}',
                        sync() {
                            // Quando troca o tipo, limpamos os campos que não se aplicam para evitar salvar lixo
                            if (this.tipoRemuneracao === 'mensal') {
                                this.$refs.valorDiaria && (this.$refs.valorDiaria.value = '');
                                this.$refs.valorHora && (this.$refs.valorHora.value = '');
                            } else if (this.tipoRemuneracao === 'diaria') {
                                this.$refs.salarioBase && (this.$refs.salarioBase.value = '');
                                this.$refs.valorHora && (this.$refs.valorHora.value = '');
                            } else if (this.tipoRemuneracao === 'horaria') {
                                this.$refs.salarioBase && (this.$refs.salarioBase.value = '');
                                this.$refs.valorDiaria && (this.$refs.valorDiaria.value = '');
                            }
                        }
                    }" x-init="sync()">
                        <h2 class="pb-3 mb-5 text-base font-semibold text-gray-900 border-b">Remuneração</h2>

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                            {{-- Tipo de remuneração --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tipo de Remuneração <span
                                        class="text-red-500">*</span></label>
                                <select name="tipo_remuneracao" x-model="tipoRemuneracao" @change="sync()"
                                    class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required>
                                    <option value="mensal">Mensal</option>
                                    <option value="diaria">Diária</option>
                                    <option value="horaria">Horária</option>
                                </select>
                                @error('tipo_remuneracao')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Salário Base (mensal) --}}
                            <div x-show="tipoRemuneracao === 'mensal'" x-transition>
                                <label class="block text-sm font-medium text-gray-700">Salário Base <span
                                        class="text-red-500">*</span></label>
                                <div class="relative mt-1">
                                    <span
                                        class="absolute inset-y-0 left-0 flex items-center pl-3 text-sm text-gray-500 pointer-events-none">R$</span>
                                    <input x-ref="salarioBase" type="number" name="salario_base" step="0.01"
                                        min="0" inputmode="decimal"
                                        value="{{ old('salario_base', $funcionario?->contrato?->salario_base ?? 0) }}"
                                        class="block w-full text-sm border-gray-300 rounded-md shadow-sm pl-9 focus:border-indigo-500 focus:ring-indigo-500"
                                        :required="tipoRemuneracao === 'mensal'" />
                                </div>
                                @error('salario_base')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Valor da Diária --}}
                            <div x-show="tipoRemuneracao === 'diaria'" x-transition>
                                <label class="block text-sm font-medium text-gray-700">Valor da Diária <span
                                        class="text-red-500">*</span></label>
                                <div class="relative mt-1">
                                    <span
                                        class="absolute inset-y-0 left-0 flex items-center pl-3 text-sm text-gray-500 pointer-events-none">R$</span>
                                    <input x-ref="valorDiaria" type="number" name="valor_diaria" step="0.01"
                                        min="0" inputmode="decimal"
                                        value="{{ old('valor_diaria', $funcionario?->contrato?->valor_diaria ?? 0) }}"
                                        class="block w-full text-sm border-gray-300 rounded-md shadow-sm pl-9 focus:border-indigo-500 focus:ring-indigo-500"
                                        :required="tipoRemuneracao === 'diaria'" />
                                </div>

                                <div class="flex items-center gap-3 mt-3">
                                    <input type="hidden" name="eh_diarista" value="0">
                                    <input id="eh_diarista" type="checkbox" name="eh_diarista" value="1"
                                        @checked(old('eh_diarista', $funcionario?->contrato?->eh_diarista ?? 0))
                                        class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" />
                                    <label for="eh_diarista" class="text-sm font-medium text-gray-700">É diarista</label>
                                </div>

                                @error('valor_diaria')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Valor da Hora --}}
                            <div x-show="tipoRemuneracao === 'horaria'" x-transition>
                                <label class="block text-sm font-medium text-gray-700">Valor da Hora <span
                                        class="text-red-500">*</span></label>
                                <div class="relative mt-1">
                                    <span
                                        class="absolute inset-y-0 left-0 flex items-center pl-3 text-sm text-gray-500 pointer-events-none">R$</span>
                                    <input x-ref="valorHora" type="number" name="valor_hora" step="0.01"
                                        min="0" inputmode="decimal"
                                        value="{{ old('valor_hora', $funcionario?->contrato?->valor_hora ?? 0) }}"
                                        class="block w-full text-sm border-gray-300 rounded-md shadow-sm pl-9 focus:border-indigo-500 focus:ring-indigo-500"
                                        :required="tipoRemuneracao === 'horaria'" />
                                </div>
                                @error('valor_hora')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Carga horária --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Carga Horária Semanal (h)</label>
                                <input type="number" name="carga_horaria_semanal" min="1" max="44"
                                    value="{{ old('carga_horaria_semanal', $funcionario?->contrato?->carga_horaria_semanal ?? 44) }}"
                                    class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                @error('carga_horaria_semanal')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Dependentes salário família (isso na real está no contrato no seu schema) --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Qtd. Dependentes Salário
                                    Família</label>
                                <input type="number" name="qtd_dependentes_salario_familia" min="0"
                                    value="{{ old('qtd_dependentes_salario_familia', $funcionario->contrato?->qtd_dependentes_salario_familia ?? 0) }}"
                                    class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                @error('qtd_dependentes_salario_familia')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Dependentes IR (você valida no Request mas não tinha no bloco) --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Qtd. Dependentes para IR</label>
                                <input type="number" name="qtd_dependentes_ir" min="0"
                                    value="{{ old('qtd_dependentes_ir', $funcionario->contrato?->qtd_dependentes_ir ?? 0) }}"
                                    class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                @error('qtd_dependentes_ir')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Horários --}}
                    <div>
                        <h2 class="pb-3 mb-5 text-base font-semibold text-gray-900 border-b border-gray-100">Horário de
                            Trabalho</h2>
                        <div class="grid grid-cols-2 gap-5 sm:grid-cols-4">
                            @foreach ([['horario_entrada', 'Entrada', '08:00'], ['horario_saida', 'Saída', '17:00'], ['horario_almoco_inicio', 'Início Almoço', '12:00'], ['horario_almoco_fim', 'Fim Almoço', '13:00']] as [$campo, $label, $default])
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">{{ $label }}</label>
                                    <input type="time" name="{{ $campo }}"
                                        value="{{ old($campo, $default) }}"
                                        class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- ABA 6: BENEFÍCIOS (tabela funcionario_beneficios) --}}
            {{-- ============================================================ --}}
            <div x-show="tab === 'beneficios'" x-transition>
                <div class="p-6 space-y-6 bg-white rounded-lg shadow ring-1 ring-black/5">
                    <h2 class="pb-3 text-base font-semibold text-gray-900 border-b border-gray-100">Benefícios</h2>
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        {{-- Vale Transporte --}}
                        <div class="p-4 space-y-3 border border-gray-200 rounded-lg" x-data="{ ativo: {{ old('vale_transporte') ? 'true' : 'false' }} }">
                            <div class="flex items-center gap-3">
                                <input type="hidden" name="vale_transporte" value="0">
                                <input id="vale_transporte" type="checkbox" name="vale_transporte" value="1"
                                    x-model="ativo"
                                    class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" />
                                <label for="vale_transporte" class="text-sm font-semibold text-gray-700">Vale
                                    Transporte</label>
                            </div>
                            <div x-show="ativo" x-transition>
                                <label class="block text-sm font-medium text-gray-700">Valor mensal</label>
                                <div class="relative mt-1">
                                    <span
                                        class="absolute inset-y-0 left-0 flex items-center pl-3 text-sm text-gray-500 pointer-events-none">R$</span>
                                    <input type="number" name="valor_vale_transporte" step="0.01" min="0"
                                        value="{{ old('valor_vale_transporte', '0.00') }}"
                                        class="block w-full text-sm border-gray-300 rounded-md shadow-sm pl-9 focus:border-indigo-500 focus:ring-indigo-500" />
                                </div>
                            </div>
                        </div>

                        {{-- Vale Alimentação --}}
                        <div class="p-4 space-y-3 border border-gray-200 rounded-lg" x-data="{ ativo: {{ old('vale_alimentacao') ? 'true' : 'false' }} }">
                            <div class="flex items-center gap-3">
                                <input type="hidden" name="vale_alimentacao" value="0">
                                <input id="vale_alimentacao" type="checkbox" name="vale_alimentacao" value="1"
                                    x-model="ativo"
                                    class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" />
                                <label for="vale_alimentacao" class="text-sm font-semibold text-gray-700">Vale
                                    Alimentação</label>
                            </div>
                            <div x-show="ativo" x-transition>
                                <label class="block text-sm font-medium text-gray-700">Valor mensal</label>
                                <div class="relative mt-1">
                                    <span
                                        class="absolute inset-y-0 left-0 flex items-center pl-3 text-sm text-gray-500 pointer-events-none">R$</span>
                                    <input type="number" name="valor_vale_alimentacao" step="0.01" min="0"
                                        value="{{ old('valor_vale_alimentacao', '0.00') }}"
                                        class="block w-full text-sm border-gray-300 rounded-md shadow-sm pl-9 focus:border-indigo-500 focus:ring-indigo-500" />
                                </div>
                            </div>
                        </div>

                        {{-- Planos --}}
                        <div class="p-4 space-y-4 border border-gray-200 rounded-lg">
                            <p class="text-sm font-semibold text-gray-700">Planos de Saúde</p>
                            <div class="flex items-center gap-3">
                                <input type="hidden" name="plano_saude" value="0">
                                <input id="plano_saude" type="checkbox" name="plano_saude" value="1"
                                    @checked(old('plano_saude'))
                                    class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" />
                                <label for="plano_saude" class="text-sm text-gray-700">Plano de Saúde</label>
                            </div>
                            <div class="flex items-center gap-3">
                                <input type="hidden" name="plano_odontologico" value="0">
                                <input id="plano_odontologico" type="checkbox" name="plano_odontologico" value="1"
                                    @checked(old('plano_odontologico'))
                                    class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" />
                                <label for="plano_odontologico" class="text-sm text-gray-700">Plano Odontológico</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- ABA 7: DADOS BANCÁRIOS (tabela funcionario_dados_bancarios) --}}
            {{-- ============================================================ --}}
            <div x-show="tab === 'bancario'" x-transition>
                <div class="p-6 space-y-6 bg-white rounded-lg shadow ring-1 ring-black/5">
                    <h2 class="pb-3 text-base font-semibold text-gray-900 border-b border-gray-100">Dados Bancários</h2>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Código do Banco</label>
                            <input type="text" name="banco_codigo" value="{{ old('banco_codigo') }}"
                                placeholder="001"
                                class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>
                        <div class="lg:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Nome do Banco</label>
                            <input type="text" name="banco_nome" value="{{ old('banco_nome') }}"
                                placeholder="Banco do Brasil"
                                class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Agência</label>
                            <input type="text" name="agencia" value="{{ old('agencia') }}" placeholder="0000-0"
                                class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Conta</label>
                            <input type="text" name="conta" value="{{ old('conta') }}" placeholder="00000-0"
                                class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tipo de Conta</label>
                            <select name="tipo_conta"
                                class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Selecione...</option>
                                <option value="corrente" @selected(old('tipo_conta') === 'corrente')>Corrente</option>
                                <option value="poupanca" @selected(old('tipo_conta') === 'poupanca')>Poupança</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- ABA 8: DEPENDENTES --}}
            {{-- ============================================================ --}}
            <div x-show="tab === 'dependentes'" x-transition>
                <div class="p-6 space-y-6 bg-white rounded-lg shadow ring-1 ring-black/5">
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                        <h2 class="text-base font-semibold text-gray-900">Dependentes</h2>
                        <span class="text-sm text-gray-500">Para salário família e imposto de renda</span>
                    </div>

                    <div x-data="dependentesForm()" class="space-y-4">
                        <template x-for="(dep, index) in dependentes" :key="index">
                            <div class="relative p-4 border border-gray-200 rounded-lg">
                                <button type="button" @click="removerDependente(index)"
                                    class="absolute text-gray-400 top-2 right-2 hover:text-red-500"
                                    x-show="dependentes.length > 0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700">Nome Completo</label>
                                        <input type="text" :name="'dependentes[' + index + '][nome_completo]'"
                                            x-model="dep.nome_completo"
                                            class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700">Data Nascimento</label>
                                        <input type="date" :name="'dependentes[' + index + '][data_nascimento]'"
                                            x-model="dep.data_nascimento"
                                            class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700">Parentesco</label>
                                        <select :name="'dependentes[' + index + '][parentesco]'" x-model="dep.parentesco"
                                            class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="">Selecione...</option>
                                            <option value="filho(a)">Filho(a)</option>
                                            <option value="enteado(a)">Enteado(a)</option>
                                            <option value="tutelado(a)">Tutelado(a)</option>
                                            <option value="outro">Outro</option>
                                        </select>
                                    </div>
                                    <div class="flex items-center gap-3 sm:pt-6">
                                        <input type="hidden" :name="'dependentes[' + index + '][invalido]'"
                                            value="0">
                                        <input type="checkbox" :name="'dependentes[' + index + '][invalido]'"
                                            value="1" x-model="dep.invalido"
                                            class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" />
                                        <label class="text-xs text-gray-700">Inválido</label>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <button type="button" @click="adicionarDependente"
                            class="inline-flex items-center gap-2 text-sm font-medium text-indigo-600 hover:text-indigo-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Adicionar Dependente
                        </button>
                    </div>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- BARRA DE AÇÕES --}}
            {{-- ============================================================ --}}
            <div
                class="flex items-center justify-between gap-3 px-6 py-4 mt-6 bg-white rounded-lg shadow ring-1 ring-black/5">
                <a href="{{ route('rh.funcionarios.index') }}"
                    class="inline-flex items-center gap-1.5 rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancelar
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold text-white bg-indigo-600 rounded-md shadow-sm hover:bg-indigo-500">
                    💾 Cadastrar Funcionário
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        function dependentesForm() {
            return {
                dependentes: [],

                adicionarDependente() {
                    this.dependentes.push({
                        nome_completo: '',
                        data_nascimento: '',
                        parentesco: '',
                        invalido: false
                    });
                },

                removerDependente(index) {
                    this.dependentes.splice(index, 1);
                }
            }
        }
    </script>
@endpush
