<?php

namespace App\Models\Domain\RH;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $funcionario_id
 * @property string $banco_codigo
 * @property string $banco_nome
 * @property string $agencia
 * @property string $conta
 * @property string $tipo_conta
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Domain\RH\Funcionario $funcionario
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioDadosBancarios newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioDadosBancarios newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioDadosBancarios query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioDadosBancarios whereAgencia($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioDadosBancarios whereBancoCodigo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioDadosBancarios whereBancoNome($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioDadosBancarios whereConta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioDadosBancarios whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioDadosBancarios whereFuncionarioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioDadosBancarios whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioDadosBancarios whereTipoConta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioDadosBancarios whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
