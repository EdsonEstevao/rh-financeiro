<?php

namespace App\Models\Domain\RH;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $funcionario_id
 * @property \Illuminate\Support\Carbon $data
 * @property numeric $valor
 * @property string $status
 * @property string|null $observacao
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Domain\RH\Funcionario $funcionario
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Diaria newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Diaria newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Diaria query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Diaria whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Diaria whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Diaria whereFuncionarioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Diaria whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Diaria whereObservacao($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Diaria whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Diaria whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Diaria whereValor($value)
 * @mixin \Eloquent
 */
class Diaria extends Model
{
    /**
     * 🔨 Como cadastrar/baixar as diárias
     * Painel do funcionário: RH ou gerente lança uma diária (funcionario, data, valor [sugere do cadastro], status).
     * Aprovação/pagamento: workflow para RH aprova/paga.
     * Para gerar a folha do diarista: seguro filtrar somente diárias status = 'aprovada' ou 'paga' no mês vigente para somar valores.
     */
    protected $fillable = [
        'funcionario_id', 'data', 'valor', 'status', 'observacao'
    ];

    protected $casts = [
        'data' => 'date',
    ];

    public function funcionario()
    {
        return $this->belongsTo(Funcionario::class, 'funcionario_id');
    }
}