<?php
// app/Http/Requests/RH/UpdateFaixaInssRequest.php

namespace App\Http\Requests\RH;

use Illuminate\Foundation\Http\FormRequest;


class UpdateFaixaInssRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'valor' => 'required|numeric',
            'faixa' => 'required|numeric',
        ];
    }
}
