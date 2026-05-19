<?php

namespace App\Models\Domain\RH;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
// use Spatie\Activitylog\LogOptions;
// use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;


/**
 * @property int $id
 * @property int $folha_pagamento_id
 * @property int $funcionario_id
 * @property numeric $salario_bruto
 * @property numeric $inss_base
 * @property numeric $inss_valor
 * @property numeric|null $inss_aliquota_aplicada
 * @property numeric $irrf_valor
 * @property numeric $vt_valor
 * @property numeric $outros_descontos
 * @property numeric $salario_liquido
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activitiesAsSubject
 * @property-read int|null $activities_as_subject_count
 * @property-read \App\Models\Domain\RH\FolhaPagamento $folha
 * @property-read \App\Models\Domain\RH\Funcionario $funcionario
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Holerite newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Holerite newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Holerite query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Holerite whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Holerite whereFolhaPagamentoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Holerite whereFuncionarioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Holerite whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Holerite whereInssAliquotaAplicada($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Holerite whereInssBase($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Holerite whereInssValor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Holerite whereIrrfValor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Holerite whereOutrosDescontos($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Holerite whereSalarioBruto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Holerite whereSalarioLiquido($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Holerite whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Holerite whereVtValor($value)
 * @mixin \Eloquent
 */
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
