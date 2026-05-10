<?php

namespace App\Models\Domain\RH;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

use App\Models\User;


class Funcionario extends Model
{
    use LogsActivity;

    protected $table = 'funcionarios';

    /*
     'user_id', 'departamento_id', 'cargo_id',
        'nome_completo', 'cpf', 'rg', 'orgao_expedidor_rg', 'data_nascimento',
        'estado_civil', 'genero', 'nacionalidade', 'naturalidade',
        'telefone', 'celular', 'email', 'email_pessoal',
        'cep', 'logradouro', 'numero', 'complemento', 'bairro', 'cidade', 'estado',
        'ctps_numero', 'ctps_serie', 'ctps_uf', 'ctps_data_emissao',
        'pis_pasep', 'titulo_eleitor', 'zona_eleitoral', 'secao_eleitoral', 'certificado_reservista',
        'data_admissao', 'data_demissao', 'tipo_contrato', 'tipo_contratacao', 'salario_base',
        'carga_horaria_semanal', 'horario_entrada', 'horario_saida',
        'horario_almoco_inicio', 'horario_almoco_fim',
        'vale_transporte', 'valor_vale_transporte', 'vale_alimentacao', 'valor_vale_alimentacao',
        'plano_saude', 'plano_odontologico',
        'banco_codigo', 'banco_nome', 'agencia', 'conta', 'tipo_conta',
        'qtd_dependentes_ir', 'qtd_dependentes_salario_familia',
        'local_trabalho', 'desconto_inss_8_porcento', 'vale_extra', 'faltas',
        'dsr_faltas', 'desconto_faltas', 'gratificacao_provento', 'dsr_hora_extra',
        'salario_familia', 'hora_extra', 'sexto_dia_util_mes',
        'periodo_aquisitivo_inicio', 'periodo_aquisitivo_fim', 'ferias_vencimento', 'ferias_em_dobro',
        'ativo', 'observacoes'
     */

     protected $fillable = [
        'user_id', 'departamento_id', 'cargo_id',
        'nome_completo', 'cpf', 'rg', 'orgao_expedidor_rg', 'data_nascimento',
        'estado_civil', 'genero', 'nacionalidade', 'naturalidade',
        'telefone', 'celular', 'email', 'email_pessoal',
        'cep', 'logradouro', 'numero', 'complemento', 'bairro', 'cidade', 'estado',
        'ctps_numero', 'ctps_serie', 'ctps_uf', 'ctps_data_emissao',
        'pis_pasep', 'titulo_eleitor', 'zona_eleitoral', 'secao_eleitoral', 'certificado_reservista',
        'data_admissao', 'data_demissao', 'tipo_contrato', 'salario_base',
        'carga_horaria_semanal', 'horario_entrada', 'horario_saida',
        'horario_almoco_inicio', 'horario_almoco_fim',
        'vale_transporte', 'valor_vale_transporte', 'vale_alimentacao', 'valor_vale_alimentacao',
        'plano_saude', 'plano_odontologico',
        'banco_codigo', 'banco_nome', 'agencia', 'conta', 'tipo_conta',
        'qtd_dependentes_ir', 'qtd_dependentes_salario_familia',
        'periodo_aquisitivo_inicio', 'periodo_aquisitivo_fim', 'ferias_vencimento', 'ferias_em_dobro',
        'ativo', 'observacoes',
        //Novos campos
        'local_trabalho', 'tipo_contratacao', 'desconto_inss_8_porcento',
        'vale_extra', 'faltas', 'dsr_faltas', 'desconto_faltas',
        'gratificacao_provento', 'dsr_hora_extra', 'salario_familia',
        'hora_extra', 'sexto_dia_util_mes',
        'eh_diarista',
        'valor_diaria',
    ];

