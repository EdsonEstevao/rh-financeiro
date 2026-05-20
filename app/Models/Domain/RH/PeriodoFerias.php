<?php

namespace App\Models\Domain\RH;

use Illuminate\Database\Eloquent\{Builder, Model};
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\Support\LogOptions;

/**
 * @property int $id
 * @property int $funcionario_id
 * @property Carbon $data_inicio
 * @property Carbon $data_fim
 * @property string $tipo prevista=gerada na admissão; programada=agendada pelo RH; efetiva=já gozada
 * @property bool $abono_pecuniario
 * @property string $status
 * @property string|null $observacao
 * @property int $numero_periodo
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \App\Models\Domain\RH\Funcionario $funcionario
 * @method static Builder<static>|PeriodoFerias efetivas()
 * @method static Builder<static>|PeriodoFerias newModelQuery()
 * @method static Builder<static>|PeriodoFerias newQuery()
 * @method static Builder<static>|PeriodoFerias previstas()
 * @method static Builder<static>|PeriodoFerias programadas()
 * @method static Builder<static>|PeriodoFerias query()
 * @method static Builder<static>|PeriodoFerias whereAbonoPecuniario($value)
 * @method static Builder<static>|PeriodoFerias whereCreatedAt($value)
 * @method static Builder<static>|PeriodoFerias whereDataFim($value)
 * @method static Builder<static>|PeriodoFerias whereDataInicio($value)
 * @method static Builder<static>|PeriodoFerias whereFuncionarioId($value)
 * @method static Builder<static>|PeriodoFerias whereId($value)
 * @method static Builder<static>|PeriodoFerias whereNumeroPeriodo($value)
 * @method static Builder<static>|PeriodoFerias whereObservacao($value)
 * @method static Builder<static>|PeriodoFerias whereStatus($value)
 * @method static Builder<static>|PeriodoFerias whereTipo($value)
 * @method static Builder<static>|PeriodoFerias whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'data_inicio', 'data_fim'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

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
    public function scopeDiasCorridos(): int
    {
        return Carbon::parse($this->data_inicio)
            ->diffInDays(Carbon::parse($this->data_fim)) + 1;
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