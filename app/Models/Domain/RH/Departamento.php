<?php

namespace App\Models\Domain\RH;

use Illuminate\Database\Eloquent\{Builder, Model};
use Illuminate\Database\Eloquent\Relations\HasMany;

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