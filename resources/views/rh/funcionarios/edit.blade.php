@extends('layouts.app')

@section('content')
    <div class="px-4 py-6 mx-auto max-w-7xl sm:px-6 lg:px-8" x-data="{ tab: 'pessoal' }">

        {{-- Cabeçalho --}}
        <div class="flex flex-col gap-4 mb-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="flex items-center gap-2 mb-1 text-sm text-gray-500">
                    <a href="{{ route('rh.funcionarios.index') }}" class="hover:text-indigo-600">Funcionários</a>
                    <span>/</span>
                    <span class="font-medium text-gray-700">{{ $funcionario->nome_completo }}</span>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">Editar Funcionário</h1>
            </div>

            <div class="flex items-center gap-3">
                <span
                    class="items-center hidden gap-1 px-3 py-1 text-xs font-medium text-blue-700 rounded-full sm:inline-flex bg-blue-50 ring-1 ring-inset ring-blue-100">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-13a.75.75 0 00-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 000-1.5h-3.25V5z"
                            clip-rule="evenodd" />
                    </svg>
                    {{ $funcionario->tempo_empresa }}
                </span>

                @if ($funcionario->ativo)
                    <span
                        class="inline-flex items-center px-3 py-1 text-xs font-medium text-green-700 rounded-full bg-green-50 ring-1 ring-inset ring-green-600/20">Ativo</span>
                @else
                    <span
                        class="inline-flex items-center px-3 py-1 text-xs font-medium text-red-700 rounded-full bg-red-50 ring-1 ring-inset ring-red-600/20">Inativo</span>
                @endif

                <a href="{{ route('rh.funcionarios.index') }}"
                    class="inline-flex items-center gap-1.5 rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Voltar
                </a>
            </div>
        </div>

        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="p-4 mb-4 text-sm text-green-800 border border-green-200 rounded-md bg-green-50">
                {{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="p-4 mb-4 text-sm text-red-800 border border-red-200 rounded-md bg-red-50">
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
            <div class="mb-6 overflow-x-auto border-b border-gray-200">
                <nav class="flex gap-1 -mb-px min-w-max">
                    @foreach ([['key' => 'pessoal', 'label' => 'Dados Pessoais'], ['key' => 'endereco', 'label' => 'Endereço'], ['key' => 'contato', 'label' => 'Contatos'], ['key' => 'documentos', 'label' => 'Documentos'], ['key' => 'contrato', 'label' => 'Contrato & Salário'], ['key' => 'beneficios', 'label' => 'Benefícios'], ['key' => 'bancario', 'label' => 'Dados Bancários'], ['key' => 'dependentes', 'label' => 'Dependentes'], ['key' => 'ferias', 'label' => 'Férias']] as $aba)
                        <button type="button" @click="tab = '{{ $aba['key'] }}'"
                            :class="tab === '{{ $aba['key'] }}' ? 'border-indigo-600 text-indigo-700 bg-indigo-50' :
                                'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="inline-flex items-center gap-2 px-4 py-3 text-sm font-medium transition-colors border-b-2 whitespace-nowrap">
                            {{ $aba['label'] }}
                        </button>
                    @endforeach
                </nav>
            </div>

            {{-- ============================================================ --}}
            {{-- ABA 1: DADOS PESSOAIS --}}
            {{-- ============================================================ --}}
            <div x-show="tab === 'pessoal'" x-transition>
                <div class="p-6 space-y-6 bg-white rounded-lg shadow ring-1 ring-black/5">
                    <h2 class="pb-3 text-base font-semibold text-gray-900 border-b">Informações Pessoais</h2>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                        <div class="lg:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Nome Completo <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="nome_completo"
                                value="{{ old('nome_completo', $funcionario->nome_completo) }}"
                                class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Data de Nascimento</label>
                            <input type="date" name="data_nascimento"
                                value="{{ old('data_nascimento', $funcionario->data_nascimento?->format('Y-m-d')) }}"
                                class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Estado Civil</label>
                            <select name="estado_civil"
                                class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Selecione...</option>
                                @foreach (['solteiro' => 'Solteiro(a)', 'casado' => 'Casado(a)', 'divorciado' => 'Divorciado(a)', 'viuvo' => 'Viúvo(a)', 'uniao_estavel' => 'União Estável'] as $val => $label)
                                    <option value="{{ $val }}" @selected(old('estado_civil', $funcionario->estado_civil) === $val)>{{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Gênero</label>
                            <select name="genero"
                                class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Selecione...</option>
                                <option value="masculino" @selected(old('genero', $funcionario->genero) === 'masculino')>Masculino</option>
                                <option value="feminino" @selected(old('genero', $funcionario->genero) === 'feminino')>Feminino</option>
                                <option value="outro" @selected(old('genero', $funcionario->genero) === 'outro')>Outro</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nacionalidade</label>
                            <input type="text" name="nacionalidade"
                                value="{{ old('nacionalidade', $funcionario->nacionalidade) }}"
                                class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Naturalidade</label>
                            <input type="text" name="naturalidade"
                                value="{{ old('naturalidade', $funcionario->naturalidade) }}"
                                class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>
                        <div class="flex items-center gap-3 sm:pt-6">
                            <input type="hidden" name="ativo" value="0">
                            <input id="ativo" type="checkbox" name="ativo" value="1" @checked(old('ativo', $funcionario->ativo))
                                class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" />
                            <label for="ativo" class="text-sm font-medium text-gray-700">Funcionário ativo</label>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Observações</label>
                        <textarea name="observacoes" rows="3"
                            class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('observacoes', $funcionario->observacoes) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- ABA 2: ENDEREÇO (funcionario_enderecos) --}}
            {{-- ============================================================ --}}
            <div x-show="tab === 'endereco'" x-transition>
                <div class="p-6 space-y-6 bg-white rounded-lg shadow ring-1 ring-black/5">
                    <h2 class="pb-3 text-base font-semibold text-gray-900 border-b">Endereço Residencial</h2>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">CEP</label>
                            <input type="text" name="cep" value="{{ old('cep', $funcionario->endereco?->cep) }}"
                                class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>
                        <div class="lg:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Logradouro</label>
                            <input type="text" name="logradouro"
                                value="{{ old('logradouro', $funcionario->endereco?->logradouro) }}"
                                class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Número</label>
                            <input type="text" name="numero"
                                value="{{ old('numero', $funcionario->endereco?->numero) }}"
                                class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Complemento</label>
                            <input type="text" name="complemento"
                                value="{{ old('complemento', $funcionario->endereco?->complemento) }}"
                                class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Bairro</label>
                            <input type="text" name="bairro"
                                value="{{ old('bairro', $funcionario->endereco?->bairro) }}"
                                class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Cidade</label>
                            <input type="text" name="cidade"
                                value="{{ old('cidade', $funcionario->endereco?->cidade) }}"
                                class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Estado (UF)</label>
                            <select name="estado"
                                class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">UF</option>
                                @foreach (['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'] as $uf)
                                    <option value="{{ $uf }}" @selected(old('estado', $funcionario->endereco?->estado) === $uf)>{{ $uf }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- ABA 3: CONTATOS (funcionario_contatos) --}}
            {{-- ============================================================ --}}
            <div x-show="tab === 'contato'" x-transition>
                <div class="p-6 space-y-6 bg-white rounded-lg shadow ring-1 ring-black/5">
                    <h2 class="pb-3 text-base font-semibold text-gray-900 border-b">Contatos</h2>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Telefone</label>
                            <input type="text" name="telefone"
                                value="{{ old('telefone', $funcionario->contatos?->telefone) }}"
                                class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Celular</label>
                            <input type="text" name="celular"
                                value="{{ old('celular', $funcionario->contatos?->celular) }}"
                                class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">E-mail Corporativo</label>
                            <input type="email" name="email"
                                value="{{ old('email', $funcionario->contatos?->email) }}"
                                class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">E-mail Pessoal</label>
                            <input type="email" name="email_pessoal"
                                value="{{ old('email_pessoal', $funcionario->contatos?->email_pessoal) }}"
                                class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- ABA 4: DOCUMENTOS (funcionario_documentos) --}}
            {{-- ============================================================ --}}
            <div x-show="tab === 'documentos'" x-transition>
                <div class="p-6 space-y-8 bg-white rounded-lg shadow ring-1 ring-black/5">
                    <div>
                        <h2 class="pb-3 mb-5 text-base font-semibold text-gray-900 border-b">Documentos Pessoais</h2>
                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">CPF <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="cpf"
                                    value="{{ old('cpf', $funcionario->documentos?->cpf) }}"
                                    class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">RG</label>
                                <input type="text" name="rg"
                                    value="{{ old('rg', $funcionario->documentos?->rg) }}"
                                    class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Órgão Expedidor RG</label>
                                <input type="text" name="orgao_expedidor_rg"
                                    value="{{ old('orgao_expedidor_rg', $funcionario->documentos?->orgao_expedidor_rg) }}"
                                    class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Título de Eleitor</label>
                                <input type="text" name="titulo_eleitor"
                                    value="{{ old('titulo_eleitor', $funcionario->documentos?->titulo_eleitor) }}"
                                    class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Zona Eleitoral</label>
                                <input type="text" name="zona_eleitoral"
                                    value="{{ old('zona_eleitoral', $funcionario->documentos?->zona_eleitoral) }}"
                                    class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Seção Eleitoral</label>
                                <input type="text" name="secao_eleitoral"
                                    value="{{ old('secao_eleitoral', $funcionario->documentos?->secao_eleitoral) }}"
                                    class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            </div>
                        </div>
                    </div>

                    <div>
                        <h2 class="pb-3 mb-5 text-base font-semibold text-gray-900 border-b">CTPS</h2>
                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Número CTPS</label>
                                <input type="text" name="ctps_numero"
                                    value="{{ old('ctps_numero', $funcionario->documentos?->ctps_numero) }}"
                                    class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Série</label>
                                <input type="text" name="ctps_serie"
                                    value="{{ old('ctps_serie', $funcionario->documentos?->ctps_serie) }}"
                                    class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">UF CTPS</label>
                                <select name="ctps_uf"
                                    class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">UF</option>
                                    @foreach (['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'] as $uf)
                                        <option value="{{ $uf }}" @selected(old('ctps_uf', $funcionario->documentos?->ctps_uf) === $uf)>
                                            {{ $uf }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Data de Emissão</label>
                                <input type="date" name="ctps_data_emissao"
                                    value="{{ old('ctps_data_emissao', $funcionario->documentos?->ctps_data_emissao?->format('Y-m-d')) }}"
                                    class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            </div>
                        </div>
                    </div>

                    <div>
                        <h2 class="pb-3 mb-5 text-base font-semibold text-gray-900 border-b">Outros Documentos</h2>
                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">PIS/PASEP</label>
                                <input type="text" name="pis_pasep"
                                    value="{{ old('pis_pasep', $funcionario->documentos?->pis_pasep) }}"
                                    class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Certificado Reservista</label>
                                <input type="text" name="certificado_reservista"
                                    value="{{ old('certificado_reservista', $funcionario->documentos?->certificado_reservista) }}"
                                    class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- ABA 5: CONTRATO & SALÁRIO (funcionario_contratos) --}}
            {{-- ============================================================ --}}
            <div x-show="tab === 'contrato'" x-transition>
                <div class="p-6 space-y-8 bg-white rounded-lg shadow ring-1 ring-black/5">
                    <div>
                        <h2 class="pb-3 mb-5 text-base font-semibold text-gray-900 border-b">Vínculo Empregatício</h2>
                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Departamento</label>
                                <select name="departamento_id"
                                    class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Selecione...</option>
                                    @foreach ($departamentos as $dep)
                                        <option value="{{ $dep->id }}" @selected(old('departamento_id', $funcionario->departamento_id) == $dep->id)>
                                            {{ $dep->nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Cargo</label>
                                <select name="cargo_id"
                                    class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Selecione...</option>
                                    @foreach ($cargos as $cargo)
                                        <option value="{{ $cargo->id }}" @selected(old('cargo_id', $funcionario->cargo_id) == $cargo->id)>
                                            {{ $cargo->titulo }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Local de Trabalho</label>
                                <input type="text" name="local_trabalho"
                                    value="{{ old('local_trabalho', $funcionario->contrato?->local_trabalho) }}"
                                    class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tipo de Contratação (Regime)</label>
                                <select name="tipo_contratacao"
                                    class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Selecione...</option>
                                    @foreach (['clt' => 'CLT', 'pj' => 'PJ', 'autonomo' => 'Autônomo', 'avulso' => 'Avulso', 'estatutario' => 'Estatutário'] as $val => $lbl)
                                        <option value="{{ $val }}" @selected(old('tipo_contratacao', $funcionario->contrato?->tipo_contratacao) === $val)>
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
                                        <option value="{{ $val }}" @selected(old('tipo_contrato', $funcionario->contrato?->tipo_contrato) === $val)>
                                            {{ $lbl }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Data de Admissão <span
                                        class="text-red-500">*</span></label>
                                <input type="date" name="data_admissao"
                                    value="{{ old('data_admissao', $funcionario->contrato?->data_admissao?->format('Y-m-d')) }}"
                                    class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Data de Demissão</label>
                                <input type="date" name="data_demissao"
                                    value="{{ old('data_demissao', $funcionario->contrato?->data_demissao?->format('Y-m-d')) }}"
                                    class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            </div>
                        </div>
                    </div>

                    {{-- <div>
                        <h2 class="pb-3 mb-5 text-base font-semibold text-gray-900 border-b">Remuneração</h2>
                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Salário Base <span
                                        class="text-red-500">*</span></label>
                                <div class="relative mt-1">
                                    <span
                                        class="absolute inset-y-0 left-0 flex items-center pl-3 text-sm text-gray-500 pointer-events-none">R$</span>
                                    <input type="number" name="salario_base" step="0.01" min="0"
                                        value="{{ old('salario_base', $funcionario->contrato?->salario_base) }}"
                                        class="block w-full text-sm border-gray-300 rounded-md shadow-sm pl-9 focus:border-indigo-500 focus:ring-indigo-500"
                                        required />
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Carga Horária Semanal (h)</label>
                                <input type="number" name="carga_horaria_semanal" min="1" max="44"
                                    value="{{ old('carga_horaria_semanal', $funcionario->contrato?->carga_horaria_semanal ?? 44) }}"
                                    class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Qtd. Dependentes Salário
                                    Família</label>
                                <input type="number" name="qtd_dependentes_salario_familia" min="0"
                                    value="{{ old('qtd_dependentes_salario_familia', $funcionario->contrato?->qtd_dependentes_salario_familia ?? 0) }}"
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
                                        value="{{ old('salario_base', $funcionario->contrato?->salario_base) }}"
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
                                        value="{{ old('valor_diaria', $funcionario->contrato?->valor_diaria) }}"
                                        class="block w-full text-sm border-gray-300 rounded-md shadow-sm pl-9 focus:border-indigo-500 focus:ring-indigo-500"
                                        :required="tipoRemuneracao === 'diaria'" />
                                </div>

                                <div class="flex items-center gap-3 mt-3">
                                    <input type="hidden" name="eh_diarista" value="0">
                                    <input id="eh_diarista" type="checkbox" name="eh_diarista" value="1"
                                        @checked(old('eh_diarista', $funcionario->contrato?->eh_diarista))
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
                                        value="{{ old('valor_hora', $funcionario->contrato?->valor_hora) }}"
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
                                    value="{{ old('carga_horaria_semanal', $funcionario->contrato?->carga_horaria_semanal ?? 44) }}"
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

                    <div>
                        <h2 class="pb-3 mb-5 text-base font-semibold text-gray-900 border-b">Horário de Trabalho</h2>
                        <div class="grid grid-cols-2 gap-5 sm:grid-cols-4">
                            @foreach ([['horario_entrada', 'Entrada', '08:00'], ['horario_saida', 'Saída', '17:00'], ['horario_almoco_inicio', 'Início Almoço', '12:00'], ['horario_almoco_fim', 'Fim Almoço', '13:00']] as [$campo, $label, $default])
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">{{ $label }}</label>
                                    <input type="time" name="{{ $campo }}"
                                        value="{{ old($campo, $funcionario->contrato?->$campo?->format('H:i') ?? $default) }}"
                                        class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- ABA 6: BENEFÍCIOS (funcionario_beneficios) --}}
            {{-- ============================================================ --}}
            <div x-show="tab === 'beneficios'" x-transition>
                <div class="p-6 space-y-6 bg-white rounded-lg shadow ring-1 ring-black/5">
                    <h2 class="pb-3 text-base font-semibold text-gray-900 border-b">Benefícios</h2>
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div class="p-4 space-y-3 border border-gray-200 rounded-lg" x-data="{ ativo: {{ old('vale_transporte', $funcionario->beneficios?->vale_transporte) ? 'true' : 'false' }} }">
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
                                        value="{{ old('valor_vale_transporte', $funcionario->beneficios?->valor_vale_transporte) }}"
                                        class="block w-full text-sm border-gray-300 rounded-md shadow-sm pl-9 focus:border-indigo-500 focus:ring-indigo-500" />
                                </div>
                            </div>
                        </div>

                        <div class="p-4 space-y-3 border border-gray-200 rounded-lg" x-data="{ ativo: {{ old('vale_alimentacao', $funcionario->beneficios?->vale_alimentacao) ? 'true' : 'false' }} }">
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
                                        value="{{ old('valor_vale_alimentacao', $funcionario->beneficios?->valor_vale_alimentacao) }}"
                                        class="block w-full text-sm border-gray-300 rounded-md shadow-sm pl-9 focus:border-indigo-500 focus:ring-indigo-500" />
                                </div>
                            </div>
                        </div>

                        <div class="p-4 space-y-4 border border-gray-200 rounded-lg">
                            <p class="text-sm font-semibold text-gray-700">Planos de Saúde</p>
                            <div class="flex items-center gap-3">
                                <input type="hidden" name="plano_saude" value="0">
                                <input id="plano_saude" type="checkbox" name="plano_saude" value="1"
                                    @checked(old('plano_saude', $funcionario->beneficios?->plano_saude))
                                    class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" />
                                <label for="plano_saude" class="text-sm text-gray-700">Plano de Saúde</label>
                            </div>
                            <div class="flex items-center gap-3">
                                <input type="hidden" name="plano_odontologico" value="0">
                                <input id="plano_odontologico" type="checkbox" name="plano_odontologico" value="1"
                                    @checked(old('plano_odontologico', $funcionario->beneficios?->plano_odontologico))
                                    class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" />
                                <label for="plano_odontologico" class="text-sm text-gray-700">Plano Odontológico</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- ABA 7: DADOS BANCÁRIOS (funcionario_dados_bancarios) --}}
            {{-- ============================================================ --}}
            <div x-show="tab === 'bancario'" x-transition>
                <div class="p-6 space-y-6 bg-white rounded-lg shadow ring-1 ring-black/5">
                    <h2 class="pb-3 text-base font-semibold text-gray-900 border-b">Dados Bancários</h2>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Código do Banco</label>
                            <input type="text" name="banco_codigo"
                                value="{{ old('banco_codigo', $funcionario->dadosBancarios?->banco_codigo) }}"
                                class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>
                        <div class="lg:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Nome do Banco</label>
                            <input type="text" name="banco_nome"
                                value="{{ old('banco_nome', $funcionario->dadosBancarios?->banco_nome) }}"
                                class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Agência</label>
                            <input type="text" name="agencia"
                                value="{{ old('agencia', $funcionario->dadosBancarios?->agencia) }}"
                                class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Conta</label>
                            <input type="text" name="conta"
                                value="{{ old('conta', $funcionario->dadosBancarios?->conta) }}"
                                class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tipo de Conta</label>
                            <select name="tipo_conta"
                                class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Selecione...</option>
                                <option value="corrente" @selected(old('tipo_conta', $funcionario->dadosBancarios?->tipo_conta) === 'corrente')>Corrente</option>
                                <option value="poupanca" @selected(old('tipo_conta', $funcionario->dadosBancarios?->tipo_conta) === 'poupanca')>Poupança</option>
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

                    <div x-data="dependentesForm({{ Js::from($funcionario->dependentes ?? []) }})" class="space-y-4">
                        <template x-for="(dep, index) in dependentes" :key="index">
                            <div class="relative p-4 border border-gray-200 rounded-lg">
                                <button type="button" @click="removerDependente(index)"
                                    class="absolute text-gray-400 top-2 right-2 hover:text-red-500">
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
                                            x-init="dep.parentesco = dep.parentesco || ''"
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
            {{-- ABA 9: FÉRIAS --}}
            {{-- ============================================================ --}}
            <div x-show="tab === 'ferias'" x-transition>
                <div class="p-6 space-y-6 bg-white rounded-lg shadow ring-1 ring-black/5">
                    <div class="flex items-start gap-3 p-4 border rounded-lg bg-amber-50 border-amber-200">
                        <svg class="h-5 w-5 text-amber-600 shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z"
                                clip-rule="evenodd" />
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-amber-800">Status das Férias</p>
                            <p class="text-sm text-amber-700 mt-0.5">{{ $funcionario->status_ferias }}</p>
                        </div>
                    </div>

                    <h2 class="pb-3 text-base font-semibold text-gray-900 border-b">Período Aquisitivo</h2>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Início do Período Aquisitivo</label>
                            <input type="date" name="periodo_aquisitivo_inicio"
                                value="{{ old('periodo_aquisitivo_inicio', $funcionario->periodo_aquisitivo_inicio?->format('Y-m-d')) }}"
                                class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Fim do Período Aquisitivo</label>
                            <input type="date" name="periodo_aquisitivo_fim"
                                value="{{ old('periodo_aquisitivo_fim', $funcionario->periodo_aquisitivo_fim?->format('Y-m-d')) }}"
                                class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Vencimento das Férias</label>
                            <input type="date" name="ferias_vencimento"
                                value="{{ old('ferias_vencimento', $funcionario->ferias_vencimento?->format('Y-m-d')) }}"
                                class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>
                    </div>
                </div>
            </div>

            {{-- Barra de ações --}}
            <div class="flex items-center justify-end gap-3 px-6 py-4 mt-6 bg-white rounded-lg shadow ring-1 ring-black/5">
                <a href="{{ route('rh.funcionarios.index') }}"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                    Cancelar
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
                    💾 Salvar alterações
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        function dependentesForm(existingData) {
            return {
                dependentes: existingData && existingData.length > 0 ?
                    existingData.map(d => ({
                        id: d.id,
                        nome_completo: d.nome_completo || '',
                        data_nascimento: d.data_nascimento ? d.data_nascimento.toString().substring(0, 10) :
                        '', // Formata para YYYY-MM-DD || '',
                        parentesco: d.parentesco || '',
                        invalido: d.invalido || false
                    })) : [],

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
