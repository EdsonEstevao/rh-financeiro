<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Novo Funcionário') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('rh.funcionarios.store') }}">
                        @csrf

                        <!-- Dados Básicos -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold mb-4">Dados Pessoais</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Nome Completo *</label>
                                    <input type="text" name="nome_completo" value="{{ old('nome_completo') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">CPF *</label>
                                    <input type="text" name="cpf" value="{{ old('cpf') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">RG *</label>
                                    <input type="text" name="rg" value="{{ old('rg') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Data de Nascimento *</label>
                                    <input type="date" name="data_nascimento" value="{{ old('data_nascimento') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Estado Civil *</label>
                                    <select name="estado_civil"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                        <option value="">Selecione...</option>
                                        <option value="solteiro"
                                            {{ old('estado_civil') == 'solteiro' ? 'selected' : '' }}>Solteiro(a)
                                        </option>
                                        <option value="casado" {{ old('estado_civil') == 'casado' ? 'selected' : '' }}>
                                            Casado(a)</option>
                                        <option value="divorciado"
                                            {{ old('estado_civil') == 'divorciado' ? 'selected' : '' }}>Divorciado(a)
                                        </option>
                                        <option value="viuvo" {{ old('estado_civil') == 'viuvo' ? 'selected' : '' }}>
                                            Viúvo(a)</option>
                                        <option value="uniao_estavel"
                                            {{ old('estado_civil') == 'uniao_estavel' ? 'selected' : '' }}>União
                                            Estável</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Gênero *</label>
                                    <select name="genero"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                        <option value="">Selecione...</option>
                                        <option value="masculino" {{ old('genero') == 'masculino' ? 'selected' : '' }}>
                                            Masculino</option>
                                        <option value="feminino" {{ old('genero') == 'feminino' ? 'selected' : '' }}>
                                            Feminino</option>
                                        <option value="outro" {{ old('genero') == 'outro' ? 'selected' : '' }}>Outro
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Contato -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold mb-4">Contato</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Telefone *</label>
                                    <input type="text" name="telefone" value="{{ old('telefone') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Celular *</label>
                                    <input type="text" name="celular" value="{{ old('celular') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Email *</label>
                                    <input type="email" name="email" value="{{ old('email') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                </div>
                            </div>
                        </div>

                        <!-- Endereço -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold mb-4">Endereço</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">CEP *</label>
                                    <input type="text" name="cep" value="{{ old('cep') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Logradouro *</label>
                                    <input type="text" name="logradouro" value="{{ old('logradouro') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Número *</label>
                                    <input type="text" name="numero" value="{{ old('numero') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Bairro *</label>
                                    <input type="text" name="bairro" value="{{ old('bairro') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Cidade *</label>
                                    <input type="text" name="cidade" value="{{ old('cidade') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Estado *</label>
                                    <input type="text" name="estado" value="{{ old('estado') }}" maxlength="2"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                </div>
                            </div>
                        </div>

                        <!-- Dados Trabalhistas -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold mb-4">Dados Trabalhistas</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Departamento *</label>
                                    <select name="departamento_id"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                        <option value="">Selecione...</option>
                                        @foreach ($departamentos as $dept)
                                            <option value="{{ $dept->id }}"
                                                {{ old('departamento_id') == $dept->id ? 'selected' : '' }}>
                                                {{ $dept->nome }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Cargo *</label>
                                    <select name="cargo_id"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                        <option value="">Selecione...</option>
                                        @foreach ($cargos as $cargo)
                                            <option value="{{ $cargo->id }}"
                                                {{ old('cargo_id') == $cargo->id ? 'selected' : '' }}>
                                                {{ $cargo->titulo }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Data de Admissão *</label>
                                    <input type="date" name="data_admissao" value="{{ old('data_admissao') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Tipo de Contrato *</label>
                                    <select name="tipo_contrato"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                        <option value="">Selecione...</option>
                                        <option value="clt" {{ old('tipo_contrato') == 'clt' ? 'selected' : '' }}>
                                            CLT</option>
                                        <option value="temporario"
                                            {{ old('tipo_contrato') == 'temporario' ? 'selected' : '' }}>Temporário
                                        </option>
                                        <option value="aprendiz"
                                            {{ old('tipo_contrato') == 'aprendiz' ? 'selected' : '' }}>Aprendiz
                                        </option>
                                        <option value="estagio"
                                            {{ old('tipo_contrato') == 'estagio' ? 'selected' : '' }}>Estágio</option>
                                        <option value="terceirizado"
                                            {{ old('tipo_contrato') == 'terceirizado' ? 'selected' : '' }}>Terceirizado
                                        </option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Salário Base *</label>
                                    <input type="number" step="0.01" name="salario_base"
                                        value="{{ old('salario_base') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">CTPS Número *</label>
                                    <input type="text" name="ctps_numero" value="{{ old('ctps_numero') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">CTPS Série *</label>
                                    <input type="text" name="ctps_serie" value="{{ old('ctps_serie') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">CTPS UF *</label>
                                    <input type="text" name="ctps_uf" value="{{ old('ctps_uf') }}"
                                        maxlength="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                        required>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">PIS/PASEP *</label>
                                    <input type="text" name="pis_pasep" value="{{ old('pis_pasep') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                </div>
                            </div>
                        </div>

                        <!-- Dados Bancários -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold mb-4">Dados Bancários</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Código do Banco *</label>
                                    <input type="text" name="banco_codigo" value="{{ old('banco_codigo') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Nome do Banco *</label>
                                    <input type="text" name="banco_nome" value="{{ old('banco_nome') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Agência *</label>
                                    <input type="text" name="agencia" value="{{ old('agencia') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Conta *</label>
                                    <input type="text" name="conta" value="{{ old('conta') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Tipo da Conta *</label>
                                    <select name="tipo_conta"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                        <option value="">Selecione...</option>
                                        <option value="corrente"
                                            {{ old('tipo_conta') == 'corrente' ? 'selected' : '' }}>Corrente</option>
                                        <option value="poupanca"
                                            {{ old('tipo_conta') == 'poupanca' ? 'selected' : '' }}>Poupança</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Botões -->
                        <div class="flex justify-end space-x-4">
                            <a href="{{ route('rh.funcionarios.index') }}"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Cancelar
                            </a>
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Salvar Funcionário
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
