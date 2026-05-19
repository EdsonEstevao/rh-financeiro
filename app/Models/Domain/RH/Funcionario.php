<?php

namespace App\Models\Domain\RH;

use Illuminate\Database\Eloquent\{Builder, Model};
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany, HasOne};
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

use App\Models\User;



/**
 * @property int $id
 * @property int|null $user_id
 * @property int $departamento_id
 * @property int $cargo_id
 * @property string $nome_completo
 * @property Carbon $data_nascimento
 * @property string $estado_civil
 * @property string $genero
 * @property string $nacionalidade
 * @property string|null $naturalidade
 * @property Carbon|null $periodo_aquisitivo_inicio
 * @property Carbon|null $periodo_aquisitivo_fim
 * @property Carbon|null $ferias_vencimento
 * @property bool $ferias_vencidas
 * @property bool $ferias_em_dobro
 * @property bool $ativo
 * @property string|null $observacoes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activitiesAsSubject
 * @property-read int|null $activities_as_subject_count
 * @property-read \App\Models\Domain\RH\FuncionarioBeneficio|null $beneficios
 * @property-read \App\Models\Domain\RH\Cargo $cargo
 * @property-read \App\Models\Domain\RH\FuncionarioContato|null $contatos
 * @property-read \App\Models\Domain\RH\FuncionarioContrato|null $contrato
 * @property-read \App\Models\Domain\RH\FuncionarioDadosBancarios|null $dadosBancarios
 * @property-read \App\Models\Domain\RH\Departamento $departamento
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Domain\RH\Dependente> $dependentes
 * @property-read int|null $dependentes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Domain\RH\Diaria> $diarias
 * @property-read int|null $diarias_count
 * @property-read \App\Models\Domain\RH\FuncionarioDocumento|null $documentos
 * @property-read \App\Models\Domain\RH\FuncionarioEndereco|null $endereco
 * @property-read mixed $ferias_vencidas_old
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Domain\RH\FolhaPagamento> $folhasPagamento
 * @property-read int|null $folhas_pagamento_count
 * @property-read mixed $agencia
 * @property-read mixed $banco_nome
 * @property-read mixed $carga_horaria_semanal
 * @property-read mixed $celular
 * @property-read mixed $cep
 * @property-read mixed $cidade
 * @property-read mixed $conta
 * @property-read mixed $cpf
 * @property-read mixed $ctps_numero
 * @property-read mixed $data_admissao
 * @property-read int|null $dias_para_vencimento_ferias
 * @property-read mixed $email
 * @property-read mixed $estado
 * @property-read mixed $local_trabalho
 * @property-read mixed $logradouro
 * @property-read mixed $pis_pasep
 * @property-read mixed $qtd_dependentes_salario_familia
 * @property-read mixed $rg
 * @property-read mixed $salario_base
 * @property-read float $salario_bruto
 * @property-read float $salario_liquido
 * @property-read string $sexto_dia_util_status
 * @property-read string $status_ferias
 * @property-read mixed $get_status_ferias_attribute_old
 * @property-read mixed $telefone
 * @property-read string $tempo_empresa
 * @property-read mixed $tipo_contratacao
 * @property-read mixed $tipo_contrato
 * @property-read string $tipo_contrato_label
 * @property-read float $total_descontos
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Domain\RH\PeriodoFerias> $periodoFerias
 * @property-read int|null $periodo_ferias_count
 * @property-read User|null $usuario
 * @method static Builder<static>|Funcionario ativos()
 * @method static Builder<static>|Funcionario feriasVencendo(int $dias = 30)
 * @method static Builder<static>|Funcionario feriasVencidas()
 * @method static Builder<static>|Funcionario feriasVencidasEmDobro()
 * @method static Builder<static>|Funcionario newModelQuery()
 * @method static Builder<static>|Funcionario newQuery()
 * @method static Builder<static>|Funcionario query()
 * @method static Builder<static>|Funcionario whereAtivo($value)
 * @method static Builder<static>|Funcionario whereCargoId($value)
 * @method static Builder<static>|Funcionario whereCreatedAt($value)
 * @method static Builder<static>|Funcionario whereDataNascimento($value)
 * @method static Builder<static>|Funcionario whereDepartamentoId($value)
 * @method static Builder<static>|Funcionario whereEstadoCivil($value)
 * @method static Builder<static>|Funcionario whereFeriasEmDobro($value)
 * @method static Builder<static>|Funcionario whereFeriasVencidas($value)
 * @method static Builder<static>|Funcionario whereFeriasVencimento($value)
 * @method static Builder<static>|Funcionario whereGenero($value)
 * @method static Builder<static>|Funcionario whereId($value)
 * @method static Builder<static>|Funcionario whereNacionalidade($value)
 * @method static Builder<static>|Funcionario whereNaturalidade($value)
 * @method static Builder<static>|Funcionario whereNomeCompleto($value)
 * @method static Builder<static>|Funcionario whereObservacoes($value)
 * @method static Builder<static>|Funcionario wherePeriodoAquisitivoFim($value)
 * @method static Builder<static>|Funcionario wherePeriodoAquisitivoInicio($value)
 * @method static Builder<static>|Funcionario whereUpdatedAt($value)
 * @method static Builder<static>|Funcionario whereUserId($value)
 * @mixin \Eloquent
 */
