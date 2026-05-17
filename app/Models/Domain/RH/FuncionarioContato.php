<?php

namespace App\Models\Domain\RH;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FuncionarioContato extends Model
{
    protected $table = 'funcionario_contatos';

    protected $fillable = [
        'funcionario_id',
        'telefone',
        'celular',
        'email',
        'email_pessoal',
    ];

    protected $casts = [
        'telefone' => 'string',
        'celular' => 'string',
        'email' => 'string',
        'email_pessoal' => 'string',
    ];

    /**
     * Relacionamento com Funcionario
     */
    public function funcionario(): BelongsTo
    {
        return $this->belongsTo(Funcionario::class);
    }
}
