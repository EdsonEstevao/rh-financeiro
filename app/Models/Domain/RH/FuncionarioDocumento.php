<?php

namespace App\Models\Domain\RH;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $funcionario_id
 * @property string|null $rg
 * @property string|null $orgao_expedidor_rg
 * @property string $cpf
 * @property string|null $titulo_eleitor
 * @property string|null $zona_eleitoral
 * @property string|null $secao_eleitoral
 * @property string|null $certificado_reservista
 * @property string|null $ctps_numero
 * @property string|null $ctps_serie
 * @property string|null $ctps_uf
 * @property \Illuminate\Support\Carbon|null $ctps_data_emissao
 * @property string|null $pis_pasep
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Domain\RH\Funcionario $funcionario
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioDocumento newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioDocumento newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioDocumento query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioDocumento whereCertificadoReservista($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioDocumento whereCpf($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioDocumento whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioDocumento whereCtpsDataEmissao($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioDocumento whereCtpsNumero($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioDocumento whereCtpsSerie($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioDocumento whereCtpsUf($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioDocumento whereFuncionarioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioDocumento whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioDocumento whereOrgaoExpedidorRg($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioDocumento wherePisPasep($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioDocumento whereRg($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioDocumento whereSecaoEleitoral($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioDocumento whereTituloEleitor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioDocumento whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioDocumento whereZonaEleitoral($value)
 * @mixin \Eloquent
 */
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
