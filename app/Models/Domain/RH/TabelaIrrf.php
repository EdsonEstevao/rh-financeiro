<?php

namespace App\Models\Domain\RH;

use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Schema\Builder;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class TabelaIrrf extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $table = 'tabelas_irrf';

    protected $fillable = [
        'ano_vigencia',
        'descricao',
        'ativo',
        'vigencia_inicio',
        'vigencia_fim',
        'deducao_dependente',
        'deducao_pensao',
    ];

    protected $casts = [
        'ano_vigencia' => 'integer',
        'ativo' => 'boolean',
        'vigencia_inicio' => 'date',
        'vigencia_fim' => 'date',
        'deducao_dependente' => 'decimal:2',
        'deducao_pensao' => 'decimal:2',
    ];

    /**
     * Registra alterações via spatie/activitylog.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->useLogName('tabelas_irrf')
            ->setDescriptionForEvent(fn(string $eventName) => "Tabela IRRF {$eventName}");
    }

    // ═════════════════════════════════════════════════════════════════
    // RELACIONAMENTOS
    // ═════════════════════════════════════════════════════════════════

    public function faixas(): HasMany
    {
        return $this->hasMany(FaixaIrrf::class, 'tabela_irrf_id')
            ->orderBy('ordem');
    }

    // ═════════════════════════════════════════════════════════════════
    // SCOPES
    // ═════════════════════════════════════════════════════════════════

    /**
     * Apenas tabelas ativas.
     */
    public function scopeAtiva(Builder $query)
    {
        return $query->where('ativo', true);
    }

    /**
     * Tabela vigente em uma determinada data.
     */
    public function scopeVigente(Builder $query, $data = null)
    {
        $data = $data ? now()->parse($data) : now();

        return $query->where('vigencia_inicio', '<=', $data)
            ->where(function ($q) use ($data) {
                $q->whereNull('vigencia_fim')
                    ->orWhere('vigencia_fim', '>=', $data);
            });
    }

    // ═════════════════════════════════════════════════════════════════
    // CÁLCULO
    // ═════════════════════════════════════════════════════════════════

    /**
     * Calcula o IRRF com base na tabela progressiva.
     *
     * @param float $baseCalculo Base de cálculo do IRRF (já com deduções aplicadas).
     * @param int $dependentes Quantidade de dependentes.
     * @param float $pensaoAlimenticia Valor de pensão alimentícia (se houver).
     * @return array
     */
    public function calcular(float $baseCalculo, int $dependentes = 0, float $pensaoAlimenticia = 0): array
    {
        $baseCalculo = max(0, $baseCalculo);

        $deducaoDependentes = $dependentes * (float) $this->deducao_dependente;
        $deducaoPensao = $pensaoAlimenticia > 0 ? $pensaoAlimenticia : (float) $this->deducao_pensao;

        $baseReduzida = $baseCalculo - $deducaoDependentes - $deducaoPensao;

        // Se após deduções a base for zerada ou negativa, isenta
        if ($baseReduzida <= 0) {
            return [
                'irrf' => 0.00,
                'aliquota_efetiva' => 0.00,
                'base_calculo' => $baseCalculo,
                'base_reduzida' => 0.00,
                'deducao_dependentes' => $deducaoDependentes,
                'deducao_pensao' => $deducaoPensao,
                'faixa_aplicada' => null,
                'detalhamento' => [],
            ];
        }

        $irrf = 0;
        $faixaAplicada = null;
        $detalhamento = [];

        foreach ($this->faixas as $faixa) {
            $teto = (float) $faixa->teto;
            $aliquota = (float) $faixa->aliquota;
            $deducao = (float) $faixa->deducao;

            if ($baseReduzida <= $teto || $teto === 0) {
                // Última faixa (ou teto zero = faixa sem limite superior)
                $valorIrrf = max(0, ($baseReduzida * $aliquota) - $deducao);
                $irrf = $valorIrrf;
                $faixaAplicada = $faixa;

                $detalhamento[] = [
                    'ordem' => $faixa->ordem,
                    'teto' => $teto,
                    'aliquota' => $aliquota * 100,
                    'base_calculo' => $baseReduzida,
                    'deducao' => $deducao,
                    'valor' => $irrf,
                ];
                break;
            }
        }

        $irrf = round($irrf, 2);
        $aliquotaEfetiva = $baseCalculo > 0 ? round(($irrf / $baseCalculo) * 100, 2) : 0;

        return [
            'irrf' => $irrf,
            'aliquota_efetiva' => $aliquotaEfetiva,
            'base_calculo' => round($baseCalculo, 2),
            'base_reduzida' => round($baseReduzida, 2),
            'deducao_dependentes' => round($deducaoDependentes, 2),
            'deducao_pensao' => round($deducaoPensao, 2),
            'faixa_aplicada' => $faixaAplicada,
            'detalhamento' => $detalhamento,
        ];
    }
}
