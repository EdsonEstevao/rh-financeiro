<?php
// app/Http/Requests/RH/UpdateFaixaIrrfRequest.php

namespace App\Http\Requests\RH;

use Illuminate\Foundation\Http\FormRequest;


class UpdateFaixaIrrfRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('faixa-irrf.update');
    }

    public function rules(): array
    {
        return [
            'numero_faixa'       => ['required', 'integer', 'min:1'],
            'limite_inferior'    => ['required', 'numeric', 'min:0'],
            'limite_superior'    => ['nullable', 'numeric', 'gt:limite_inferior'],
            'aliquota'           => ['required', 'numeric', 'min:0', 'max:100'],
            'parcela_deduzir'    => ['required', 'numeric', 'min:0'],
            'deducao_dependente' => ['required', 'numeric', 'min:0'],
            'vigencia_inicio'    => ['required', 'date'],
            'vigencia_fim'       => ['nullable', 'date', 'after:vigencia_inicio'],
            'ativa'              => ['boolean'],
        ];
    }
}
