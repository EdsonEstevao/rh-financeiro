<?php

namespace App\Models\Domain\RH;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
// use Spatie\Activitylog\LogOptions;
// use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Holerite extends Model
{
    use LogsActivity;

    protected $fillable = [
        'folha_pagamento_id',
        'funcionario_id',
        'salario_bruto',
        'inss_base',
        'inss_valor',
        'inss_aliquota_aplicada',
        'irrf_valor',
        'vt_valor',
        'outros_descontos',
        'salario_liquido',
    ];

    protected $casts = [
        'salario_bruto' => 'decimal:2',
        'inss_base' => 'decimal:2',
        'inss_valor' => 'decimal:2',
        'inss_aliquota_aplicada' => 'decimal:4',
        'irrf_valor' => 'decimal:2',
        'vt_valor' => 'decimal:2',
        'outros_descontos' => 'decimal:2',
        'salario_liquido' => 'decimal:2',
    ];

    public function folha(): BelongsTo
    {
        return $this->belongsTo(FolhaPagamento::class, 'folha_pagamento_id');
    }

    public function funcionario(): BelongsTo
    {
        return $this->belongsTo(Funcionario::class, 'funcionario_id');
    }

    /**
     * Calcula total de descontos
     */
    public function totalDescontos(): float
    {
        return $this->inss_valor + $this->irrf_valor + $this->vt_valor + $this->outros_descontos;
    }

    /**
     * Activity Log
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['salario_bruto', 'inss_valor', 'irrf_valor', 'salario_liquido'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
            // ->dontSubmitEmptyLogs();
    }
}