class Funcionario extends Model
{
    use LogsActivity;

    protected $table = 'funcionarios';

    protected $fillable = [
        'user_id', 'departamento_id', 'cargo_id',
        'nome_completo', 'data_nascimento', 'estado_civil', 'genero',
        'nacionalidade', 'naturalidade',
        'periodo_aquisitivo_inicio', 'periodo_aquisitivo_fim',
        'ferias_vencimento', 'ferias_vencidas', 'ferias_em_dobro',
        'ativo', 'observacoes',
    ];

    protected $casts = [
        'data_nascimento' => 'date',
        'periodo_aquisitivo_inicio' => 'date',
        'periodo_aquisitivo_fim' => 'date',
        'ferias_vencimento' => 'date',
        'ferias_vencidas' => 'boolean',
        'ferias_em_dobro' => 'boolean',
        'ativo' => 'boolean',
    ];

    // Model Funcionario
    public function dependentes(): HasMany
    {
        return $this->hasMany(Dependente::class);
    }


    // Accessor
    public function getTipoContratoLabelAttribute(): string
    {
        return match($this->tipo_contrato) {
            'indeterminado' => 'Prazo Indeterminado',
            'determinado' => 'Prazo Determinado',
            'experiencia' => 'Experiência (90 dias)',
            'intermitente' => 'Intermitente',
            'temporario' => 'Temporário',
            'aprendiz' => 'Aprendiz',
            'estagio' => 'Estágio',
            default => $this->tipo_contrato,
        };
    }
     // Relacionamentos 1:1 (novas tabelas)
    public function endereco(): HasOne
    {
        return $this->hasOne(FuncionarioEndereco::class);
    }

    public function documentos(): HasOne
    {
        return $this->hasOne(FuncionarioDocumento::class);
    }

    public function contatos(): HasOne
    {
        return $this->hasOne(FuncionarioContato::class);
    }

    public function dadosBancarios(): HasOne
    {
        return $this->hasOne(FuncionarioDadosBancarios::class);
    }

    public function contrato(): HasOne
    {
        return $this->hasOne(FuncionarioContrato::class);
    }

    public function beneficios(): HasOne
    {
        return $this->hasOne(FuncionarioBeneficio::class);
    }

