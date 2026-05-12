@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6" x-data="{ tab: 'pessoal' }">

        {{-- Cabeçalho --}}
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-sm text-gray-500 mb-1">
                    <a href="{{ route('rh.funcionarios.index') }}" class="hover:text-indigo-600">Funcionários</a>
                    <span>/</span>
                    <span class="text-gray-700 font-medium">{{ $funcionario->nome_completo }}</span>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">Editar Funcionário</h1>
            </div>

            <div class="flex items-center gap-3">
                {{-- Badge Tempo de Empresa --}}
                <span
                    class="hidden sm:inline-flex items-center gap-1 rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-100">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-13a.75.75 0 00-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 000-1.5h-3.25V5z"
                            clip-rule="evenodd" />
                    </svg>
                    {{ $funcionario->tempo_empresa }}
                </span>

                {{-- Badge Status --}}
                @if ($funcionario->ativo)
                    <span
                        class="inline-flex items-center rounded-full bg-green-50 px-3 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Ativo</span>
                @else
                    <span
                        class="inline-flex items-center rounded-full bg-red-50 px-3 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/20">Inativo</span>
                @endif

                <a href="{{ route('rh.funcionarios.index') }}"
                    class="inline-flex items-center gap-1.5 rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M17 10a.75.75 0 01-.75.75H5.612l4.158 3.96a.75.75 0 11-1.04 1.08l-5.5-5.25a.75.75 0 010-1.08l5.5-5.25a.75.75 0 111.04 1.08L5.612 9.25H16.25A.75.75 0 0117 10z"
                            clip-rule="evenodd" />
                    </svg>
                    Voltar
                </a>
            </div>
        </div>

        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="mb-4 rounded-md border border-green-200 bg-green-50 p-4 text-sm text-green-800">
                {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="mb-4 rounded-md border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                <strong class="font-medium">Corrija os erros abaixo:</strong>
                <ul class="mt-1 list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('rh.funcionarios.update', $funcionario) }}">
            @csrf
            @method('PUT')

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
            ['key' => 'ferias', 'label' => 'Férias', 'icon' => 'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5'],
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
                    <h2 class="text-base font-semibold text-gray-900 border-b pb-3">Informações Pessoais</h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                        {{-- Nome Completo --}}
                        <div class="lg:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Nome Completo <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="nome_completo"
                                value="{{ old('nome_completo', $funcionario->nome_completo) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                required />
                            @error('nome_completo')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- CPF --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">CPF <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="cpf" value="{{ old('cpf', $funcionario->cpf) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                placeholder="000.000.000-00" required />
                            @error('cpf')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- RG --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">RG</label>
                            <input type="text" name="rg" value="{{ old('rg', $funcionario->rg) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                            @error('rg')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Órgão Expedidor RG --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Órgão Expedidor RG</label>
                            <input type="text" name="orgao_expedidor_rg"
                                value="{{ old('orgao_expedidor_rg', $funcionario->orgao_expedidor_rg) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                placeholder="SSP/RO" />
                            @error('orgao_expedidor_rg')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Data de Nascimento --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Data de Nascimento</label>
                            <input type="date" name="data_nascimento"
                                value="{{ old('data_nascimento', $funcionario->data_nascimento?->format('Y-m-d')) }}"
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
                                @foreach (['solteiro' => 'Solteiro(a)', 'casado' => 'Casado(a)', 'divorciado' => 'Divorciado(a)', 'viuvo' => 'Viúvo(a)', 'uniao_estavel' => 'União Estável'] as $val => $label)
                                    <option value="{{ $val }}" @selected(old('estado_civil', $funcionario->estado_civil) === $val)>{{ $label }}
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
                                <option value="masculino" @selected(old('genero', $funcionario->genero) === 'masculino')>Masculino</option>
                                <option value="feminino" @selected(old('genero', $funcionario->genero) === 'feminino')>Feminino</option>
                                <option value="outro" @selected(old('genero', $funcionario->genero) === 'outro')>Outro</option>
                            </select>
                            @error('genero')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Nacionalidade --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nacionalidade</label>
                            <input type="text" name="nacionalidade"
                                value="{{ old('nacionalidade', $funcionario->nacionalidade) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                placeholder="Brasileiro(a)" />
                            @error('nacionalidade')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Naturalidade --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Naturalidade</label>
                            <input type="text" name="naturalidade"
                                value="{{ old('naturalidade', $funcionario->naturalidade) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                placeholder="Porto Velho/RO" />
                            @error('naturalidade')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Telefone --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Telefone</label>
                            <input type="text" name="telefone" value="{{ old('telefone', $funcionario->telefone) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                placeholder="(69) 3000-0000" />
                            @error('telefone')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Celular --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Celular</label>
                            <input type="text" name="celular" value="{{ old('celular', $funcionario->celular) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                placeholder="(69) 9 9000-0000" />
                            @error('celular')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- E-mail Corporativo --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">E-mail Corporativo</label>
                            <input type="email" name="email" value="{{ old('email', $funcionario->email) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                            @error('email')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- E-mail Pessoal --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">E-mail Pessoal</label>
                            <input type="email" name="email_pessoal"
                                value="{{ old('email_pessoal', $funcionario->email_pessoal) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                            @error('email_pessoal')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Status Ativo --}}
                        <div class="flex items-center gap-3 pt-6">
                            <input type="hidden" name="ativo" value="0">
                            <input id="ativo" type="checkbox" name="ativo" value="1"
                                @checked(old('ativo', $funcionario->ativo))
                                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                            <label for="ativo" class="text-sm font-medium text-gray-700">Funcionário ativo</label>
                        </div>
                    </div>

                    {{-- Observações --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Observações</label>
                        <textarea name="observacoes" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">{{ old('observacoes', $funcionario->observacoes) }}</textarea>
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
                    <h2 class="text-base font-semibold text-gray-900 border-b pb-3">Endereço Residencial</h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">CEP</label>
                            <input type="text" name="cep" value="{{ old('cep', $funcionario->cep) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                placeholder="00000-000" />
                            @error('cep')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="lg:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Logradouro</label>
                            <input type="text" name="logradouro"
                                value="{{ old('logradouro', $funcionario->logradouro) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                            @error('logradouro')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Número</label>
                            <input type="text" name="numero" value="{{ old('numero', $funcionario->numero) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                            @error('numero')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Complemento</label>
                            <input type="text" name="complemento"
                                value="{{ old('complemento', $funcionario->complemento) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                placeholder="Apto, Bloco..." />
                            @error('complemento')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Bairro</label>
                            <input type="text" name="bairro" value="{{ old('bairro', $funcionario->bairro) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                            @error('bairro')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Cidade</label>
                            <input type="text" name="cidade" value="{{ old('cidade', $funcionario->cidade) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                            @error('cidade')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Estado (UF)</label>
                            <select name="estado"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <option value="">UF</option>
                                @foreach (['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'] as $uf)
                                    <option value="{{ $uf }}" @selected(old('estado', $funcionario->estado) === $uf)>{{ $uf }}
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
                        <h2 class="text-base font-semibold text-gray-900 border-b pb-3 mb-5">CTPS</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Número CTPS</label>
                                <input type="text" name="ctps_numero"
                                    value="{{ old('ctps_numero', $funcionario->ctps_numero) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                                @error('ctps_numero')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Série</label>
                                <input type="text" name="ctps_serie"
                                    value="{{ old('ctps_serie', $funcionario->ctps_serie) }}"
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
                                        <option value="{{ $uf }}" @selected(old('ctps_uf', $funcionario->ctps_uf) === $uf)>
                                            {{ $uf }}</option>
                                    @endforeach
                                </select>
                                @error('ctps_uf')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Data de Emissão</label>
                                <input type="date" name="ctps_data_emissao"
                                    value="{{ old('ctps_data_emissao', $funcionario->ctps_data_emissao?->format('Y-m-d')) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                                @error('ctps_data_emissao')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Outros Documentos --}}
                    <div>
                        <h2 class="text-base font-semibold text-gray-900 border-b pb-3 mb-5">Outros Documentos</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">PIS/PASEP</label>
                                <input type="text" name="pis_pasep"
                                    value="{{ old('pis_pasep', $funcionario->pis_pasep) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                                @error('pis_pasep')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Título de Eleitor</label>
                                <input type="text" name="titulo_eleitor"
                                    value="{{ old('titulo_eleitor', $funcionario->titulo_eleitor) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                                @error('titulo_eleitor')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Zona Eleitoral</label>
                                <input type="text" name="zona_eleitoral"
                                    value="{{ old('zona_eleitoral', $funcionario->zona_eleitoral) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                                @error('zona_eleitoral')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Seção Eleitoral</label>
                                <input type="text" name="secao_eleitoral"
                                    value="{{ old('secao_eleitoral', $funcionario->secao_eleitoral) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                                @error('secao_eleitoral')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Certificado Reservista</label>
                                <input type="text" name="certificado_reservista"
                                    value="{{ old('certificado_reservista', $funcionario->certificado_reservista) }}"
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
                        <h2 class="text-base font-semibold text-gray-900 border-b pb-3 mb-5">Vínculo Empregatício</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Departamento</label>
                                <select name="departamento_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    <option value="">Selecione...</option>
                                    @foreach ($departamentos as $dep)
                                        <option value="{{ $dep->id }}" @selected(old('departamento_id', $funcionario->departamento_id) == $dep->id)>
                                            {{ $dep->nome }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('departamento_id')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Cargo</label>
                                <select name="cargo_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    <option value="">Selecione...</option>
                                    @foreach ($cargos as $cargo)
                                        <option value="{{ $cargo->id }}" @selected(old('cargo_id', $funcionario->cargo_id) == $cargo->id)>
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
                                <input type="text" name="local_trabalho"
                                    value="{{ old('local_trabalho', $funcionario->local_trabalho) }}"
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
                                    @foreach (['clt' => 'CLT', 'pj' => 'PJ', 'estagio' => 'Estágio', 'temporario' => 'Temporário', 'autonomo' => 'Autônomo'] as $val => $lbl)
                                        <option value="{{ $val }}" @selected(old('tipo_contrato', $funcionario->tipo_contrato) === $val)>
                                            {{ $lbl }}</option>
                                    @endforeach
                                </select>
                                @error('tipo_contrato')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tipo de Contratação</label>
                                <input type="text" name="tipo_contratacao"
                                    value="{{ old('tipo_contratacao', $funcionario->tipo_contratacao) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                                @error('tipo_contratacao')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Data de Admissão</label>
                                <input type="date" name="data_admissao"
                                    value="{{ old('data_admissao', $funcionario->data_admissao?->format('Y-m-d')) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                                @error('data_admissao')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Data de Demissão</label>
                                <input type="date" name="data_demissao"
                                    value="{{ old('data_demissao', $funcionario->data_demissao?->format('Y-m-d')) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                                @error('data_demissao')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Salário --}}
                    <div>
                        <h2 class="text-base font-semibold text-gray-900 border-b pb-3 mb-5">Remuneração</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Salário Base <span
                                        class="text-red-500">*</span></label>
                                <div class="relative mt-1">
                                    <span
                                        class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 text-sm">R$</span>
                                    <input type="number" name="salario_base" step="0.01" min="0"
                                        value="{{ old('salario_base', $funcionario->salario_base) }}"
                                        class="block w-full rounded-md border-gray-300 pl-9 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                        required />
                                </div>
                                @error('salario_base')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Carga Horária Semanal (h)</label>
                                <input type="number" name="carga_horaria_semanal" min="0" max="44"
                                    value="{{ old('carga_horaria_semanal', $funcionario->carga_horaria_semanal) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                                @error('carga_horaria_semanal')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Qtd. Dependentes IR</label>
                                <input type="number" name="qtd_dependentes_ir" min="0"
                                    value="{{ old('qtd_dependentes_ir', $funcionario->qtd_dependentes_ir) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                                @error('qtd_dependentes_ir')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Qtd. Dependentes Salário
                                    Família</label>
                                <input type="number" name="qtd_dependentes_salario_familia" min="0"
                                    value="{{ old('qtd_dependentes_salario_familia', $funcionario->qtd_dependentes_salario_familia) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                                @error('qtd_dependentes_salario_familia')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Horários --}}
                    <div>
                        <h2 class="text-base font-semibold text-gray-900 border-b pb-3 mb-5">Horário de Trabalho</h2>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-5">
                            @foreach ([['horario_entrada', 'Entrada'], ['horario_saida', 'Saída'], ['horario_almoco_inicio', 'Início Almoço'], ['horario_almoco_fim', 'Fim Almoço']] as [$campo, $label])
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">{{ $label }}</label>
                                    <input type="time" name="{{ $campo }}"
                                        value="{{ old($campo, $funcionario->$campo->format('H:i')) }}" step="60"
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
                        <h2 class="text-base font-semibold text-gray-900 border-b pb-3 mb-5">Modalidade Diarista</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                            <div class="flex items-center gap-3">
                                <input type="hidden" name="eh_diarista" value="0">
                                <input id="eh_diarista" type="checkbox" name="eh_diarista" value="1"
                                    @checked(old('eh_diarista', $funcionario->eh_diarista))
                                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                                <label for="eh_diarista" class="text-sm font-medium text-gray-700">É diarista?</label>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Valor da Diária</label>
                                <div class="relative mt-1">
                                    <span
                                        class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 text-sm">R$</span>
                                    <input type="number" name="valor_diaria" step="0.01" min="0"
                                        value="{{ old('valor_diaria', $funcionario->valor_diaria) }}"
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
                    <h2 class="text-base font-semibold text-gray-900 border-b pb-3">Benefícios</h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

                        {{-- Vale Transporte --}}
                        <div class="rounded-lg border border-gray-200 p-4 space-y-3">
                            <div class="flex items-center gap-3">
                                <input type="hidden" name="vale_transporte" value="0">
                                <input id="vale_transporte" type="checkbox" name="vale_transporte" value="1"
                                    @checked(old('vale_transporte', $funcionario->vale_transporte))
                                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                                <label for="vale_transporte" class="text-sm font-semibold text-gray-700">Vale
                                    Transporte</label>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Valor</label>
                                <div class="relative mt-1">
                                    <span
                                        class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 text-sm">R$</span>
                                    <input type="number" name="valor_vale_transporte" step="0.01" min="0"
                                        value="{{ old('valor_vale_transporte', $funcionario->valor_vale_transporte) }}"
                                        class="block w-full rounded-md border-gray-300 pl-9 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                                </div>
                                @error('valor_vale_transporte')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Vale Alimentação --}}
                        <div class="rounded-lg border border-gray-200 p-4 space-y-3">
                            <div class="flex items-center gap-3">
                                <input type="hidden" name="vale_alimentacao" value="0">
                                <input id="vale_alimentacao" type="checkbox" name="vale_alimentacao" value="1"
                                    @checked(old('vale_alimentacao', $funcionario->vale_alimentacao))
                                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                                <label for="vale_alimentacao" class="text-sm font-semibold text-gray-700">Vale
                                    Alimentação</label>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Valor</label>
                                <div class="relative mt-1">
                                    <span
                                        class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 text-sm">R$</span>
                                    <input type="number" name="valor_vale_alimentacao" step="0.01" min="0"
                                        value="{{ old('valor_vale_alimentacao', $funcionario->valor_vale_alimentacao) }}"
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
                                        value="{{ old('vale_extra', $funcionario->vale_extra) }}"
                                        class="block w-full rounded-md border-gray-300 pl-9 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                                </div>
                                @error('vale_extra')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Plano de Saúde e Odontológico --}}
                        <div class="rounded-lg border border-gray-200 p-4 space-y-4">
                            <p class="text-sm font-semibold text-gray-700">Planos</p>
                            <div class="flex items-center gap-3">
                                <input type="hidden" name="plano_saude" value="0">
                                <input id="plano_saude" type="checkbox" name="plano_saude" value="1"
                                    @checked(old('plano_saude', $funcionario->plano_saude))
                                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                                <label for="plano_saude" class="text-sm text-gray-700">Plano de Saúde</label>
                            </div>
                            <div class="flex items-center gap-3">
                                <input type="hidden" name="plano_odontologico" value="0">
                                <input id="plano_odontologico" type="checkbox" name="plano_odontologico" value="1"
                                    @checked(old('plano_odontologico', $funcionario->plano_odontologico))
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
                    <h2 class="text-base font-semibold text-gray-900 border-b pb-3">Dados Bancários</h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Código do Banco</label>
                            <input type="text" name="banco_codigo"
                                value="{{ old('banco_codigo', $funcionario->banco_codigo) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                placeholder="001" />
                            @error('banco_codigo')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="lg:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Nome do Banco</label>
                            <input type="text" name="banco_nome"
                                value="{{ old('banco_nome', $funcionario->banco_nome) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                placeholder="Banco do Brasil" />
                            @error('banco_nome')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Agência</label>
                            <input type="text" name="agencia" value="{{ old('agencia', $funcionario->agencia) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                            @error('agencia')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Conta</label>
                            <input type="text" name="conta" value="{{ old('conta', $funcionario->conta) }}"
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
                                @foreach (['corrente' => 'Corrente', 'poupanca' => 'Poupança', 'salario' => 'Conta Salário'] as $val => $lbl)
                                    <option value="{{ $val }}" @selected(old('tipo_conta', $funcionario->tipo_conta) === $val)>
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
            {{-- ABA 7: FÉRIAS --}}
            {{-- ============================================================ --}}
            <div x-show="tab === 'ferias'" x-transition>
                <div class="rounded-lg bg-white shadow ring-1 ring-black/5 p-6 space-y-6">

                    {{-- Status férias (readonly / informativo) --}}
                    <div class="rounded-lg bg-amber-50 border border-amber-200 p-4 flex items-start gap-3">
                        <svg class="h-5 w-5 text-amber-600 shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z"
                                clip-rule="evenodd" />
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-amber-800">Status das Férias</p>
                            <p class="text-sm text-amber-700 mt-0.5">{{ $funcionario->status_ferias }}</p>
                            @if ($funcionario->ferias_em_dobro)
                                <span
                                    class="mt-2 inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800">
                                    Férias em Dobro — CLT Art. 137
                                </span>
                            @endif
                        </div>
                    </div>

                    <h2 class="text-base font-semibold text-gray-900 border-b pb-3">Período Aquisitivo</h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Início do Período Aquisitivo</label>
                            <input type="date" name="periodo_aquisitivo_inicio"
                                value="{{ old('periodo_aquisitivo_inicio', $funcionario->periodo_aquisitivo_inicio?->format('Y-m-d')) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                            @error('periodo_aquisitivo_inicio')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Fim do Período Aquisitivo</label>
                            <input type="date" name="periodo_aquisitivo_fim"
                                value="{{ old('periodo_aquisitivo_fim', $funcionario->periodo_aquisitivo_fim?->format('Y-m-d')) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                            @error('periodo_aquisitivo_fim')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Vencimento das Férias</label>
                            <input type="date" name="ferias_vencimento"
                                value="{{ old('ferias_vencimento', $funcionario->ferias_vencimento?->format('Y-m-d')) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                            @error('ferias_vencimento')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex items-center gap-3 pt-4">
                            <input type="hidden" name="ferias_em_dobro" value="0">
                            <input id="ferias_em_dobro" type="checkbox" name="ferias_em_dobro" value="1"
                                @checked(old('ferias_em_dobro', $funcionario->ferias_em_dobro))
                                class="h-4 w-4 rounded border-gray-300 text-red-600 focus:ring-red-500" />
                            <label for="ferias_em_dobro" class="text-sm font-medium text-gray-700">Férias em dobro</label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- ABA 8: ADICIONAIS (Proventos, Descontos, DSR) --}}
            {{-- ============================================================ --}}
            <div x-show="tab === 'adicionais'" x-transition>
                <div class="rounded-lg bg-white shadow ring-1 ring-black/5 p-6 space-y-8">

                    {{-- Proventos --}}
                    <div>
                        <h2 class="text-base font-semibold text-gray-900 border-b pb-3 mb-5">Proventos</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                            @foreach ([['gratificacao_provento', 'Gratificação/Provento'], ['hora_extra', 'Hora Extra (R$)'], ['dsr_hora_extra', 'DSR Hora Extra (R$)'], ['salario_familia', 'Salário Família (R$)']] as [$campo, $label])
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">{{ $label }}</label>
                                    <div class="relative mt-1">
                                        <span
                                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 text-sm">R$</span>
                                        <input type="number" name="{{ $campo }}" step="0.01" min="0"
                                            value="{{ old($campo, $funcionario->$campo) }}"
                                            class="block w-full rounded-md border-gray-300 pl-9 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                                    </div>
                                    @error($campo)
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endforeach

                            {{-- Sexto Dia Útil --}}
                            <div class="flex items-center gap-3 pt-4">
                                <input type="hidden" name="sexto_dia_util_mes" value="0">
                                <input id="sexto_dia_util_mes" type="checkbox" name="sexto_dia_util_mes" value="1"
                                    @checked(old('sexto_dia_util_mes', $funcionario->sexto_dia_util_mes))
                                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                                <label for="sexto_dia_util_mes" class="text-sm font-medium text-gray-700">6º Dia Útil do
                                    Mês</label>
                            </div>
                        </div>
                    </div>

                    {{-- Descontos --}}
                    <div>
                        <h2 class="text-base font-semibold text-gray-900 border-b pb-3 mb-5">Descontos</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                            @foreach ([['desconto_inss_8_porcento', 'Desconto INSS 8%'], ['faltas', 'Faltas (dias)'], ['dsr_faltas', 'DSR Faltas (R$)'], ['desconto_faltas', 'Desconto Faltas (R$)']] as [$campo, $label])
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">{{ $label }}</label>
                                    <div class="relative mt-1">
                                        @if (!str_contains($label, 'dias'))
                                            <span
                                                class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 text-sm">R$</span>
                                        @endif
                                        <input type="number" name="{{ $campo }}" step="0.01" min="0"
                                            value="{{ old($campo, $funcionario->$campo) }}"
                                            class="block w-full rounded-md border-gray-300 {{ !str_contains($label, 'dias') ? 'pl-9' : '' }} shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                                    </div>
                                    @error($campo)
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Resumo Salarial --}}
                    <div class="rounded-lg bg-gray-50 border border-gray-200 p-5">
                        <h3 class="text-sm font-semibold text-gray-700 mb-4">Resumo Salarial (atual)</h3>
                        <dl class="grid grid-cols-3 gap-4 text-sm">
                            <div class="rounded-lg bg-green-50 border border-green-100 p-3">
                                <dt class="text-xs text-green-700 font-medium">Salário Bruto</dt>
                                <dd class="mt-1 text-lg font-bold text-green-800">
                                    R$ {{ number_format($funcionario->salario_bruto, 2, ',', '.') }}
                                </dd>
                            </div>
                            <div class="rounded-lg bg-red-50 border border-red-100 p-3">
                                <dt class="text-xs text-red-700 font-medium">Total Descontos</dt>
                                <dd class="mt-1 text-lg font-bold text-red-800">
                                    R$ {{ number_format($funcionario->total_descontos, 2, ',', '.') }}
                                </dd>
                            </div>
                            <div class="rounded-lg bg-indigo-50 border border-indigo-100 p-3">
                                <dt class="text-xs text-indigo-700 font-medium">Salário Líquido</dt>
                                <dd class="mt-1 text-lg font-bold text-indigo-800">
                                    R$ {{ number_format($funcionario->salario_liquido, 2, ',', '.') }}
                                </dd>
                            </div>
                        </dl>
                    </div>

                </div>
            </div>

            {{-- Barra de ações fixa --}}
            <div class="mt-6 flex items-center justify-end gap-3 rounded-lg bg-white px-6 py-4 shadow ring-1 ring-black/5">
                <a href="{{ route('rh.funcionarios.index') }}"
                    class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancelar
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-md bg-indigo-600 px-5 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z"
                            clip-rule="evenodd" />
                    </svg>
                    Salvar alterações
                </button>
            </div>
        </form>
    </div>
@endsection
