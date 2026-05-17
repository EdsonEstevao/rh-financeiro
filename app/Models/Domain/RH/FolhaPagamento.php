<?php

namespace App\Models\Domain\RH;

use Illuminate\Database\Eloquent\{Builder, Model};
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};
use Illuminate\Support\Carbon;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class FolhaPagamento extends Model
{
    use LogsActivity;

    protected $table = 'folha_pagamentos';

    protected $fillable = [
        'funcionario_id',
        'competencia',
        'salario_base',
        'gratificacao_feriado',
        'horas_extras_totais',
        'valor_hora_extra',
        // 'dsr_hora_extra',
        'salario_familia_hr_extra',
        'arredondamento_provento',
        'desconto_inss',
        'vale_dia_20',
        'vale_extra',
        'faltas_valor',
        'dsr_faltas',
        'arredondamento_desconto',
        'quinto_dia_util',
        'observacao',
        'status',
    ];

    protected $casts = [
        'competencia'              => 'date',
        'quinto_dia_util'          => 'date',
        'salario_base'             => 'decimal:2',
        'horas_extras_totais'      => 'decimal:2',
        'valor_hora_extra'         => 'decimal:2',
        'gratificacao_feriado'     => 'decimal:2',
        // 'dsr_hora_extra'           => 'decimal:2',
        'salario_familia_hr_extra' => 'decimal:2',
        'arredondamento_provento'  => 'decimal:2',
        'desconto_inss'            => 'decimal:2',
        'vale_dia_20'              => 'decimal:2',
        'vale_extra'               => 'decimal:2',
        'faltas_valor'             => 'decimal:2',
        'dsr_faltas'               => 'decimal:2',
        'arredondamento_desconto'  => 'decimal:2',
    ];

    protected $appends = ['dsr_hora_extra'];

    // ─── RELACIONAMENTOS ─────────────────────────────────────────

    // No Model FolhaPagamento, adicione:

    public function lancamentos(): HasMany
    {
        return $this->hasMany(FolhaLancamento::class);
    }

    // Folha de lançamento
    // public function lancamento(): HasOne
    // {
    //     return $this->hasOne(FolhaLancamento::class);
    // }



    /**
     * Recalcula totais baseado nos lançamentos
     */
    public function recalcularTotais(): void
    {
        $proventos = $this->lancamentos()->proventos()->sum('valor_total');
        $descontos = $this->lancamentos()->descontos()->sum('valor_total');

        $this->update([
            'total_proventos' => $proventos,
            'total_descontos' => $descontos,
            'salario_liquido' => $proventos - $descontos,
        ]);
    }

    /**
     * Obtém total de horas extras (quantidade)
     */
    public function getTotalHorasExtrasQuantidadeAttribute(): float
    {
        return $this->lancamentos()
            ->whereIn('tipo', [
                FolhaLancamento::TIPO_HORA_EXTRA_NORMAL,
                FolhaLancamento::TIPO_HORA_EXTRA_SABADO,
                FolhaLancamento::TIPO_HORA_EXTRA_FERIADO,
            ])
            ->sum('quantidade');
    }

    /**
     * Obtém total de horas extras (valor)
     */
    public function getTotalHorasExtrasValorAttribute(): float
    {
        return $this->lancamentos()
            ->whereIn('tipo', [
                FolhaLancamento::TIPO_HORA_EXTRA_NORMAL,
                FolhaLancamento::TIPO_HORA_EXTRA_SABADO,
                FolhaLancamento::TIPO_HORA_EXTRA_FERIADO,
            ])
            ->sum('valor_total');
    }

    public function funcionario(): BelongsTo
    {
        return $this->belongsTo(Funcionario::class);
    }


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


    protected function salarioLiquidoNew(): Attribute
    {
        return Attribute::make(
            get: fn () => round($this->total_proventos - $this->total_descontos, 2)
        );
    }


    /**
     * Salário Líquido — calculado, não armazenado
     */
    protected function salarioLiquido(): Attribute
    {
        return Attribute::make(
            get: function () {
                $proventos =
                    ($this->salario_base               ?? 0) +
                    ($this->gratificacao_feriado       ?? 0) +
                    // ($this->dsr_hora_extra             ?? 0) +
                    ($this->horas_extras_totais        ?? 0) *
                    ($this->valor_hora_extra           ?? 0) +
                    ($this->salario_familia_hr_extra   ?? 0) +
                    ($this->arredondamento_provento    ?? 0);

                $descontos =
                    ($this->desconto_inss              ?? 0) +
                    ($this->vale_dia_20                ?? 0) +
                    ($this->vale_extra                 ?? 0) +
                    ($this->faltas_valor               ?? 0) +
                    ($this->dsr_faltas                 ?? 0) +
                    ($this->arredondamento_desconto    ?? 0);

                return round($proventos - $descontos, 2);
            }
        );
    }

    // ─── ACCESSORS CALCULADOS (não armazenados no banco) ─────────

    protected function totalProventosNew(): Attribute
    {
        return Attribute::make(
            get: fn () => round(
                ($this->salario_base               ?? 0) +
                ($this->gratificacao_feriado       ?? 0) +
                // ($this->dsr_hora_extra             ?? 0) +
                ($this->horas_extras_totais        ?? 0) *
                ($this->salario_familia_hr_extra   ?? 0) +
                ($this->arredondamento_provento    ?? 0),
                2
            )
        );
    }

    /**
     * Total de Proventos — também calculado
     */
    protected function totalProventos(): Attribute
    {
        return Attribute::make(
            get: fn () => round(
                ($this->salario_base               ?? 0) +
                ($this->gratificacao_feriado       ?? 0) +
                // ($this->dsr_hora_extra             ?? 0) +
                ($this->horas_extras_totais        ?? 0) *
                ($this->salario_familia_hr_extra   ?? 0) +
                ($this->arredondamento_provento    ?? 0),
                2
            )
        );
    }

    protected function totalDescontosNew(): Attribute
    {
        return Attribute::make(
            get: fn () => round(
                ($this->desconto_inss              ?? 0) +
                ($this->vale_dia_20                ?? 0) +
                ($this->vale_extra                 ?? 0) +
                ($this->faltas_valor               ?? 0) +
                ($this->dsr_faltas                 ?? 0) +
                ($this->arredondamento_desconto    ?? 0),
                2
            )
        );
    }
    /**
     * Total de Descontos — também calculado
     */
    protected function totalDescontos(): Attribute
    {
        return Attribute::make(
            get: fn () => round(
                ($this->desconto_inss              ?? 0) +
                ($this->vale_dia_20                ?? 0) +
                ($this->vale_extra                 ?? 0) +
                ($this->faltas_valor               ?? 0) +
                ($this->dsr_faltas                 ?? 0) +
                ($this->arredondamento_desconto    ?? 0),
                2
            )
        );
    }

    protected function dsrHoraExtra(): Attribute
    {
        return Attribute::make(
            get: function () {
                // Se não tem horas extras ou valor, retorna 0
                if (!$this->horas_extras_totais || !$this->valor_hora_extra) {
                    return 0.00;
                }

                $competencia = Carbon::parse($this->competencia);
                $diasUteis = $this->calcularDiasUteis($competencia);
                $domingosFeriados = $this->calcularDomingosEFeriados($competencia);

                // Evita divisão por zero
                if ($diasUteis == 0) {
                    return 0.00;
                }

                $mediaHoraExtraDia = $this->horas_extras_totais / $diasUteis;

                return round($mediaHoraExtraDia * $domingosFeriados * $this->valor_hora_extra, 2);
            }
        );
    }

    // Método para verificar feriados nacionais (adicionar)
    private function isFeriado(Carbon $data): bool
    {
        $feriadosFixos = [
            '01-01', // Ano Novo
            '21-04', // Tiradentes
            '01-05', // Dia do Trabalho
            '07-09', // Independência
            '12-10', // Nossa Senhora Aparecida
            '02-11', // Finados
            '15-11', // Proclamação da República
            '25-12', // Natal
        ];

        // Verifica feriados fixos
        if (in_array($data->format('d-m'), $feriadosFixos)) {
            return true;
        }

        // Verifica feriados móveis (Páscoa, Carnaval, Corpus Christi)
        $ano = $data->year;
        $pascoa = $this->calcularPascoa($ano);

        $feriadosMoveis = [
            $pascoa->copy()->subDays(48), // Carnaval (segunda)
            $pascoa->copy()->subDays(47), // Carnaval (terça)
            $pascoa->copy()->subDays(2),  // Sexta-feira Santa
            $pascoa->copy()->addDays(60), // Corpus Christi
        ];

        foreach ($feriadosMoveis as $feriado) {
            if ($data->isSameDay($feriado)) {
                return true;
            }
        }

        return false;
    }

    // Cálculo da Páscoa (Algoritmo de Meeus)
    private function calcularPascoa(int $ano): Carbon
    {
        $a = $ano % 19;
        $b = intdiv($ano, 100);
        $c = $ano % 100;
        $d = intdiv($b, 4);
        $e = $b % 4;
        $f = intdiv(($b + 8), 25);
        $g = intdiv(($b - $f + 1), 3);
        $h = (19 * $a + $b - $d - $g + 15) % 30;
        $i = intdiv($c, 4);
        $k = $c % 4;
        $l = (32 + 2 * $e + 2 * $i - $h - $k) % 7;
        $m = intdiv(($a + 11 * $h + 22 * $l), 451);
        $mes = intdiv(($h + $l - 7 * $m + 114), 31);
        $dia = (($h + $l - 7 * $m + 114) % 31) + 1;

        return Carbon::create($ano, $mes, $dia);
    }

    private function calcularDiasUteis(Carbon $competencia): int
    {
        $diasUteis = 0;
        $data = $competencia->copy()->startOfMonth();
        $fimMes = $competencia->copy()->endOfMonth();

        while ($data->lte($fimMes)) {
            if ($data->isWeekday()) {
                $diasUteis++;
            }
            $data->addDay();
        }

        return $diasUteis;
    }

    private function calcularDomingosEFeriados(Carbon $competencia): int
    {
        $dsrs = 0;
        $data = $competencia->copy()->startOfMonth();
        $fimMes = $competencia->copy()->endOfMonth();

        while ($data->lte($fimMes)) {
            if ($data->isSunday() || $this->isFeriado($data)) {
                $dsrs++;
            }
            $data->addDay();
        }

        return $dsrs;
    }
}
