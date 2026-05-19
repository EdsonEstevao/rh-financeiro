<?php

namespace App\Models\Domain\RH;

use Illuminate\Database\Eloquent\{Builder, Model};
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $nome
 * @property bool $ativo
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Domain\RH\Funcionario> $funcionarios
 * @property-read int|null $funcionarios_count
 * @method static Builder<static>|Departamento ativos()
 * @method static Builder<static>|Departamento newModelQuery()
 * @method static Builder<static>|Departamento newQuery()
 * @method static Builder<static>|Departamento query()
 * @method static Builder<static>|Departamento whereAtivo($value)
 * @method static Builder<static>|Departamento whereCreatedAt($value)
 * @method static Builder<static>|Departamento whereId($value)
 * @method static Builder<static>|Departamento whereNome($value)
 * @method static Builder<static>|Departamento whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Departamento extends Model
{
    protected $table = 'departamentos';

    protected $fillable = ['nome', 'ativo'];

    protected function casts(): array
    {
        return ['ativo' => 'boolean'];
    }

    public function funcionarios(): HasMany
    {
        return $this->hasMany(Funcionario::class, 'departamento_id');
    }

    public function scopeAtivos(Builder $query)
    {
        return $query->where('ativo', true);
    }
}