<?php

namespace App\Models\Domain\RH;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FuncionarioEndereco extends Model
{
    protected $table = 'funcionario_enderecos';

    protected $fillable = [
        'funcionario_id',
        'cep',
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'estado',
    ];

    public function funcionario(): BelongsTo
    {
        return $this->belongsTo(Funcionario::class);
    }
}