    protected function casts(): array
    {
        return [
            'data_nascimento' => 'date',
            'data_admissao' => 'date',
            'data_demissao' => 'date',
            'ctps_data_emissao' => 'date',
            'periodo_aquisitivo_inicio' => 'date',
            'periodo_aquisitivo_fim' => 'date',
            'ferias_vencimento' => 'date',
            'salario_base' => 'decimal:2',
            'valor_vale_transporte' => 'decimal:2',
            'valor_vale_alimentacao' => 'decimal:2',
            'vale_transporte' => 'boolean',
            'vale_alimentacao' => 'boolean',
            'plano_saude' => 'boolean',
            'plano_odontologico' => 'boolean',
            'ferias_em_dobro' => 'boolean',
            'ativo' => 'boolean',
            //Novos campos
            'desconto_inss_8_porcento' => 'decimal:2',
            'vale_extra' => 'decimal:2',
            'dsr_faltas' => 'decimal:2',
            'desconto_faltas' => 'decimal:2',
            'gratificacao_provento' => 'decimal:2',
            'dsr_hora_extra' => 'decimal:2',
            'salario_familia' => 'decimal:2',
            'hora_extra' => 'decimal:2',
            'sexto_dia_util_mes' => 'boolean',
            'eh_diarista' => 'boolean',
            'valor_diaria' => 'decimal:2',

        ];
    }

    // Relacionamentos
    public function departamento(): BelongsTo
    {
        return $this->belongsTo(Departamento::class, 'departamento_id');
    }
    public function diarias()
    {
        return $this->hasMany(Diaria::class, 'funcionario_id');
    }

    public function cargo(): BelongsTo
    {
        return $this->belongsTo(Cargo::class, 'cargo_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function periodosFerias(): HasMany
    {
        return $this->hasMany(PeriodoFerias::class, 'funcionario_id');
    }

    // Eventos do Model
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($funcionario) {
            $funcionario->calcularPeriodoFeriasAutomatico();
        });

        static::updating(function ($funcionario) {
            // Se mudou a data de admissão, recalcular férias
            if ($funcionario->isDirty('data_admissao')) {
                $funcionario->calcularPeriodoFeriasAutomatico();
            }
        });
    }

    /**
     * Calcula automaticamente o período aquisitivo e vencimento das férias
     * Conforme CLT Art. 130 - Direito a 30 dias após 12 meses de trabalho
     */
    public function calcularPeriodoFeriasAutomatico(): void
    {
        if (!$this->data_admissao) {
            return;
        }

        $dataAdmissao = Carbon::parse($this->data_admissao);

        // Período aquisitivo: inicia na data de admissão
        $this->periodo_aquisitivo_inicio = $dataAdmissao->format('Y-m-d');

        // Fim do período aquisitivo: 11 meses e 30 dias após admissão
        $this->periodo_aquisitivo_fim = $dataAdmissao->copy()->addYear()->subDay()->format('Y-m-d');

        // Vencimento das férias: 23 meses após admissão (CLT Art. 134)
        // Funcionário tem até 12 meses após completar o período aquisitivo para tirar
        $this->ferias_vencimento = $dataAdmissao->copy()->addMonths(23)->format('Y-m-d');

        // Verifica se as férias estão vencendo em dobro (após o prazo)
        $this->verificarFeriasEmDobro();
    }

    /**
     * Verifica se as férias devem ser pagas em dobro
     * CLT Art. 137 - Férias não gozadas no prazo devem ser pagas em dobro
     */
    public function verificarFeriasEmDobro(): void
    {
        if (!$this->ferias_vencimento) {
            return;
        }

        $hoje = Carbon::now();
        $vencimento = Carbon::parse($this->ferias_vencimento);

        $this->ferias_em_dobro = $hoje->greaterThan($vencimento);

        if ($this->ferias_em_dobro) {
            $this->ferias_vencimento = $vencimento->addYear()->format('Y-m-d');
        }
    }

