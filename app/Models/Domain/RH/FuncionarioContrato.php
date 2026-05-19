<?php

namespace App\Models\Domain\RH;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $funcionario_id
 * @property Carbon $data_admissao
 * @property Carbon|null $data_demissao
 * @property string $tipo_contratacao Regime/Vínculo
 * @property string $tipo_contrato Prazo/Termo
 * @property string $tipo_remuneracao
 * @property numeric $salario_base
 * @property numeric|null $valor_diaria
 * @property numeric|null $valor_hora
 * @property bool $eh_diarista
 * @property bool $aplica_inss
 * @property int $carga_horaria_semanal
 * @property Carbon $horario_entrada
 * @property Carbon $horario_saida
 * @property Carbon $horario_almoco_inicio
 * @property Carbon $horario_almoco_fim
 * @property string|null $local_trabalho
 * @property int $qtd_dependentes_ir
 * @property int $qtd_dependentes_salario_familia
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \App\Models\Domain\RH\Funcionario $funcionario
 * @property-read float $valor_hora_extra
 * @property-read float $valor_hora_normal
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioContrato newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioContrato newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioContrato query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioContrato whereAplicaInss($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioContrato whereCargaHorariaSemanal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioContrato whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioContrato whereDataAdmissao($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioContrato whereDataDemissao($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioContrato whereEhDiarista($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioContrato whereFuncionarioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioContrato whereHorarioAlmocoFim($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioContrato whereHorarioAlmocoInicio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioContrato whereHorarioEntrada($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioContrato whereHorarioSaida($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioContrato whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioContrato whereLocalTrabalho($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioContrato whereQtdDependentesIr($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioContrato whereQtdDependentesSalarioFamilia($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioContrato whereSalarioBase($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioContrato whereTipoContratacao($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioContrato whereTipoContrato($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioContrato whereTipoRemuneracao($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioContrato whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioContrato whereValorDiaria($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioContrato whereValorHora($value)
 * @mixin \Eloquent
 */
class FuncionarioContrato extends Model
{
    protected $table = 'funcionario_contratos';

    protected $fillable = [
        'funcionario_id',
        'data_admissao',
        'data_demissao',
        'tipo_contratacao',
        'tipo_contrato',
        'tipo_remuneracao',
        'salario_base',
        'valor_diaria',
        'valor_hora',
        'eh_diarista',
        'aplica_inss',
        'carga_horaria_semanal',
        'horario_entrada',
        'horario_saida',
        'horario_almoco_inicio',
        'horario_almoco_fim',
        'local_trabalho',
        'qtd_dependentes_ir',
        'qtd_dependentes_salario_familia',
    ];

    protected $casts = [
        'data_admissao' => 'date',
        'data_demissao' => 'date',
        'salario_base' => 'decimal:2',
        'valor_diaria' => 'decimal:2',
        'valor_hora' => 'decimal:2',
        'eh_diarista' => 'boolean',
        'aplica_inss' => 'boolean',
        'horario_entrada' => 'datetime:H:i:s',
        'horario_saida' => 'datetime:H:i:s',
        'horario_almoco_inicio' => 'datetime:H:i:s',
        'horario_almoco_fim' => 'datetime:H:i:s',
    ];

    public function funcionario(): BelongsTo
    {
        return $this->belongsTo(Funcionario::class);
    }

    /**
     * Boot - Eventos do Model
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($contrato) {
            // Ao criar um contrato, gera férias previstas
            $contrato->gerarFeriasPrevistas();
        });

        static::updated(function ($contrato) {
            // Se mudou a data de admissão, recalcula férias
            if ($contrato->wasChanged('data_admissao')) {
                $contrato->recalcularFeriasPrevistas();
            }
        });
    }

    /**
     * Gera férias previstas ao criar contrato
     */
    public function gerarFeriasPrevistas(): void
    {
          // ✅ PJ não tem férias
        if ($this->tipo_contratacao === 'pj') {
            return;
        }

        if (!$this->data_admissao) return;

        $admissao = Carbon::parse($this->data_admissao)->startOfDay();

        // ✅ Período Aquisitivo: da admissão até 12 meses depois
        $inicioAquisitivo = $admissao->copy();
        $fimAquisitivo = $admissao->copy()->addYear()->subDay();

        // ✅ Férias previstas: INÍCIO após completar 12 meses
        $inicioFerias = $admissao->copy()->addYear(); // 13/05/2026
        $fimFerias = $inicioFerias->copy()->addDays(29); // 30 dias corridos

        // ✅ Vencimento do período concessivo: 24 meses após admissão
        $vencimentoConcessivo = $admissao->copy()->addMonths(24); // 13/05/2027

        // Atualiza dados de férias no funcionário
        $this->funcionario->update([
            'periodo_aquisitivo_inicio' => $inicioAquisitivo->toDateString(),
            'periodo_aquisitivo_fim' => $fimAquisitivo->toDateString(),
            'ferias_vencimento' => $vencimentoConcessivo->toDateString(),
        ]);

        // Cria período de férias prevista (começa após 12 meses)
        $this->funcionario->periodoFerias()->create([
            'data_inicio' => $inicioFerias->toDateString(),      // 13/05/2026 ✅
            'data_fim' => $fimFerias->toDateString(),             // 11/06/2026
            'tipo' => 'prevista',
            'status' => 'planejada',
            'numero_periodo' => 1,
            'abono_pecuniario' => false,
            'observacao' => 'Férias previstas - Período aquisitivo completado em ' . $fimAquisitivo->format('d/m/Y'),
        ]);
    }

    /**
     * Recalcula férias previstas quando data de admissão muda
     */
    public function recalcularFeriasPrevistas(): void
    {
        $funcionario = $this->funcionario;

        // Verifica se já tem férias efetivadas
        $temEfetivadas = $funcionario->periodoFerias()
            ->whereIn('status', ['aprovada', 'gozada', 'em_gozo'])
            ->exists();

        if ($temEfetivadas) {
            return; // Não altera férias já efetivadas
        }

        // Remove férias previstas antigas
        $funcionario->periodoFerias()
            ->where('tipo', 'prevista')
            ->where('status', 'planejada')
            ->delete();

        // Gera novas
        $this->gerarFeriasPrevistas();
    }

    /**
     * Calcula valor da hora normal
     */
    public function getValorHoraNormalAttribute(): float
    {
        if ($this->eh_diarista && $this->valor_diaria > 0) {
            return $this->valor_diaria / 8; // 8h por dia
        }

        $horasMensais = ($this->carga_horaria_semanal / 6) * 30;
        return $horasMensais > 0 ? $this->salario_base / $horasMensais : 0;
    }

    /**
     * Calcula valor da hora extra (50%)
     */
    public function getValorHoraExtraAttribute(): float
    {
        return $this->valor_hora_normal * 1.5;
    }
    /**
     *  Salario Bruto
     */
   

}