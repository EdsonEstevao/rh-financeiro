<?php

namespace App\Models\Domain\RH;

use Illuminate\Database\Eloquent\{Builder, Model};
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $funcionario_id
 * @property string $nome_completo
 * @property \Illuminate\Support\Carbon $data_nascimento
 * @property string $parentesco
 * @property bool $invalido
 * @property bool $ativo
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Domain\RH\Funcionario $funcionario
 * @property-read int $idade
 * @property-read bool $tem_direito_salario_familia
 * @method static Builder<static>|Dependente ativos()
 * @method static Builder<static>|Dependente comDireitoSalarioFamilia()
 * @method static Builder<static>|Dependente invalidos()
 * @method static Builder<static>|Dependente menoresDe14Anos()
 * @method static Builder<static>|Dependente newModelQuery()
 * @method static Builder<static>|Dependente newQuery()
 * @method static Builder<static>|Dependente query()
 * @method static Builder<static>|Dependente whereAtivo($value)
 * @method static Builder<static>|Dependente whereCreatedAt($value)
 * @method static Builder<static>|Dependente whereDataNascimento($value)
 * @method static Builder<static>|Dependente whereFuncionarioId($value)
 * @method static Builder<static>|Dependente whereId($value)
 * @method static Builder<static>|Dependente whereInvalido($value)
 * @method static Builder<static>|Dependente whereNomeCompleto($value)
 * @method static Builder<static>|Dependente whereParentesco($value)
 * @method static Builder<static>|Dependente whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Dependente extends Model
{
    protected $table = 'dependentes';

    protected $fillable = [
        'funcionario_id',
        'nome_completo',
        'data_nascimento',
        'parentesco',
        'invalido',
        'ativo',
    ];

    protected $casts = [
        'data_nascimento' => 'date',
        'invalido' => 'boolean',
        'ativo' => 'boolean',
    ];

    public function funcionario(): BelongsTo
    {
        return $this->belongsTo(Funcionario::class);
    }

    /**
     * Scope: Dependentes ativos
     */
    public function scopeAtivos(Builder $query): Builder
    {
        return $query->where('ativo', true);
    }

    /**
     * Scope: Dependentes menores de 14 anos
     */
    public function scopeMenoresDe14Anos(Builder $query): Builder
    {
        $dataLimite = now()->subYears(14)->format('Y-m-d');

        return $query->whereDate('data_nascimento', '>', $dataLimite);
    }

    /**
     * Scope: Dependentes inválidos
     */
    public function scopeInvalidos(Builder $query): Builder
    {
        return $query->where('invalido', true);
    }

    /**
     * Scope: Dependentes com direito a salário família
     * - Menores de 14 anos OU inválidos
     * - Ativos
     */
    public function scopeComDireitoSalarioFamilia(Builder $query): Builder
    {
        $dataLimite = now()->subYears(14)->format('Y-m-d');

        return $query->where('ativo', true)
            ->where(function($q) use ($dataLimite) {
                $q->whereDate('data_nascimento', '>', $dataLimite) // Menor de 14 anos
                  ->orWhere('invalido', true); // Ou inválido de qualquer idade
            });
    }

    /**
     * Accessor: Idade do dependente
     */
    public function getIdadeAttribute(): int
    {
        return $this->data_nascimento?->age ?? 0;
    }

    /**
     * Accessor: Tem direito a salário família?
     */
    public function getTemDireitoSalarioFamiliaAttribute(): bool
    {
        if (!$this->ativo) {
            return false;
        }

        return $this->invalido || $this->idade < 14;
    }
}