    /**
     * Calcula tempo de empresa em anos, meses e dias
     */
    public function getTempoEmpresaAttribute(): string
    {
        if (!$this->data_admissao) {
            return '0 anos';
        }

        $admissao = Carbon::parse($this->data_admissao);
        $referencia = $this->data_demissao ? Carbon::parse($this->data_demissao) : Carbon::now();

        $diff = $admissao->diff($referencia);

        $anos = $diff->y;
        $meses = $diff->m;
        $dias = $diff->d;

        $tempo = [];
        if ($anos > 0) $tempo[] = "$anos ano" . ($anos > 1 ? 's' : '');
        if ($meses > 0) $tempo[] = "$meses mês" . ($meses > 1 ? 'es' : '');
        if ($dias > 0 && $anos == 0) $tempo[] = "$dias dia" . ($dias > 1 ? 's' : '');

        return empty($tempo) ? '0 dias' : implode(', ', $tempo);
    }

    /**
     * Verifica se funcionário tem direito a férias
     */
    public function temDireitoFerias(): bool
    {
        if (!$this->data_admissao || !$this->ativo) {
            return false;
        }

        $admissao = Carbon::parse($this->data_admissao);
        $hoje = Carbon::now();

        return $hoje->diffInMonths($admissao) >= 12;
    }

    /**
     * Dias até o vencimento das férias
     */
    public function getDiasParaVencimentoFeriasAttribute(): int
    {
        if (!$this->ferias_vencimento) {
            return 0;
        }

        $hoje = Carbon::now();
        $vencimento = Carbon::parse($this->ferias_vencimento);

        return $hoje->diffInDays($vencimento, false); // false = pode retornar negativo
    }

    /**
     * Status das férias (texto amigável)
     */
    public function getStatusFeriasAttribute(): string
    {
        if (!$this->temDireitoFerias()) {
            return 'Sem direito ainda';
        }

        if ($this->ferias_em_dobro) {
            return 'Vencidas (em dobro)';
        }

        $dias = $this->dias_para_vencimento_ferias;

        if ($dias < 0) {
            return 'Vencidas há ' . abs($dias) . ' dias';
        } elseif ($dias <= 30) {
            return "Vencem em $dias dias";
        } else {
            return 'Dentro do prazo';
        }
    }

    /**
     * Activity Log Options
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('rh')
            ->logOnly([
                'nome_completo', 'cpf', 'data_admissao', 'data_demissao',
                'salario_base', 'departamento_id', 'cargo_id', 'ativo'
            ])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    // Scopes úteis
    public function scopeAtivos(Builder $query)
    {
        return $query->where('ativo', true);
    }

    public function scopeFeriasVencendo( Builder $query, $dias = 30)
    {
        return $query->where('ferias_vencimento', '<=', Carbon::now()->addDays($dias))
                    ->where('ativo', true);
    }

    public function scopeFeriasVencidas(Builder $query)
    {
        return $query->where('ferias_em_dobro', true)
                    ->where('ativo', true);
    }

    public function scopeFeriasVencidasEmDobro(Builder $query)
    {
        return $query->where('ferias_em_dobro', true)
                    ->where('ativo', true);
    }

     /**
     * Calcula salário bruto total
     */
    public function getSalarioBrutoAttribute(): float
    {
        return $this->salario_base +
               $this->gratificacao_provento +
               $this->dsr_hora_extra +
               $this->salario_familia +
               $this->hora_extra;
    }

    /**
     * Calcula total de descontos
     */
    public function getTotalDescontosAttribute(): float
    {
        return $this->desconto_inss_8_porcento +
               $this->desconto_faltas +
               $this->valor_vale_transporte +
               $this->valor_vale_alimentacao;
    }

    /**
     * Calcula salário líquido
     */
    public function getSalarioLiquidoAttribute(): float
    {
        return $this->salario_bruto - $this->total_descontos;
    }

    /**
     * Formata valor monetário para exibição
     */
    public function formatarMoeda(string $valor): string
    {
        return 'R$ ' . number_format($valor, 2, ',', '.');
    }

    /**
     * Status do 6º dia útil do mês
     */
    public function getSextoDiaUtilStatusAttribute(): string
    {
        return $this->sexto_dia_util_mes ? 'SIM' : 'NÃO';
    }
}