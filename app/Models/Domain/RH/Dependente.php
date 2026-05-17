<?php

namespace App\Models\Domain\RH;

use Illuminate\Database\Eloquent\{Builder, Model};
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
