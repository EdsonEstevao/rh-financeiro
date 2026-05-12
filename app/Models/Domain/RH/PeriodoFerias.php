<?php

namespace App\Models\Domain\RH;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Schema\Builder;

class PeriodoFerias extends Model
{
    protected $table = 'periodos_ferias';

    protected $fillable = [
        'funcionario_id',
        'data_inicio',
        'data_fim',
        'tipo',
        'status',
        'abono_pecuniario',
        'observacao',
        'numero_periodo',
    ];

    protected $casts = [
        'data_inicio' => 'date',
        'data_fim'    => 'date',
        'abono_pecuniario' => 'boolean',
    ];

    public function funcionario(): BelongsTo
    {
        return $this->belongsTo(Funcionario::class, 'funcionario_id');
    }

    /**
     * Calcula os dias corridos do período (inclusivo).
     */
    public function diasCorridos(): int
    {
        return $this->data_inicio->diffInDays($this->data_fim) + 1;
    }

    /**
     * Scopes úteis
     */
    public function scopePrevistas(Builder $query)
    {
        return $query->where('tipo', 'prevista');
    }

    public function scopeProgramadas(Builder $query)
    {
        return $query->where('tipo', 'programada');
    }

    public function scopeEfetivas(Builder $query)
    {
        return $query->where('tipo', 'efetiva');
    }
}