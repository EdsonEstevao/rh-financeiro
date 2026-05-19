<?php

namespace App\Models\Domain\RH;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $funcionario_id
 * @property string $cep
 * @property string $logradouro
 * @property string $numero
 * @property string|null $complemento
 * @property string $bairro
 * @property string $cidade
 * @property string $estado
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Domain\RH\Funcionario $funcionario
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioEndereco newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioEndereco newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioEndereco query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioEndereco whereBairro($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioEndereco whereCep($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioEndereco whereCidade($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioEndereco whereComplemento($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioEndereco whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioEndereco whereEstado($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioEndereco whereFuncionarioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioEndereco whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioEndereco whereLogradouro($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioEndereco whereNumero($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioEndereco whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
