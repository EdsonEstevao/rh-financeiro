<?php

namespace App\Http\Requests\RH\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CalcularIrrfRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'funcionario_id' => ['required', 'integer', 'exists:funcionarios,id'],
            'salario' => ['required', 'numeric', 'min:0'],
            'inss' => ['required', 'numeric', 'min:0'],
            'dependentes' => ['sometimes', 'integer', 'min:0'],
            'competencia' => ['required', 'string', 'size:7'],
        ];
    }
}