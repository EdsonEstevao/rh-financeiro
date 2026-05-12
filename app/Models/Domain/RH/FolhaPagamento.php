<?php

namespace App\Models\Domain\RH;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Schema\Builder;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class FolhaPagamento extends Model
{
    use LogsActivity;

    protected $table = 'folhas_pagamento';

    protected $fillable = [
        'competencia',
        'status',
    ];

    protected $casts = [
        'competencia' => 'date',
    ];

    public function holerites(): HasMany
    {
        return $this->hasMany(Holerite::class, 'folha_pagamento_id');
    }

    /**
     * Totalizadores da folha
     */
    public function totalSalarioBruto(): float
    {
        return $this->holerites()->sum('salario_bruto');
    }

    public function totalInss(): float
    {
        return $this->holerites()->sum('inss_valor');
    }

    public function totalIrrf(): float
    {
        return $this->holerites()->sum('irrf_valor');
    }

    public function totalSalarioLiquido(): float
    {
        return $this->holerites()->sum('salario_liquido');
    }

    /**
     * Scopes
     */
    public function scopeAbertas(Builder $query)
    {
        return $query->where('status', 'aberta');
    }

    public function scopeFechadas(Builder $query)
    {
        return $query->where('status', 'fechada');
    }

    /**
     * Activity Log
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['competencia', 'status'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
            // ->dontSubmitEmptyLogs();
    }
}