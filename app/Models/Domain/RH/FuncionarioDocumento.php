<?php

namespace App\Models\Domain\RH;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FuncionarioDocumento extends Model
{
    protected $table = 'funcionario_documentos';

    protected $fillable = [
        'funcionario_id',
        'rg',
        'orgao_expedidor_rg',
        'cpf',
        'titulo_eleitor',
        'zona_eleitoral',
        'secao_eleitoral',
        'certificado_reservista',
        'ctps_numero',
        'ctps_serie',
        'ctps_uf',
        'ctps_data_emissao',
        'pis_pasep',
    ];

    protected $casts = [
        'ctps_data_emissao' => 'date',
    ];

    public function funcionario(): BelongsTo
    {
        return $this->belongsTo(Funcionario::class);
    }
}
