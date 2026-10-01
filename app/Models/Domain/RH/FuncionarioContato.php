<?php

namespace App\Models\Domain\RH;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $funcionario_id
 * @property string|null $telefone
 * @property string|null $celular
 * @property string|null $email
 * @property string|null $email_pessoal
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Funcionario $funcionario
 *
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
 *
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

    protected function celular(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => preg_replace('/\D/', '', $value), // remove tudo que não é número
            get: fn ($value) => $this->formatarTelefone($value),
        );
    }

    protected function telefone(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => $value ? preg_replace('/\D/', '', $value) : null,
            get: fn ($value) => $value ? $this->formatarTelefone($value) : null,
        );
    }

    private function formatarTelefone(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        if (strlen($value) === 11) {
            return sprintf('(%s) %s %s-%s',
                substr($value, 0, 2),
                substr($value, 2, 1),
                substr($value, 3, 4),
                substr($value, 7, 4)
            );
        }

        return $value;
    }
}
