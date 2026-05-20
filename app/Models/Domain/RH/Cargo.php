<?php

namespace App\Models\Domain\RH;

use Illuminate\Database\Eloquent\{Builder, Model};
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Support\LogOptions;

/**
 * @property int $id
 * @property string $titulo
 * @property bool $ativo
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Domain\RH\Funcionario> $funcionarios
 * @property-read int|null $funcionarios_count
 * @method static Builder<static>|Cargo ativos()
 * @method static Builder<static>|Cargo newModelQuery()
 * @method static Builder<static>|Cargo newQuery()
 * @method static Builder<static>|Cargo query()
 * @method static Builder<static>|Cargo whereAtivo($value)
 * @method static Builder<static>|Cargo whereCreatedAt($value)
 * @method static Builder<static>|Cargo whereId($value)
 * @method static Builder<static>|Cargo whereTitulo($value)
 * @method static Builder<static>|Cargo whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Cargo extends Model
{
    protected $table = 'cargos';

    protected $fillable = ['titulo', 'ativo'];

    protected function casts(): array
    {
        return ['ativo' => 'boolean'];
    }

     public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['titulo', 'ativo'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    public function funcionarios(): HasMany
    {
        return $this->hasMany(Funcionario::class, 'cargo_id');
    }

    public function scopeAtivos(Builder $query)
    {
        return $query->where('ativo', true);
    }
}