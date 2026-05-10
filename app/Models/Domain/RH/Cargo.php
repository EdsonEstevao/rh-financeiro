<?php

namespace App\Models\Domain\RH;

use Illuminate\Database\Eloquent\{Builder, Model};
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cargo extends Model
{
    protected $table = 'cargos';

    protected $fillable = ['titulo', 'ativo'];

    protected function casts(): array
    {
        return ['ativo' => 'boolean'];
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
