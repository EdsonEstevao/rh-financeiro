<?php

namespace App\Http\Requests\RH;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FolhaPagamentoRequest extends FormRequest
{
    public function authorize(): bool
    {
        // return true; // permissão já tratada no Controller via middleware
        return $this->user()?->can('folha.generate') ?? false;
    }

    public function rules(): array
    {
        return [
            'funcionario_id'           => ['required', 'exists:funcionarios,id'],
            'competencia'              => ['required', 'date_format:Y-m-d'],
            'salario_base'             => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'gratificacao_feriado'     => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            // 'dsr_hora_extra'           => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'salario_familia_hr_extra' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'arredondamento_provento'  => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
            'desconto_inss'            => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'vale_dia_20'              => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'vale_extra'               => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'faltas_valor'             => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'dsr_faltas'               => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'arredondamento_desconto'  => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
            'quinto_dia_util'          => ['nullable', 'date'],
            'observacao'               => ['nullable', 'string', 'max:1000'],
            'status'                   => ['required', Rule::in(['pendente', 'processado', 'pago'])],
        ];
    }

    public function attributes(): array
    {
        return [
            'funcionario_id'           => 'funcionário',
            'competencia'              => 'competência',
            'salario_base'             => 'salário base',
            'gratificacao_feriado'     => 'gratificação de feriado',
            // 'dsr_hora_extra'           => 'DSR hora extra',
            'salario_familia_hr_extra' => 'salário família / hora extra',
            'arredondamento_provento'  => 'arredondamento de provento',
            'desconto_inss'            => 'desconto INSS',
            'vale_dia_20'              => 'vale dia 20',
            'vale_extra'               => 'vale extra',
            'faltas_valor'             => 'valor de faltas',
            'dsr_faltas'               => 'DSR faltas',
            'arredondamento_desconto'  => 'arredondamento de desconto',
            'quinto_dia_util'          => 'quinto dia útil',
            'observacao'               => 'observação',
            'status'                   => 'status',
        ];
    }

    // Converte vírgula para ponto nos campos decimais antes de validar
    protected function prepareForValidation(): void
    {
        $campos = [
            'salario_base', 'gratificacao_feriado', 'dsr_hora_extra',
            'salario_familia_hr_extra', 'arredondamento_provento',
            'desconto_inss', 'vale_dia_20', 'vale_extra',
            'faltas_valor', 'dsr_faltas', 'arredondamento_desconto',
        ];

        $dados = [];
        foreach ($campos as $campo) {
            if ($this->has($campo)) {
                $dados[$campo] = str_replace(',', '.', $this->input($campo));
            }
        }

        $this->merge($dados);
    }
}
