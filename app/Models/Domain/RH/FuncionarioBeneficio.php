<?php

namespace App\Models\Domain\RH;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FuncionarioBeneficio extends Model
{
    protected $table = 'funcionario_beneficios';

    protected $fillable = [
        'funcionario_id',
        'vale_transporte',
        'valor_vale_transporte',
        'vale_alimentacao',
        'valor_vale_alimentacao',
        'plano_saude',
        'plano_odontologico',
        'sexto_dia_util_mes',
    ];

    protected $casts = [
        'vale_transporte' => 'boolean',
        'vale_alimentacao' => 'boolean',
        'plano_saude' => 'boolean',
        'plano_odontologico' => 'boolean',
        'sexto_dia_util_mes' => 'boolean',
        'valor_vale_transporte' => 'decimal:2',
        'valor_vale_alimentacao' => 'decimal:2',
    ];

    public function funcionario(): BelongsTo
    {
        return $this->belongsTo(Funcionario::class);
    }
}
