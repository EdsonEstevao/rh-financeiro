<?php

namespace App\Http\Requests\RH;

use Illuminate\Foundation\Http\FormRequest;

class StorePeriodoFeriasRequest extends FormRequest
{
    public function authorize(): bool
    {

        // permissão já tratada no Controller via middleware
        return $this->user()?->can('ferias.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'data_inicio'       => 'required|date',
            'data_fim'          => 'required|date|after_or_equal:data_inicio',
            'tipo'              => 'sometimes|in:prevista,programada,efetiva',
            'status'            => 'sometimes|in:planejada,aprovada,gozada,cancelada',
            'abono_pecuniario'  => 'nullable|boolean',
            'observacao'        => 'nullable|string|max:500',
            'numero_periodo'    => 'nullable|integer|min:1',
            'ferias_vencimento' => 'nullable|date|after:data_fim',
        ];
    }

    public function messages(): array
    {
        return [
            'data_inicio.required'           => 'A data de início é obrigatória.',
            'data_fim.required'              => 'A data de fim é obrigatória.',
            'data_fim.after_or_equal'        => 'A data final deve ser maior ou igual à data inicial.',
            'ferias_vencimento.after'        => 'O vencimento deve ser após o fim das férias.',
            'tipo.in'                        => 'Tipo de período inválido.',
            'status.in'                      => 'Status inválido.',
            'numero_periodo.min'             => 'O número do período deve ser pelo menos 1.',
        ];
    }
}
