<?php

// namespace App\Models\Domain\RH;

// use Illuminate\Database\Eloquent\Model;

// class TabelaInss extends Model
// {
//     //
// }
namespace App\Models\Domain\RH;

use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
// use Spatie\Activitylog\Traits\LogsActivity;

class TabelaInss extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'tabelas_inss';

    protected $fillable = [
        'ano_vigencia', 'descricao', 'ativo',
        'vigencia_inicio', 'vigencia_fim',
    ];

    protected $casts = [
        'ano_vigencia' => 'integer',
        'ativo' => 'boolean',
        'vigencia_inicio' => 'date',
        'vigencia_fim' => 'date',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Tabela INSS {$eventName}");
    }

    // Relacionamentos
    public function faixas()
    {
        return $this->hasMany(FaixaInss::class)->orderBy('ordem');
    }

    // Scopes
    public function scopeAtiva($query)
    {
        return $query->where('ativo', true);
    }

    public function scopeVigente($query, $data = null)
    {
        $data = $data ?? now();
        return $query->where('vigencia_inicio', '<=', $data)
                     ->where(function ($q) use ($data) {
                         $q->whereNull('vigencia_fim')
                           ->orWhere('vigencia_fim', '>=', $data);
                     });
    }

    // ─── Cálculo ───────────────────────────────
    public function calcular(float $salario): array
    {
        $inss = 0;
        $anterior = 0;
        $detalhamento = [];

        foreach ($this->faixas as $faixa) {
            if ($salario > $anterior) {
                $baseCalculo = min($salario, (float) $faixa->teto) - $anterior;
                $valorFaixa = round($baseCalculo * (float) $faixa->aliquota, 2);
                $inss += $valorFaixa;

                $detalhamento[] = [
                    'ordem' => $faixa->ordem,
                    'teto' => (float) $faixa->teto,
                    'aliquota' => (float) $faixa->aliquota * 100,
                    'base_calculo' => $baseCalculo,
                    'valor' => $valorFaixa,
                ];

                $anterior = (float) $faixa->teto;
            }
            if ($salario <= (float) $faixa->teto) break;
        }

        $inss = round($inss, 2);
        $aliquotaEfetiva = $salario > 0 ? round(($inss / $salario) * 100, 2) : 0;

        return [
            'inss' => $inss,
            'aliquota_efetiva' => $aliquotaEfetiva,
            'detalhamento' => $detalhamento,
        ];
    }
}
