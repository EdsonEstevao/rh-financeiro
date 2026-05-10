<?php

namespace App\Models\Domain\RH;

use Illuminate\Database\Eloquent\Model;

class Diaria extends Model
{
    /**
     * 🔨 Como cadastrar/baixar as diárias
     * Painel do funcionário: RH ou gerente lança uma diária (funcionario, data, valor [sugere do cadastro], status).
     * Aprovação/pagamento: workflow para RH aprova/paga.
     * Para gerar a folha do diarista: seguro filtrar somente diárias status = 'aprovada' ou 'paga' no mês vigente para somar valores.
     */
    protected $fillable = [
        'funcionario_id', 'data', 'valor', 'status', 'observacao'
    ];

    protected $casts = [
        'data' => 'date',
    ];

    public function funcionario()
    {
        return $this->belongsTo(Funcionario::class, 'funcionario_id');
    }
}