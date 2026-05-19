<?php

namespace App\Http\Requests\RH;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FuncionarioStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('funcionarios.create') ?? false;
    }

    public function rules(): array
    {
        return [
            // Relacionamentos
            'departamento_id' => ['required', 'integer', 'exists:departamentos,id'],
            'cargo_id' => ['required', 'integer', 'exists:cargos,id'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],

            // Dados Pessoais
            'nome_completo' => ['required', 'string', 'max:255'],
            'cpf' => ['required', 'string', 'size:14', 'regex:/^\d{3}\.\d{3}\.\d{3}-\d{2}$/',
            Rule::unique('funcionario_documentos', 'cpf')],
            'rg' => ['required', 'string', 'max:20'],
            'orgao_expedidor_rg' => ['nullable', 'string', 'max:10'],
            'data_nascimento' => ['required', 'date', 'before:today'],
            'estado_civil' => ['required', Rule::in(['solteiro', 'casado', 'divorciado', 'viuvo', 'uniao_estavel'])],
            'genero' => ['required', Rule::in(['masculino', 'feminino', 'outro'])],
            'nacionalidade' => ['required', 'string', 'max:50'],
            'naturalidade' => ['nullable', 'string', 'max:100'],

            // Contato
            'telefone' => ['required', 'string', 'max:15'],
            'celular' => ['required', 'string', 'max:15'],
            'email' => ['required', 'email', Rule::unique('funcionario_contatos', 'email')],
            'email_pessoal' => ['nullable', 'email'],

            // Endereço
            'cep' => ['required', 'string', 'regex:/^\d{5}-\d{3}$/'],
            'logradouro' => ['required', 'string', 'max:255'],
            'numero' => ['required', 'string', 'max:10'],
            'complemento' => ['nullable', 'string', 'max:100'],
            'bairro' => ['required', 'string', 'max:100'],
            'cidade' => ['required', 'string', 'max:100'],
            'estado' => ['required', 'string', 'size:2'],

            // Dados Trabalhistas
            'ctps_numero' => ['required', 'string', 'max:20'],
            'ctps_serie' => ['required', 'string', 'max:10'],
            'ctps_uf' => ['required', 'string', 'size:2'],
            'ctps_data_emissao' => ['required', 'date', 'before_or_equal:today'],
            'pis_pasep' => ['required', 'string', 'max:15'],
            'titulo_eleitor' => ['nullable', 'string', 'max:15'],
            'certificado_reservista' => ['nullable', 'string', 'max:20'],

            // Contrato
            'data_admissao' => ['required', 'date', 'before_or_equal:today'],
            'tipo_contratacao' => ['required', Rule::in(['clt', 'pj', 'autonomo', 'avulso', 'estatutario'])],
            'tipo_contrato' => ['required', Rule::in(['indeterminado', 'determinado', 'experiencia', 'intermitente', 'temporario', 'aprendiz', 'estagio'])],
            
            'local_trabalho' => ['nullable', 'string', 'max:255'],
            
            // Remuneração
            'tipo_remuneracao' => ['required', Rule::in(['mensal', 'diaria', 'horaria'])],
            'salario_base' => ['nullable', 'numeric', 'min:0', 'required_if:tipo_remuneracao,mensal'],
            'valor_diaria' => ['nullable', 'numeric', 'min:0', 'required_if:tipo_remuneracao,diaria'],
            'valor_hora'   => ['nullable', 'numeric', 'min:0', 'required_if:tipo_remuneracao,horaria'],
            'eh_diarista' => ['sometimes', 'boolean'],

            // Jornada de Trabalho/ Horários
            'carga_horaria_semanal' => ['required', 'integer', 'min:1', 'max:44'],
            'horario_entrada' => ['required', 'date_format:H:i'],
            'horario_saida' => ['required', 'date_format:H:i'],
            'horario_almoco_inicio' => ['required', 'date_format:H:i'],
            'horario_almoco_fim' => ['required', 'date_format:H:i'],
            
            // Benefícios
            'vale_transporte' => ['sometimes', 'boolean'],
            'valor_vale_transporte' => ['nullable', 'numeric', 'min:0'],
            'vale_alimentacao' => ['sometimes', 'boolean'],
            'valor_vale_alimentacao' => ['nullable', 'numeric', 'min:0'],
            'plano_saude' => ['sometimes', 'boolean'],
            'plano_odontologico' => ['sometimes', 'boolean'],

            // Dados Bancários
            'banco_codigo' => ['required', 'string', 'max:5'],
            'banco_nome' => ['required', 'string', 'max:100'],
            'agencia' => ['required', 'string', 'max:10'],
            'conta' => ['required', 'string', 'max:15'],
            'tipo_conta' => ['required', Rule::in(['corrente', 'poupanca'])],

            // Dependentes
            'qtd_dependentes_ir' => ['required', 'integer', 'min:0', 'max:10'],
            'qtd_dependentes_salario_familia' => ['required', 'integer', 'min:0', 'max:10'],

            // Observações
            'observacoes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nome_completo' => 'nome completo',
            'cpf' => 'CPF',
            'rg' => 'RG',
            'orgao_expedidor_rg' => 'órgão expedidor do RG',
            'data_nascimento' => 'data de nascimento',
            'estado_civil' => 'estado civil',
            'genero' => 'gênero',
            'departamento_id' => 'departamento',
            'cargo_id' => 'cargo',
            'data_admissao' => 'data de admissão',
            'salario_base' => 'salário base',
            'carga_horaria_semanal' => 'carga horária semanal',
            'ctps_numero' => 'número da CTPS',
            'ctps_serie' => 'série da CTPS',
            'ctps_uf' => 'UF da CTPS',
            'ctps_data_emissao' => 'data de emissão da CTPS',
            'pis_pasep' => 'PIS/PASEP',
            'banco_codigo' => 'código do banco',
            'banco_nome' => 'nome do banco',
            'tipo_conta' => 'tipo da conta',
            'qtd_dependentes_ir' => 'quantidade de dependentes para IR',
            'qtd_dependentes_salario_familia' => 'quantidade de dependentes para salário família',
        ];
    }

    public function messages(): array
    {
        return [
            'cpf.regex' => 'O CPF deve estar no formato XXX.XXX.XXX-XX',
            'cep.regex' => 'O CEP deve estar no formato XXXXX-XXX',
            'data_nascimento.before' => 'A data de nascimento deve ser anterior a hoje',
            'data_admissao.before_or_equal' => 'A data de admissão não pode ser futura',
        ];
    }
}