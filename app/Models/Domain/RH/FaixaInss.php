<?php

namespace App\Models\Domain\RH;

use Illuminate\Database\Eloquent\Model;

use App\Models\Domain\RH\TabelaInss;

class FaixaInss extends Model
{
    protected $table = 'faixas_inss';
    protected $fillable = [
        'tabela_inss_id',
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
        return $this->belongsTo(TabelaInss::class, 'tabela_inss_id');
    }
}