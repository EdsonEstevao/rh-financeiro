<?php

namespace App\Models\Domain\RH;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $funcionario_id
 * @property bool $vale_transporte
 * @property numeric|null $valor_vale_transporte
 * @property bool $vale_alimentacao
 * @property numeric|null $valor_vale_alimentacao
 * @property bool $plano_saude
 * @property bool $plano_odontologico
 * @property bool $sexto_dia_util_mes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Domain\RH\Funcionario $funcionario
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioBeneficio newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioBeneficio newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioBeneficio query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioBeneficio whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioBeneficio whereFuncionarioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioBeneficio whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioBeneficio wherePlanoOdontologico($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioBeneficio wherePlanoSaude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioBeneficio whereSextoDiaUtilMes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioBeneficio whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioBeneficio whereValeAlimentacao($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioBeneficio whereValeTransporte($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioBeneficio whereValorValeAlimentacao($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioBeneficio whereValorValeTransporte($value)
 * @mixin \Eloquent
 */
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
