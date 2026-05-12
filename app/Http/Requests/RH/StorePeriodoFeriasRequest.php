<?php

namespace App\Http\Requests\RH;

use Illuminate\Foundation\Http\FormRequest;

class StorePeriodoFeriasRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Ajuste para suas policies/permissions
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'data_inicio' => ['required', 'date'],
            'data_fim'    => ['required', 'date', 'after_or_equal:data_inicio'],
            'tipo'        => ['nullable', 'in:prevista,programada,efetiva'],
            'status'      => ['nullable', 'in:planejada,aprovada,gozada,cancelada'],
            'abono_pecuniario' => ['nullable', 'boolean'],
            'observacao'  => ['nullable', 'string', 'max:2000'],
            'numero_periodo' => ['nullable', 'integer', 'min:1', 'max:3'],
        ];
    }

    public function messages(): array
    {
        return [
            'data_fim.after_or_equal' => 'A data final deve ser maior ou igual à data inicial.',
            'tipo.in' => 'O tipo deve ser: prevista, programada ou efetiva.',
        ];
    }
}
