<?php

namespace App\Models\Domain\RH;

use Illuminate\Database\Eloquent\{Builder, Model};
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Models\User;

class FolhaLancamento extends Model
{
    protected $table = 'folha_lancamentos';

    protected $fillable = [
        'folha_pagamento_id',
        'categoria',
        'tipo',
        'descricao',
        'quantidade',
        'valor_unitario',
        'percentual_acrescimo',
        'valor_total',
        'data_referencia',
        'observacao',
        'automatico',
        'criado_por',
    ];

    protected $casts = [
        'quantidade' => 'decimal:2',
        'valor_unitario' => 'decimal:2',
        'percentual_acrescimo' => 'decimal:2',
        'valor_total' => 'decimal:2',
        'data_referencia' => 'date',
        'automatico' => 'boolean',
    ];

    // Constantes para tipos de lançamento
    const TIPO_HORA_EXTRA_NORMAL = 'hora_extra_normal';
    const TIPO_HORA_EXTRA_SABADO = 'hora_extra_sabado';
    const TIPO_HORA_EXTRA_FERIADO = 'hora_extra_feriado';
    const TIPO_DSR_HORA_EXTRA = 'dsr_hora_extra';
    const TIPO_FALTA = 'falta';
    const TIPO_DSR_FALTA = 'dsr_falta';
    const TIPO_GRATIFICACAO = 'gratificacao';
    const TIPO_SALARIO_FAMILIA = 'salario_familia';
    const TIPO_INSS = 'inss';
    const TIPO_VALE_DIA_20 = 'vale_dia_20';
    const TIPO_VALE_EXTRA = 'vale_extra';
    const TIPO_SALARIO_BASE = 'salario_base';
    const TIPO_ARREDONDAMENTO = 'arredondamento';

    const CATEGORIA_PROVENTO = 'provento';
    const CATEGORIA_DESCONTO = 'desconto';

    public function folhaPagamento(): BelongsTo
    {
        return $this->belongsTo(FolhaPagamento::class);
    }

    public function criador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'criado_por');
    }

    /**
     * Scope para proventos
     */
    public function scopeProventos(Builder $query)
    {
        return $query->where('categoria', self::CATEGORIA_PROVENTO);
    }

    /**
     * Scope para descontos
     */
    public function scopeDescontos(Builder $query)
    {
        return $query->where('categoria', self::CATEGORIA_DESCONTO);
    }

    /**
     * Scope por tipo
     */
    public function scopeDoTipo(Builder $query, string $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    /**
     * Calcula o valor total automaticamente
     */
    public function calcularValorTotal(): float
    {
        if ($this->quantidade == 0 || $this->valor_unitario == 0) {
            return $this->valor_total ?? 0;
        }

        $acrescimo = 1 + ($this->percentual_acrescimo / 100);
        return round($this->quantidade * $this->valor_unitario * $acrescimo, 2);
    }

    /**
     * Boot - calcula valor_total antes de salvar
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($lancamento) {
            if ($lancamento->automatico && empty($lancamento->valor_total)) {
                $lancamento->valor_total = $lancamento->calcularValorTotal();
            }
        });

        static::updating(function ($lancamento) {
            if ($lancamento->automatico) {
                $lancamento->valor_total = $lancamento->calcularValorTotal();
            }
        });
    }
}
