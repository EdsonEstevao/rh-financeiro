<?php

namespace App\Http\Requests\RH\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CalcularInssRequest extends FormRequest
{
    public function authorize(): bool
    {
        // return auth()->check();
        // return Auth::check() && Auth::user()->can('rh calcular folha');
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'funcionario_id' => ['required', 'integer', 'exists:funcionarios,id'],
            'salario' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'competencia' => ['nullable', 'date_format:Y-m'],
        ];
    }

    public function attributes(): array
    {
        return [
            'salario' => 'salário',
            'competencia' => 'competência',
        ];
    }

    public function messages(): array
    {
        return [
            'salario.required' => 'O salário é obrigatório para o cálculo.',
            'salario.numeric' => 'O salário deve ser um valor numérico.',
            'salario.min' => 'O salário não pode ser negativo.',
            'competencia.date_format' => 'A competência deve estar no formato AAAA-MM.',
        ];
    }
}