     public function folhasPagamento(): HasMany
    {
        return $this->hasMany(FolhaPagamento::class);
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

    public function periodoFerias(): HasMany
    {
        return $this->hasMany(PeriodoFerias::class, 'funcionario_id');
    }

    // ─── ACCESSORS (delega para tabelas relacionadas) ───

    // Endereço
    public function getCepAttribute() { return $this->endereco?->cep; }
    public function getLogradouroAttribute() { return $this->endereco?->logradouro; }
    public function getCidadeAttribute() { return $this->endereco?->cidade; }
    public function getEstadoAttribute() { return $this->endereco?->estado; }

    // Documentos
    public function getCpfAttribute() { return $this->documentos?->cpf; }
    public function getRgAttribute() { return $this->documentos?->rg; }
    public function getCtpsNumeroAttribute() { return $this->documentos?->ctps_numero; }
    public function getPisPasepAttribute() { return $this->documentos?->pis_pasep; }

    // Contatos
    public function getTelefoneAttribute() { return $this->contatos?->telefone; }
    public function getCelularAttribute() { return $this->contatos?->celular; }
    public function getEmailAttribute() { return $this->contatos?->email; }

    // Dados Bancários
    public function getBancoNomeAttribute() { return $this->dadosBancarios?->banco_nome; }
    public function getAgenciaAttribute() { return $this->dadosBancarios?->agencia; }
    public function getContaAttribute() { return $this->dadosBancarios?->conta; }

    // Contrato (delegação)
    public function getDataAdmissaoAttribute() { return $this->contrato?->data_admissao; }
    public function getSalarioBaseAttribute() { return $this->contrato?->salario_base; }
    public function getCargaHorariaSemanalAttribute() { return $this->contrato?->carga_horaria_semanal; }
    public function getLocalTrabalhoAttribute() { return $this->contrato?->local_trabalho; }
    public function getTipoContratacaoAttribute() { return $this->contrato?->tipo_contratacao; }
    public function getTipoContratoAttribute() { return $this->contrato?->tipo_contrato; }
    public function getQtdDependentesSalarioFamiliaAttribute() { return $this->contrato?->qtd_dependentes_salario_familia; }

    // ─── SCOPES ───────────────────────────────────

    public function scopeAtivos(Builder $query): Builder
    {
        return $query->where('ativo', true);
    }

    public function scopeFeriasVencendo(Builder $query, int $dias = 30): Builder
    {
        return $query->where('ferias_vencimento', '<=', now()->addDays($dias))
                     ->where('ativo', true);
    }

    public function scopeFeriasVencidas(Builder $query): Builder
    {
        return $query->where('ferias_vencidas', true)
                     ->where('ativo', true);
    }

    // ─── MÉTODOS DE FÉRIAS ───────────────────────

    public function temDireitoFerias(): bool
    {
        if (!$this->data_admissao || !$this->ativo) return false;
        return Carbon::parse($this->data_admissao)->startOfDay()
            ->diffInMonths(now()->startOfDay()) >= 12;
    }

    public function getDiasParaVencimentoFeriasAttribute(): ?int
    {
        if (!$this->data_admissao || !$this->ativo) return null;

        $admissao = Carbon::parse($this->data_admissao)->startOfDay();
        $hoje = now()->startOfDay();
        $fimAquisitivo = $admissao->copy()->addYear();

        if ($hoje->lessThan($fimAquisitivo)) return null;

        $fimConcessivo = $admissao->copy()->addMonths(12);
        return (int) $hoje->diffInDays($fimConcessivo, false);
    }

    public function getStatusFeriasAttribute(): string
    {
        if (!$this->data_admissao || !$this->ativo) return 'Não aplicável';

        $admissao = Carbon::parse($this->data_admissao)->startOfDay();
        $hoje = now()->startOfDay();
        $fimAquisitivo = $admissao->copy()->addYear();
        $fimConcessivo = $admissao->copy()->addMonths(24);

        if ($hoje->lessThan($fimAquisitivo)) {
            $dias = (int) $hoje->diffInDays($fimAquisitivo);
            return "Em período aquisitivo ({$dias} dias restantes)";
        }

        if ($hoje->greaterThan($fimConcessivo)) {
            $diasVencido = (int) $fimConcessivo->diffInDays($hoje);
            return "Férias VENCIDAS há {$diasVencido} dia(s)";
        }

        $diasRestantes = (int) $hoje->diffInDays($fimConcessivo);

        if ($diasRestantes <= 30) {
            return "⚠ Férias a vencer em {$diasRestantes} dia(s)";
        }

        return "Férias disponíveis";
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
     *
     * CLT Art. 130 → Período Aquisitivo: 12 meses de trabalho
     * CLT Art. 134 → Período Concessivo: 12 meses após adquirir o direito
     * Total: 24 meses após a admissão = data limite para gozar as férias
     */
    public function calcularPeriodoFeriasAutomatico(): void
    {
        if (!$this->data_admissao) {
            return;
        }

        $dataAdmissao = Carbon::parse($this->data_admissao);

        // Período aquisitivo: da admissão até completar 12 meses
        $this->periodo_aquisitivo_inicio = $dataAdmissao->format('Y-m-d');
        $this->periodo_aquisitivo_fim    = $dataAdmissao->copy()
                                            ->addYear()
                                            ->subDay()
                                            ->format('Y-m-d');

        // Vencimento: 12 meses + 1 dias corridos após admissão

        $this->ferias_vencimento = $dataAdmissao->copy()
            ->addYear()
            ->addDays(1)
            ->format('Y-m-d');


        $this->verificarFeriasVencidas();
    }

     public function verificarFeriasVencidas(): void
    {
        if (!$this->ferias_vencimento) {
            return;
        }

        $hoje      = Carbon::now()->startOfDay();
        $vencimento = Carbon::parse($this->ferias_vencimento)->startOfDay();

        // dd($vencimento);

        // Férias em dobro = hoje ultrapassou o vencimento do período concessivo
        $this->ferias_vencidas = $hoje->greaterThan($vencimento);

        // ✅ Não alteramos ferias_vencimento aqui!
        // A data de vencimento reflete a realidade (quando venceu de fato)
        // O sistema apenas sinaliza que o pagamento deve ser em dobro
    }

    protected function feriasVencidasOld(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->data_admissao || !$this->ativo) {
                    return false;
                }

                // Só considera vencidas se tiver direito (>= 12 meses)
                // e tiver passado do prazo concessivo (24 meses)
                if (!$this->temDireitoFerias()) {
                    return false;
                }

                $vencimento = Carbon::parse($this->data_admissao)
                    ->startOfDay()
                    ->addMonths(24);

                return Carbon::now()->startOfDay()->greaterThan($vencimento);
            }
        );
    }


    /**
     * Verifica se as férias estão vencidas (em dobro)
     *
     * CLT Art. 137 → Se a empresa não concedeu as férias dentro
     * do período concessivo (12 meses após o período aquisitivo),
     * deve pagar a remuneração em DOBRO.
     *
     * ⚠️ IMPORTANTE:
     * - "Em dobro" afeta apenas o PAGAMENTO, não os dias de descanso
     * - Os dias de férias continuam sendo 30 dias
     * - Não existe "novo vencimento automático" — o direito permanece vencido
     *   até ser quitado ou gozado com pagamento em dobro
     */
    public function verificarFeriasEmDobro(): void
    {
        if (!$this->ferias_vencimento) {
            return;
        }

        $hoje      = Carbon::now()->startOfDay();
        $vencimento = Carbon::parse($this->ferias_vencimento)->startOfDay();

        // dd($vencimento);

        // Férias em dobro = hoje ultrapassou o vencimento do período concessivo
        $this->ferias_em_dobro = $hoje->greaterThan($vencimento);

        // ✅ Não alteramos ferias_vencimento aqui!
        // A data de vencimento reflete a realidade (quando venceu de fato)
        // O sistema apenas sinaliza que o pagamento deve ser em dobro
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
        if ($meses > 0) $tempo[] = "$meses " . ($meses > 1 ? 'meses' : 'mês');
        if ($dias > 0 && $anos == 0) $tempo[] = "$dias dia" . ($dias > 1 ? 's' : '');

        return empty($tempo) ? '0 dias' : implode(', ', $tempo);
    }

    /**
     * Verifica se funcionário tem direito a férias
     */
    public function temDireitoFeriasOld(): bool
    {
        if (!$this->data_admissao || !$this->ativo) {
            return false;
        }

        $admissao = Carbon::parse($this->data_admissao)->startOfDay();
        $hoje = Carbon::now()->startOfDay();



        return $admissao->diffInMonths($hoje) >= 12;
    }

    /**
     * Dias até o vencimento das férias
     */
    public function getDiasParaVencimentoFeriasAttributeOld(): int
    {
        if (!$this->ferias_vencimento) {
            return 0;
        }

        $hoje = Carbon::now();
        $vencimento = Carbon::parse($this->ferias_vencimento);

        return $hoje->diffInDays($vencimento, false); // false = pode retornar negativo
    }



    /**
     * Status descritivo das férias para exibição.
     */
    protected function getStatusFeriasAttributeOld(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->data_admissao || !$this->ativo) {
                    return 'Não aplicável';
                }

                if (!$this->temDireitoFerias()) {
                    $diasRestantes = $this->diasParaDireitoFerias();
                    return "Em período aquisitivo ({$diasRestantes} dias restantes)";
                }

                if ($this->ferias_vencidas) {
                    return 'Férias VENCIDAS — pagamento em dobro';
                }

                $dias = $this->dias_para_vencimento_ferias;

                if ($dias <= 30) {
                    return "Férias a vencer em {$dias} dia(s)";
                }

                return "Férias disponíveis ({$dias} dias para vencer)";
            }
        );
    }

    /**
     * Quantos dias faltam para adquirir direito às férias (período aquisitivo).
     * Retorna 0 se já tiver direito.
     */
    public function diasParaDireitoFeriasOld(): int
    {
        if (!$this->data_admissao) {
            return 0;
        }

        // Data em que completa 12 meses
        $dataAquisicao = Carbon::parse($this->data_admissao)
            ->startOfDay()
            ->addYearNoOverflow();

        $hoje = Carbon::now()->startOfDay();

        // Se já tem direito, retorna 0
        if ($hoje->greaterThanOrEqualTo($dataAquisicao)) {
            return 0;
        }

        return (int) $hoje->diffInDays($dataAquisicao);
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



    public function scopeFeriasVencidasEmDobro(Builder $query)
    {
        return $query->where('ferias_em_dobro', true)
                    ->where('ativo', true);
    }

     /**
     * Calcula salário bruto total
     */
    // public function getSalarioBrutoAttribute(): float
    // {
    //     return $this->salario_base +
    //            $this->gratificacao_provento +
    //         //    $this->dsr_hora_extra +
    //            $this->salario_familia +
    //            $this->hora_extra;

        
    // }

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

    // Periodo Ferias
    // ─────────────────────────────────────────────
    // MÉTODOS PÚBLICOS (chamáveis como $funcionario->metodo())
    // ─────────────────────────────────────────────

    /**
     * Verifica se o funcionário tem direito a férias.
     * CLT Art. 130 — após 12 meses de contrato.
     */
    public function temDireitoFeriasOld2(): bool
    {
        if (!$this->data_admissao || !$this->ativo) {
            return false;
        }

        return Carbon::parse($this->data_admissao)
            ->startOfDay()
            ->diffInMonths(Carbon::now()->startOfDay()) >= 12;
    }

    /**
     * Quantos meses completos o funcionário trabalhou.
     */
    public function mesesTrabalhadosOld2(): int
    {
        if (!$this->data_admissao) {
            return 0;
        }

        return (int) Carbon::parse($this->data_admissao)
            ->startOfDay()
            ->diffInMonths(Carbon::now()->startOfDay());
    }

    /**
     * Quantos dias faltam para adquirir direito às férias (período aquisitivo).
     * Retorna 0 se já tiver direito.
     */
    public function diasParaDireitoFeriasOld2(): int
    {
        if (!$this->data_admissao) {
            return 0;
        }

        // Data em que completa 12 meses
        $dataAquisicao = Carbon::parse($this->data_admissao)
            ->startOfDay()
            ->addYearNoOverflow();

        $hoje = Carbon::now()->startOfDay();

        // Se já tem direito, retorna 0
        if ($hoje->greaterThanOrEqualTo($dataAquisicao)) {
            return 0;
        }

        return (int) $hoje->diffInDays($dataAquisicao);
    }

    // ─────────────────────────────────────────────
    // ACCESSORS (acessados como $funcionario->atributo)
    // ─────────────────────────────────────────────


    /**
     * Texto descritivo do status das férias para exibição.
     */
    protected function statusFerias(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->data_admissao || !$this->ativo) {
                    return 'Não aplicável';
                }

                if (!$this->temDireitoFerias()) {
                    $dias = $this->diasParaDireitoFerias();
                    return "Em período aquisitivo ({$dias} dias restantes)";
                }

                if ($this->ferias_vencidas) {
                    return 'Férias VENCIDAS — pagamento em dobro';
                }

                $dias = $this->dias_para_vencimento_ferias;

                if ($dias !== null && $dias <= 30) {
                    return "Férias a vencer em {$dias} dia(s)";
                }

                return "Férias disponíveis ({$dias} dias para vencer)";
            }
        );
    }


    //Accessor
    /**
     * Dias até o vencimento do período concessivo (admissão + 24 meses).
     * Negativo = já venceu | null = sem data de admissão ou ainda em aquisição.
     */
    public function getDiasParaVencimentoFeriasAttributeOld2(): ?int
    {
        if (!$this->data_admissao || !$this->ativo) {
            return null;
        }

        if (!$this->temDireitoFerias()) {
            return null; // ainda em período aquisitivo
        }

        $vencimento = Carbon::parse($this->data_admissao)
            ->startOfDay()
            ->addMonths(24);

        $hoje = Carbon::now()->startOfDay();

        // false = permite valor negativo (já venceu)
        return (int) $hoje->diffInDays($vencimento, false);
    }

    /**
     * Férias vencidas = passou dos 24 meses sem gozar.
     * CLT Art. 137 — pagamento em dobro.
     */
    public function getFeriasVencidasAttributeOld2(): bool
    {
        if (!$this->data_admissao || !$this->ativo) {
            return false;
        }

        if (!$this->temDireitoFerias()) {
            return false;
        }

        $vencimento = Carbon::parse($this->data_admissao)
            ->startOfDay()
            ->addMonths(24);

        return Carbon::now()->startOfDay()->greaterThan($vencimento);
    }

    /*************** */


    public function getFeriasVencidasAttribute(): bool
    {
        if (!$this->data_admissao || !$this->ativo) {
            return false;
        }

        $admissao      = Carbon::parse($this->data_admissao)->startOfDay();
        $hoje          = Carbon::now()->startOfDay();
        $fimAquisitivo = $admissao->copy()->addYear();

        // Não completou nem 12 meses — impossível estar vencida
        if ($hoje->lessThan($fimAquisitivo)) {
            return false;
        }

        $fimConcessivo = $admissao->copy()->addMonths(24);

        return $hoje->greaterThan($fimConcessivo);
    }

    public function mesesTrabalhados(): int
    {
        if (!$this->data_admissao) {
            return 0;
        }

        return (int) Carbon::parse($this->data_admissao)
            ->startOfDay()
            ->diffInMonths(Carbon::now()->startOfDay());
    }

    public function diasParaDireitoFerias(): int
    {
        if (!$this->data_admissao) {
            return 0;
        }

        $fimAquisitivo = Carbon::parse($this->data_admissao)
            ->startOfDay()
            ->addYear();

        $hoje = Carbon::now()->startOfDay();

        if ($hoje->greaterThanOrEqualTo($fimAquisitivo)) {
            return 0;
        }

        return (int) $hoje->diffInDays($fimAquisitivo);
    }


}