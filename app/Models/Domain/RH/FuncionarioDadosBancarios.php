<?php

namespace App\Models\Domain\RH;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FuncionarioDadosBancarios extends Model
{
    protected $table = 'funcionario_dados_bancarios';

    protected $fillable = [
        'funcionario_id',
        'banco_codigo',
        'banco_nome',
        'agencia',
        'conta',
        'tipo_conta',
    ];

    public function funcionario(): BelongsTo
    {
        return $this->belongsTo(Funcionario::class);
    }
}
