{{-- resources/views/rh/funcionarios/show.blade.php --}}
@extends('layouts.app')
@php
    use App\Helpers\Mascara;
@endphp

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6" x-data="{ tab: 'pessoal' }">

        {{-- ===== CABEÇALHO ===== --}}
        <div class="mb-6 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-sm text-gray-500 mb-1">
                    <a href="{{ route('rh.funcionarios.index') }}" class="hover:text-indigo-600 transition-colors">
                        Funcionários
                    </a>
                    <span>/</span>
                    <span class="text-gray-700 font-medium">{{ $funcionario->nome_completo }}</span>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $funcionario->nome_completo }}</h1>
                <div class="mt-1 flex flex-wrap items-center gap-2 text-sm text-gray-500">
                    <span>{{ $funcionario->cargo?->nome ?? '—' }}</span>
                    @if ($funcionario->departamento)
                        <span class="text-gray-300">•</span>
                        <span>{{ $funcionario->departamento->nome }}</span>
                    @endif
                    <span class="text-gray-300">•</span>
                    <span>{{ $funcionario->tempo_empresa }}</span>
                </div>
            </div>

            {{-- Badges de status --}}
            <div class="flex flex-wrap items-center gap-2 self-start">
                @if ($funcionario->ativo)
                    <span
                        class="inline-flex items-center gap-1 rounded-full bg-green-50 px-3 py-1 text-xs font-semibold text-green-700 ring-1 ring-green-600/20">
                        <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                        Ativo
                    </span>
                @else
                    <span
                        class="inline-flex items-center gap-1 rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700 ring-1 ring-red-600/20">
                        <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                        Inativo
                    </span>
                @endif

                @if ($funcionario->eh_diarista)
                    <span
                        class="inline-flex items-center rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700 ring-1 ring-amber-600/20">
                        Diarista
                    </span>
                @endif

                @if ($funcionario->ferias_em_dobro)
                    <span
                        class="inline-flex items-center rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700 ring-1 ring-red-600/20">
                        ⚠ Férias Vencidas
                    </span>
                @endif
            </div>
        </div>

        {{-- ===== AÇÕES ===== --}}
        <div class="mb-6 flex flex-wrap items-center gap-2">
            <a href="{{ route('rh.funcionarios.index') }}"
                class="inline-flex items-center gap-1.5 rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M17 10a.75.75 0 01-.75.75H5.612l4.158 3.96a.75.75 0 11-1.04 1.08l-5.5-5.25a.75.75 0 010-1.08l5.5-5.25a.75.75 0 111.04 1.08L5.612 9.25H16.25A.75.75 0 0117 10z"
                        clip-rule="evenodd" />
                </svg>
                Voltar
            </a>

            @can('update', $funcionario)
                <a href="{{ route('rh.funcionarios.edit', $funcionario) }}"
                    class="inline-flex items-center gap-1.5 rounded-md bg-indigo-600 px-3 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition-colors">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path
                            d="M2.695 14.763l-1.262 3.154a.5.5 0 00.65.65l3.155-1.262a4 4 0 001.343-.885L17.5 5.5a2.121 2.121 0 00-3-3L3.58 13.42a4 4 0 00-.885 1.343z" />
                    </svg>
                    Editar
                </a>
            @endcan

            @can('rh.funcionarios.folha')
                <a href="{{ route('rh.folha-pagamento.show', $funcionario) }}"
                    class="inline-flex items-center gap-1.5 rounded-md bg-emerald-600 px-3 py-2 text-sm font-medium text-white hover:bg-emerald-700 transition-colors">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z"
                            clip-rule="evenodd" />
                    </svg>
                    Folha de Pagamento
                </a>
            @endcan
        </div>

        {{-- ===== CARDS DE RESUMO ===== --}}
        <div class="mb-6 grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Salário Bruto --}}
            <div class="rounded-lg bg-white p-5 shadow ring-1 ring-black/5">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Salário Bruto</p>
                <p class="mt-1 text-xl font-bold text-gray-900">
                    R$ {{ number_format($funcionario->salario_bruto, 2, ',', '.') }}
                </p>
                <p class="mt-1 text-xs text-gray-400">Base: R$ {{ number_format($funcionario->salario_base, 2, ',', '.') }}
                </p>
            </div>

            {{-- Total Descontos --}}
            <div class="rounded-lg bg-white p-5 shadow ring-1 ring-black/5">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Descontos</p>
                <p class="mt-1 text-xl font-bold text-red-600">
                    R$ {{ number_format($funcionario->total_descontos, 2, ',', '.') }}
                </p>
                <p class="mt-1 text-xs text-gray-400">INSS, faltas, benefícios</p>
            </div>

            {{-- Salário Líquido --}}
            <div class="rounded-lg bg-white p-5 shadow ring-1 ring-black/5 border-l-4 border-indigo-500">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Salário Líquido</p>
                <p class="mt-1 text-xl font-bold text-indigo-700">
                    R$ {{ number_format($funcionario->salario_liquido, 2, ',', '.') }}
                </p>
                <p class="mt-1 text-xs text-gray-400">Bruto menos descontos</p>
            </div>

            {{-- Status Férias --}}
            <div class="rounded-lg bg-white p-5 shadow ring-1 ring-black/5">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Férias</p>
                <p
                    class="mt-1 text-sm font-bold
                @if ($funcionario->ferias_em_dobro) text-red-600
                @elseif($funcionario->dias_para_vencimento_ferias <= 30 && $funcionario->dias_para_vencimento_ferias >= 0) text-amber-600
                @else text-green-600 @endif">
                    {{ $funcionario->status_ferias }}
                </p>
                @if ($funcionario->ferias_vencimento)
                    <p class="mt-1 text-xs text-gray-400">
                        Vencimento: {{ \Carbon\Carbon::parse($funcionario->ferias_vencimento)->format('d/m/Y') }}
                    </p>
                @endif
            </div>
        </div>

        {{-- ===== ABAS DE NAVEGAÇÃO ===== --}}
        <div class="mb-6 border-b border-gray-200 overflow-x-auto">
            <nav class="-mb-px flex gap-1 min-w-max">

                @php
                    $abas = [
                        ['id' => 'pessoal', 'label' => 'Dados Pessoais', 'icon' => 'user'],
                        ['id' => 'contato', 'label' => 'Contato & Endereço', 'icon' => 'map-pin'],
                        ['id' => 'contrato', 'label' => 'Contrato', 'icon' => 'briefcase'],
                        ['id' => 'documentos', 'label' => 'Documentos', 'icon' => 'document'],
                        ['id' => 'financeiro', 'label' => 'Financeiro', 'icon' => 'currency'],
                        ['id' => 'beneficios', 'label' => 'Benefícios', 'icon' => 'heart'],
                        ['id' => 'ferias', 'label' => 'Férias', 'icon' => 'calendar'],
                        ['id' => 'bancario', 'label' => 'Dados Bancários', 'icon' => 'bank'],
                    ];
                @endphp

                @foreach ($abas as $aba)
                    <button type="button" @click="tab = '{{ $aba['id'] }}'"
                        :class="tab === '{{ $aba['id'] }}'
                            ?
                            'border-indigo-600 text-indigo-600' :
                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="flex items-center gap-1.5 whitespace-nowrap border-b-2 px-4 py-3 text-sm font-medium transition-colors">

                        {{-- Ícones de cada aba --}}
                        @if ($aba['icon'] === 'user')
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path
                                    d="M10 8a3 3 0 100-6 3 3 0 000 6zM3.465 14.493a1.23 1.23 0 00.41 1.412A9.957 9.957 0 0010 18c2.31 0 4.438-.784 6.131-2.1.43-.333.604-.903.408-1.41a7.002 7.002 0 00-13.074.003z" />
                            </svg>
                        @elseif($aba['icon'] === 'map-pin')
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M9.69 18.933l.003.001C9.89 19.02 10 19 10 19s.11.02.308-.066l.002-.001.006-.003.018-.008a5.741 5.741 0 00.281-.14c.186-.096.446-.24.757-.433.62-.384 1.445-.966 2.274-1.765C15.302 14.988 17 12.493 17 9A7 7 0 103 9c0 3.492 1.698 5.988 3.355 7.584a13.731 13.731 0 002.273 1.765 11.842 11.842 0 00.757.433l.018.008.006.003zM10 11.25a2.25 2.25 0 100-4.5 2.25 2.25 0 000 4.5z"
                                    clip-rule="evenodd" />
                            </svg>
                        @elseif($aba['icon'] === 'briefcase')
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M6 3.75A2.75 2.75 0 018.75 1h2.5A2.75 2.75 0 0114 3.75v.443c.572.055 1.14.122 1.706.2C17.053 4.582 18 5.75 18 7.168V14.5A2.5 2.5 0 0115.5 17H4.5A2.5 2.5 0 012 14.5V7.168c0-1.418.947-2.586 2.294-2.775A41.147 41.147 0 016 4.193V3.75zm6.5 0v.325a41.622 41.622 0 00-5 0V3.75c0-.69.56-1.25 1.25-1.25h2.5c.69 0 1.25.56 1.25 1.25zM10 10a1 1 0 00-1 1v.01a1 1 0 001 1h.01a1 1 0 001-1V11a1 1 0 00-1-1H10z"
                                    clip-rule="evenodd" />
                            </svg>
                        @elseif($aba['icon'] === 'document')
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z"
                                    clip-rule="evenodd" />
                            </svg>
                        @elseif($aba['icon'] === 'currency')
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.798 7.45c.512-.67 1.135-.95 1.702-.95s1.19.28 1.702.95a.75.75 0 001.192-.91C12.637 5.55 11.596 5 10.5 5s-2.137.55-2.894 1.54A5.205 5.205 0 006.83 8H6.5a.75.75 0 000 1.5h.098a5.402 5.402 0 000 1H6.5a.75.75 0 000 1.5h.326a5.199 5.199 0 00.778 1.459C8.363 14.45 9.404 15 10.5 15s2.137-.55 2.894-1.541a.75.75 0 00-1.192-.91c-.512.67-1.135.95-1.702.95s-1.19-.28-1.702-.95a3.699 3.699 0 01-.392-.549H10.5a.75.75 0 000-1.5H8.07a3.899 3.899 0 010-1H10.5a.75.75 0 000-1.5H8.41a3.7 3.7 0 01.388-.55z"
                                    clip-rule="evenodd" />
                            </svg>
                        @elseif($aba['icon'] === 'heart')
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path
                                    d="M9.653 16.915l-.005-.003-.019-.01a20.759 20.759 0 01-1.162-.682 22.045 22.045 0 01-2.582-2.184C4.342 12.533 2 10.5 2 7.5 2 5.015 4.015 3 6.5 3c1.343 0 2.546.563 3.5 1.44C10.954 3.563 12.157 3 13.5 3 15.985 3 18 5.015 18 7.5c0 3-2.342 5.033-3.885 6.536a22.049 22.049 0 01-2.582 2.184 20.663 20.663 0 01-1.162.682l-.019.01-.005.003h-.002a.75.75 0 01-.69 0h-.002z" />
                            </svg>
                        @elseif($aba['icon'] === 'calendar')
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M5.75 2a.75.75 0 01.75.75V4h7V2.75a.75.75 0 011.5 0V4h.25A2.75 2.75 0 0118 6.75v8.5A2.75 2.75 0 0115.25 18H4.75A2.75 2.75 0 012 15.25v-8.5A2.75 2.75 0 014.75 4H5V2.75A.75.75 0 015.75 2zm-1 5.5c-.69 0-1.25.56-1.25 1.25v6.5c0 .69.56 1.25 1.25 1.25h10.5c.69 0 1.25-.56 1.25-1.25v-6.5c0-.69-.56-1.25-1.25-1.25H4.75z"
                                    clip-rule="evenodd" />
                            </svg>
                        @elseif($aba['icon'] === 'bank')
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M1 2.75A.75.75 0 011.75 2h16.5a.75.75 0 010 1.5H18v8.75A2.75 2.75 0 0115.25 15h-1.072l.798 3.06a.75.75 0 01-1.452.38L13.41 18H6.59l-.114.44a.75.75 0 01-1.452-.38L5.823 15H4.75A2.75 2.75 0 012 12.25V3.5h-.25A.75.75 0 011 2.75zM7.373 15l-.391 1.5h6.037l-.392-1.5H7.373zM13.25 5a.75.75 0 01.75.75v5.5a.75.75 0 01-1.5 0v-5.5a.75.75 0 01.75-.75zm-3.25.75a.75.75 0 00-1.5 0v5.5a.75.75 0 001.5 0v-5.5zM6.75 5a.75.75 0 01.75.75v5.5a.75.75 0 01-1.5 0v-5.5A.75.75 0 016.75 5z"
                                    clip-rule="evenodd" />
                            </svg>
                        @endif
                        {{ $aba['label'] }}
                    </button>
                @endforeach
            </nav>
        </div>

        {{-- ===== CONTEÚDO DAS ABAS ===== --}}

        {{-- ─── ABA: DADOS PESSOAIS ─── --}}
        <div x-show="tab === 'pessoal'" x-transition.opacity>
            <div class="rounded-lg bg-white shadow ring-1 ring-black/5 overflow-hidden">
                <div class="border-b border-gray-100 bg-gray-50 px-6 py-3">
                    <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Dados Pessoais</h2>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-5">

                        <x-rh.show-field label="Nome Completo" :value="$funcionario->nome_completo" class="lg:col-span-2" />
                        <x-rh.show-field label="CPF" :value="$funcionario->cpf ? Mascara::cpf($funcionario->cpf) : '—'" />
                        <x-rh.show-field label="RG" :value="$funcionario->rg ?? '—'" />
                        <x-rh.show-field label="Órgão Expedidor" :value="$funcionario->orgao_expedidor_rg ?? '—'" />
                        <x-rh.show-field label="Data de Nascimento" :value="$funcionario->data_nascimento ? $funcionario->data_nascimento->format('d/m/Y') : '—'" />
                        <x-rh.show-field label="Estado Civil" :value="$funcionario->estado_civil ?? '—'" />
                        <x-rh.show-field label="Gênero" :value="$funcionario->genero ?? '—'" />
                        <x-rh.show-field label="Nacionalidade" :value="$funcionario->nacionalidade ?? '—'" />
                        <x-rh.show-field label="Naturalidade" :value="$funcionario->naturalidade ?? '—'" />
                        <x-rh.show-field label="Dep. Imposto de Renda" :value="$funcionario->qtd_dependentes_ir ?? '0'" />
                        <x-rh.show-field label="Dep. Salário Família" :value="$funcionario->qtd_dependentes_salario_familia ?? '0'" />
                    </dl>
                </div>
            </div>
        </div>

        {{-- ─── ABA: CONTATO & ENDEREÇO ─── --}}
        <div x-show="tab === 'contato'" x-transition.opacity>
            <div class="space-y-5">

                {{-- Contato --}}
                <div class="rounded-lg bg-white shadow ring-1 ring-black/5 overflow-hidden">
                    <div class="border-b border-gray-100 bg-gray-50 px-6 py-3">
                        <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Contato</h2>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-5">
                            <x-rh.show-field label="Telefone" :value="$funcionario->telefone ?? '—'" />
                            <x-rh.show-field label="Celular" :value="$funcionario->celular ?? '—'" />
                            <x-rh.show-field label="E-mail Corporativo" :value="$funcionario->email ?? '—'" />
                            <x-rh.show-field label="E-mail Pessoal" :value="$funcionario->email_pessoal ?? '—'" />
                        </dl>
                    </div>
                </div>

                {{-- Endereço --}}
                <div class="rounded-lg bg-white shadow ring-1 ring-black/5 overflow-hidden">
                    <div class="border-b border-gray-100 bg-gray-50 px-6 py-3">
                        <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Endereço</h2>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-5">
                            <x-rh.show-field label="CEP" :value="$funcionario->cep ?? '—'" />
                            <x-rh.show-field label="Logradouro" :value="$funcionario->logradouro ?? '—'" class="lg:col-span-2" />
                            <x-rh.show-field label="Número" :value="$funcionario->numero ?? '—'" />
                            <x-rh.show-field label="Complemento" :value="$funcionario->complemento ?? '—'" />
                            <x-rh.show-field label="Bairro" :value="$funcionario->bairro ?? '—'" />
                            <x-rh.show-field label="Cidade" :value="$funcionario->cidade ?? '—'" />
                            <x-rh.show-field label="Estado" :value="$funcionario->estado ?? '—'" />
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        {{-- ─── ABA: CONTRATO ─── --}}
        <div x-show="tab === 'contrato'" x-transition.opacity>
            <div class="space-y-5">

                {{-- Vínculo empregatício --}}
                <div class="rounded-lg bg-white shadow ring-1 ring-black/5 overflow-hidden">
                    <div class="border-b border-gray-100 bg-gray-50 px-6 py-3">
                        <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Vínculo Empregatício</h2>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-5">
                            <x-rh.show-field label="Departamento" :value="$funcionario->departamento?->nome ?? '—'" />
                            <x-rh.show-field label="Cargo" :value="$funcionario->cargo?->nome ?? '—'" />
                            <x-rh.show-field label="Local de Trabalho" :value="$funcionario->local_trabalho ?? '—'" />
                            <x-rh.show-field label="Tipo de Contrato" :value="$funcionario->tipo_contrato ?? '—'" />
                            <x-rh.show-field label="Tipo de Contratação" :value="$funcionario->tipo_contratacao ?? '—'" />
                            <x-rh.show-field label="Data de Admissão" :value="$funcionario->data_admissao ? $funcionario->data_admissao->format('d/m/Y') : '—'" />
                            <x-rh.show-field label="Data de Demissão" :value="$funcionario->data_demissao ? $funcionario->data_demissao->format('d/m/Y') : '—'" />
                            <x-rh.show-field label="Tempo de Empresa" :value="$funcionario->tempo_empresa" />

                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Situação</dt>
                                <dd class="mt-1">
                                    @if ($funcionario->ativo)
                                        <span
                                            class="inline-flex items-center rounded-full bg-green-50 px-2.5 py-0.5 text-xs font-medium text-green-700 ring-1 ring-green-600/20">Ativo</span>
                                    @else
                                        <span
                                            class="inline-flex items-center rounded-full bg-red-50 px-2.5 py-0.5 text-xs font-medium text-red-700 ring-1 ring-red-600/20">Inativo</span>
                                    @endif
                                </dd>
                            </div>

                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Diarista</dt>
                                <dd class="mt-1">
                                    @if ($funcionario->eh_diarista)
                                        <span
                                            class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-medium text-amber-700 ring-1 ring-amber-600/20">Sim</span>
                                    @else
                                        <span
                                            class="inline-flex items-center rounded-full bg-gray-50 px-2.5 py-0.5 text-xs font-medium text-gray-600 ring-1 ring-gray-600/20">Não</span>
                                    @endif
                                </dd>
                            </div>

                            @if ($funcionario->eh_diarista)
                                <x-rh.show-field label="Valor da Diária" :value="'R$ ' . number_format($funcionario->valor_diaria ?? 0, 2, ',', '.')" />
                            @endif
                        </dl>
                    </div>
                </div>

                {{-- Horários --}}
                <div class="rounded-lg bg-white shadow ring-1 ring-black/5 overflow-hidden">
                    <div class="border-b border-gray-100 bg-gray-50 px-6 py-3">
                        <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Jornada de Trabalho</h2>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-x-6 gap-y-5">
                            <x-rh.show-field label="Carga Horária Semanal" :value="$funcionario->carga_horaria_semanal
                                ? $funcionario->carga_horaria_semanal . 'h'
                                : '—'" />

                            <x-rh.show-field label="Entrada" :value="$funcionario->horario_entrada
                                ? \Carbon\Carbon::parse($funcionario->horario_entrada)->format('H:i')
                                : '—'" />
                            <x-rh.show-field label="Saída" :value="$funcionario->horario_saida
                                ? \Carbon\Carbon::parse($funcionario->horario_saida)->format('H:i')
                                : '—'" />
                            <x-rh.show-field label="Almoço Início" :value="$funcionario->horario_almoco_inicio
                                ? \Carbon\Carbon::parse($funcionario->horario_almoco_inicio)->format('H:i')
                                : '—'" />
                            <x-rh.show-field label="Almoço Fim" :value="$funcionario->horario_almoco_fim
                                ? \Carbon\Carbon::parse($funcionario->horario_almoco_fim)->format('H:i')
                                : '—'" />

                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">6º Dia Útil no Mês
                                </dt>
                                <dd class="mt-1">
                                    @if ($funcionario->sexto_dia_util_mes)
                                        <span
                                            class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-medium text-blue-700 ring-1 ring-blue-600/20">Sim</span>
                                    @else
                                        <span
                                            class="inline-flex items-center rounded-full bg-gray-50 px-2.5 py-0.5 text-xs font-medium text-gray-600 ring-1 ring-gray-600/20">Não</span>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                {{-- Observações --}}
                @if ($funcionario->observacoes)
                    <div class="rounded-lg bg-white shadow ring-1 ring-black/5 overflow-hidden">
                        <div class="border-b border-gray-100 bg-gray-50 px-6 py-3">
                            <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Observações</h2>
                        </div>
                        <div class="p-6">
                            <p class="text-sm text-gray-700 whitespace-pre-line">{{ $funcionario->observacoes }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- ─── ABA: DOCUMENTOS ─── --}}
        <div x-show="tab === 'documentos'" x-transition.opacity>
            <div class="rounded-lg bg-white shadow ring-1 ring-black/5 overflow-hidden">
                <div class="border-b border-gray-100 bg-gray-50 px-6 py-3">
                    <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Documentos</h2>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-5">
                        <x-rh.show-field label="CTPS – Número" :value="$funcionario->ctps_numero ?? '—'" />
                        <x-rh.show-field label="CTPS – Série" :value="$funcionario->ctps_serie ?? '—'" />
                        <x-rh.show-field label="CTPS – UF" :value="$funcionario->ctps_uf ?? '—'" />
                        <x-rh.show-field label="CTPS – Data de Emissão" :value="$funcionario->ctps_data_emissao
                            ? $funcionario->ctps_data_emissao->format('d/m/Y')
                            : '—'" />
                        <x-rh.show-field label="PIS/PASEP" :value="$funcionario->pis_pasep ?? '—'" />
                        <x-rh.show-field label="Título de Eleitor" :value="$funcionario->titulo_eleitor ?? '—'" />
                        <x-rh.show-field label="Zona Eleitoral" :value="$funcionario->zona_eleitoral ?? '—'" />
                        <x-rh.show-field label="Seção Eleitoral" :value="$funcionario->secao_eleitoral ?? '—'" />
                        <x-rh.show-field label="Certificado de Reservista" :value="$funcionario->certificado_reservista ?? '—'" />
                    </dl>
                </div>
            </div>
        </div>

        {{-- ─── ABA: FINANCEIRO ─── --}}
        <div x-show="tab === 'financeiro'" x-transition.opacity>
            <div class="space-y-5">

                {{-- Proventos --}}
                <div class="rounded-lg bg-white shadow ring-1 ring-black/5 overflow-hidden">
                    <div class="border-b border-gray-100 bg-emerald-50 px-6 py-3">
                        <h2 class="text-sm font-semibold text-emerald-700 uppercase tracking-wide">Proventos</h2>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-5">
                            <x-rh.show-field label="Salário Base" :value="'R$ ' . number_format($funcionario->salario_base ?? 0, 2, ',', '.')" />
                            <x-rh.show-field label="Gratificação / Provento" :value="'R$ ' . number_format($funcionario->gratificacao_provento ?? 0, 2, ',', '.')" />
                            <x-rh.show-field label="DSR Hora Extra" :value="'R$ ' . number_format($funcionario->dsr_hora_extra ?? 0, 2, ',', '.')" />
                            <x-rh.show-field label="Hora Extra" :value="'R$ ' . number_format($funcionario->hora_extra ?? 0, 2, ',', '.')" />
                            <x-rh.show-field label="Salário Família" :value="'R$ ' . number_format($funcionario->salario_familia ?? 0, 2, ',', '.')" />
                            <x-rh.show-field label="Vale Extra" :value="'R$ ' . number_format($funcionario->vale_extra ?? 0, 2, ',', '.')" />
                        </dl>
                        <div class="mt-4 rounded-md bg-emerald-50 px-4 py-3 flex items-center justify-between">
                            <span class="text-sm font-semibold text-emerald-700">Total Bruto</span>
                            <span class="text-lg font-bold text-emerald-700">
                                R$ {{ number_format($funcionario->salario_bruto, 2, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Descontos --}}
                <div class="rounded-lg bg-white shadow ring-1 ring-black/5 overflow-hidden">
                    <div class="border-b border-gray-100 bg-red-50 px-6 py-3">
                        <h2 class="text-sm font-semibold text-red-700 uppercase tracking-wide">Descontos</h2>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-5">
                            <x-rh.show-field label="INSS (8%)" :value="'R$ ' . number_format($funcionario->desconto_inss_8_porcento ?? 0, 2, ',', '.')" />
                            <x-rh.show-field label="Faltas (Qtd.)" :value="$funcionario->faltas ?? '0'" />
                            <x-rh.show-field label="DSR Faltas" :value="'R$ ' . number_format($funcionario->dsr_faltas ?? 0, 2, ',', '.')" />
                            <x-rh.show-field label="Desconto Faltas" :value="'R$ ' . number_format($funcionario->desconto_faltas ?? 0, 2, ',', '.')" />
                        </dl>
                        <div class="mt-4 rounded-md bg-red-50 px-4 py-3 flex items-center justify-between">
                            <span class="text-sm font-semibold text-red-700">Total Descontos</span>
                            <span class="text-lg font-bold text-red-700">
                                R$ {{ number_format($funcionario->total_descontos, 2, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Líquido --}}
                <div
                    class="rounded-lg bg-indigo-50 shadow ring-1 ring-indigo-200 px-6 py-5 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-indigo-700 uppercase tracking-wide">Salário Líquido</p>
                        <p class="text-xs text-indigo-500 mt-0.5">Bruto menos descontos</p>
                    </div>
                    <p class="text-2xl font-extrabold text-indigo-700">
                        R$ {{ number_format($funcionario->salario_liquido, 2, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        {{-- ─── ABA: BENEFÍCIOS ─── --}}
        <div x-show="tab === 'beneficios'" x-transition.opacity>
            <div class="rounded-lg bg-white shadow ring-1 ring-black/5 overflow-hidden">
                <div class="border-b border-gray-100 bg-gray-50 px-6 py-3">
                    <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Benefícios</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        {{-- Vale Transporte --}}
                        <div
                            class="rounded-lg border p-4 {{ $funcionario->vale_transporte ? 'border-green-200 bg-green-50' : 'border-gray-200 bg-gray-50' }}">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-semibold text-gray-700">Vale Transporte</span>
                                @if ($funcionario->vale_transporte)
                                    <span
                                        class="text-xs font-medium text-green-700 bg-green-100 rounded-full px-2 py-0.5">Ativo</span>
                                @else
                                    <span
                                        class="text-xs font-medium text-gray-500 bg-gray-100 rounded-full px-2 py-0.5">Inativo</span>
                                @endif
                            </div>
                            <p
                                class="text-xl font-bold {{ $funcionario->vale_transporte ? 'text-green-700' : 'text-gray-400' }}">
                                R$ {{ number_format($funcionario->valor_vale_transporte ?? 0, 2, ',', '.') }}
                            </p>
                        </div>

                        {{-- Vale Alimentação --}}
                        <div
                            class="rounded-lg border p-4 {{ $funcionario->vale_alimentacao ? 'border-green-200 bg-green-50' : 'border-gray-200 bg-gray-50' }}">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-semibold text-gray-700">Vale Alimentação</span>
                                @if ($funcionario->vale_alimentacao)
                                    <span
                                        class="text-xs font-medium text-green-700 bg-green-100 rounded-full px-2 py-0.5">Ativo</span>
                                @else
                                    <span
                                        class="text-xs font-medium text-gray-500 bg-gray-100 rounded-full px-2 py-0.5">Inativo</span>
                                @endif
                            </div>
                            <p
                                class="text-xl font-bold {{ $funcionario->vale_alimentacao ? 'text-green-700' : 'text-gray-400' }}">
                                R$ {{ number_format($funcionario->valor_vale_alimentacao ?? 0, 2, ',', '.') }}
                            </p>
                        </div>

                        {{-- Plano de Saúde --}}
                        <div
                            class="rounded-lg border p-4 {{ $funcionario->plano_saude ? 'border-blue-200 bg-blue-50' : 'border-gray-200 bg-gray-50' }}">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-semibold text-gray-700">Plano de Saúde</span>
                                @if ($funcionario->plano_saude)
                                    <span
                                        class="text-xs font-medium text-blue-700 bg-blue-100 rounded-full px-2 py-0.5">Ativo</span>
                                @else
                                    <span
                                        class="text-xs font-medium text-gray-500 bg-gray-100 rounded-full px-2 py-0.5">Inativo</span>
                                @endif
                            </div>
                        </div>

                        {{-- Plano Odontológico --}}
                        <div
                            class="rounded-lg border p-4 {{ $funcionario->plano_odontologico ? 'border-blue-200 bg-blue-50' : 'border-gray-200 bg-gray-50' }}">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-semibold text-gray-700">Plano Odontológico</span>
                                @if ($funcionario->plano_odontologico)
                                    <span
                                        class="text-xs font-medium text-blue-700 bg-blue-100 rounded-full px-2 py-0.5">Ativo</span>
                                @else
                                    <span
                                        class="text-xs font-medium text-gray-500 bg-gray-100 rounded-full px-2 py-0.5">Inativo</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ─── ABA: FÉRIAS ─── --}}
        <div x-show="tab === 'ferias'" x-transition.opacity>
            <div class="space-y-5">

                {{-- Status de Férias --}}
                <div class="rounded-lg bg-white shadow ring-1 ring-black/5 overflow-hidden">
                    <div class="border-b border-gray-100 bg-gray-50 px-6 py-3">
                        <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Status das Férias</h2>
                    </div>
                    <div class="p-6">
                        <div
                            class="mb-5 flex items-start gap-4 rounded-lg border px-5 py-4
                        @if ($funcionario->ferias_em_dobro) border-red-200 bg-red-50
                        @elseif($funcionario->dias_para_vencimento_ferias <= 30 && $funcionario->dias_para_vencimento_ferias >= 0) border-amber-200 bg-amber-50
                        @elseif(!$funcionario->temDireitoFerias()) border-gray-200 bg-gray-50
                        @else border-green-200 bg-green-50 @endif">
                            <div class="flex-shrink-0 mt-0.5">
                                @if ($funcionario->ferias_em_dobro)
                                    <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z"
                                            clip-rule="evenodd" />
                                    </svg>
                                @elseif($funcionario->dias_para_vencimento_ferias <= 30 && $funcionario->dias_para_vencimento_ferias >= 0)
                                    <svg class="h-5 w-5 text-amber-500" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z"
                                            clip-rule="evenodd" />
                                    </svg>
                                @else
                                    <svg class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"
                                            clip-rule="evenodd" />
                                    </svg>
                                @endif
                            </div>
                            <div>
                                <p
                                    class="text-sm font-semibold
                                @if ($funcionario->ferias_em_dobro) text-red-700
                                @elseif($funcionario->dias_para_vencimento_ferias <= 30 && $funcionario->dias_para_vencimento_ferias >= 0) text-amber-700
                                @else text-green-700 @endif">
                                    {{ $funcionario->status_ferias }}
                                </p>
                                @if ($funcionario->ferias_em_dobro)
                                    <p class="text-xs text-red-600 mt-0.5">Conforme CLT Art. 137, férias não gozadas devem
                                        ser pagas em dobro.</p>
                                @endif
                            </div>
                        </div>

                        <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-5">
                            <x-rh.show-field label="Período Aquisitivo Início" :value="$funcionario->periodo_aquisitivo_inicio
                                ? $funcionario->periodo_aquisitivo_inicio->format('d/m/Y')
                                : '—'" />
                            <x-rh.show-field label="Período Aquisitivo Fim" :value="$funcionario->periodo_aquisitivo_fim
                                ? $funcionario->periodo_aquisitivo_fim->format('d/m/Y')
                                : '—'" />
                            <x-rh.show-field label="Vencimento das Férias" :value="$funcionario->ferias_vencimento
                                ? \Carbon\Carbon::parse($funcionario->ferias_vencimento)->format('d/m/Y')
                                : '—'" />
                            <x-rh.show-field label="Dias para Vencimento" :value="$funcionario->temDireitoFerias()
                                ? abs($funcionario->dias_para_vencimento_ferias) . ' dias'
                                : '—'" />
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Férias em Dobro</dt>
                                <dd class="mt-1">
                                    @if ($funcionario->ferias_em_dobro)
                                        <span
                                            class="inline-flex items-center rounded-full bg-red-50 px-2.5 py-0.5 text-xs font-medium text-red-700 ring-1 ring-red-600/20">Sim</span>
                                    @else
                                        <span
                                            class="inline-flex items-center rounded-full bg-gray-50 px-2.5 py-0.5 text-xs font-medium text-gray-600 ring-1 ring-gray-600/20">Não</span>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                {{-- Histórico de Períodos de Férias --}}
                @if ($funcionario->periodoFerias && $funcionario->periodoFerias->count() > 0)
                    <div class="rounded-lg bg-white shadow ring-1 ring-black/5 overflow-hidden">
                        <div class="border-b border-gray-100 bg-gray-50 px-6 py-3">
                            <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Histórico de Férias
                                Gozadas</h2>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Início
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fim
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dias
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 bg-white">
                                    @foreach ($funcionario->periodoFerias->sortByDesc('created_at') as $periodo)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 text-sm text-gray-900">
                                                {{ $periodo->data_inicio ? \Carbon\Carbon::parse($periodo->data_inicio)->format('d/m/Y') : '—' }}
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-900">
                                                {{ $periodo->data_fim ? \Carbon\Carbon::parse($periodo->data_fim)->format('d/m/Y') : '—' }}
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-900">
                                                {{ $periodo->dias ?? '—' }}
                                            </td>
                                            <td class="px-4 py-3">
                                                <span
                                                    class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                        {{ ($periodo->status ?? '') === 'aprovado' ? 'bg-green-50 text-green-700 ring-1 ring-green-600/20' : 'bg-gray-50 text-gray-600 ring-1 ring-gray-600/20' }}">
                                                    {{ ucfirst($periodo->status ?? 'Registrado') }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- ─── ABA: DADOS BANCÁRIOS ─── --}}
        <div x-show="tab === 'bancario'" x-transition.opacity>
            <div class="rounded-lg bg-white shadow ring-1 ring-black/5 overflow-hidden">
                <div class="border-b border-gray-100 bg-gray-50 px-6 py-3">
                    <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Dados Bancários</h2>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-5">
                        <x-rh.show-field label="Banco (Código)" :value="$funcionario->banco_codigo ?? '—'" />
                        <x-rh.show-field label="Banco (Nome)" :value="$funcionario->banco_nome ?? '—'" class="lg:col-span-2" />
                        <x-rh.show-field label="Agência" :value="$funcionario->agencia ?? '—'" />
                        <x-rh.show-field label="Conta" :value="$funcionario->conta ?? '—'" />
                        <x-rh.show-field label="Tipo de Conta" :value="$funcionario->tipo_conta ?? '—'" />
                    </dl>
                </div>
            </div>
        </div>

        {{-- ===== RODAPÉ COM METADADOS ===== --}}
        <div class="mt-6 rounded-lg bg-white shadow ring-1 ring-black/5 px-6 py-4">
            <div class="flex flex-wrap items-center justify-between gap-3 text-xs text-gray-400">
                <div class="flex flex-wrap gap-4">
                    <span>
                        <strong class="font-medium text-gray-500">Cadastrado em:</strong>
                        {{ $funcionario->created_at ? $funcionario->created_at->format('d/m/Y \à\s H:i') : '—' }}
                    </span>
                    <span>
                        <strong class="font-medium text-gray-500">Última atualização:</strong>
                        {{ $funcionario->updated_at ? $funcionario->updated_at->diffForHumans() : '—' }}
                    </span>
                    @if ($funcionario->usuario)
                        <span>
                            <strong class="font-medium text-gray-500">Usuário vinculado:</strong>
                            {{ $funcionario->usuario->name }}
                        </span>
                    @endif
                </div>
                <span class="font-mono">ID #{{ $funcionario->id }}</span>
            </div>
        </div>

    </div>
@endsection
