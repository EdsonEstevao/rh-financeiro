<?php

namespace App\Models\Domain\RH;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $funcionario_id
 * @property string|null $telefone
 * @property string|null $celular
 * @property string|null $email
 * @property string|null $email_pessoal
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Domain\RH\Funcionario $funcionario
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioContato newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioContato newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioContato query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioContato whereCelular($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioContato whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioContato whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioContato whereEmailPessoal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioContato whereFuncionarioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioContato whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioContato whereTelefone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuncionarioContato whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class FuncionarioContato extends Model
{
    protected $table = 'funcionario_contatos';

    protected $fillable = [
        'funcionario_id',
        'telefone',
        'celular',
        'email',
        'email_pessoal',
    ];

    protected $casts = [
        'telefone' => 'string',
        'celular' => 'string',
        'email' => 'string',
        'email_pessoal' => 'string',
    ];

    /**
     * Relacionamento com Funcionario
     */
    public function funcionario(): BelongsTo
    {
        return $this->belongsTo(Funcionario::class);
    }
}
