<?php

namespace App\Models\Domain\RH;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class FaixaIrrf extends Model
{
    use HasFactory;

    protected $table = 'faixas_irrf';

    protected $fillable = [
        'tabela_irrf_id',
        'ordem',
        'teto',
        'aliquota',
        'deducao',
    ];

    protected $casts = [
        'ordem' => 'integer',
        'teto' => 'decimal:2',
        'aliquota' => 'decimal:4',
        'deducao' => 'decimal:2',
    ];

    public function tabela()
    {
        return $this->belongsTo(TabelaIrrf::class, 'tabela_irrf_id');
    }
}