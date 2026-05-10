<?php

namespace App\Services\RH;

use Illuminate\Support\Facades\{DB, Log};

use App\Models\Domain\RH\Funcionario;

class FuncionarioService
{
    /**
     * Criar funcionário com validações e cálculo automático de férias
     */
    public function criarFuncionario(array $dados): Funcionario
    {
        return DB::transaction(function () use ($dados) {
            // Validações adicionais de negócio
            $this->validarIdadeMinima($dados['data_nascimento']);
            $this->validarCPF($dados['cpf']);

            // Criar funcionário (o boot do model já calcula férias automaticamente)
            $funcionario = Funcionario::create($dados);

            // Log da operação
            Log::info('Funcionário cadastrado', [
                'funcionario_id' => $funcionario->id,
                'nome' => $funcionario->nome_completo,
                'data_admissao' => $funcionario->data_admissao,
                'ferias_vencimento' => $funcionario->ferias_vencimento
            ]);

            return $funcionario;
        });
    }

    /**
     * Atualizar funcionário
     */
    public function atualizarFuncionario(Funcionario $funcionario, array $dados): Funcionario
    {
        return DB::transaction(function () use ($funcionario, $dados) {
            $funcionario->update($dados);

            // Se mudou data de admissão, recalcular férias foi feito automaticamente no boot
            if (array_key_exists('data_admissao', $dados)) {
                Log::info('Período de férias recalculado', [
                    'funcionario_id' => $funcionario->id,
                    'nova_data_admissao' => $dados['data_admissao'],
                    'novo_vencimento_ferias' => $funcionario->ferias_vencimento
                ]);
            }

            return $funcionario->fresh();
        });
    }

    /**
     * Validar idade mínima (14 anos para aprendiz, 16 para CLT)
     */
    private function validarIdadeMinima(string $dataNascimento): void
    {
        $idade = now()->diffInYears($dataNascimento);

        if ($idade < 14) {
            throw new \InvalidArgumentException('Funcionário deve ter pelo menos 14 anos.');
        }
    }

    /**
     * Validação básica de CPF (algoritmo de dígitos verificadores)
     */
    private function validarCPF(string $cpf): void
    {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);

        if (strlen($cpf) !== 11) {
            throw new \InvalidArgumentException('CPF deve ter 11 dígitos.');
        }

        // Verificar se não é uma sequência de números iguais
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            throw new \InvalidArgumentException('CPF inválido.');
        }

        // Validação dos dígitos verificadores (algoritmo padrão)
        for ($i = 9; $i < 11; $i++) {
            $soma = 0;
            for ($j = 0; $j < $i; $j++) {
                $soma += $cpf[$j] * (($i + 1) - $j);
            }
            $resto = $soma % 11;
            $digito = $resto < 2 ? 0 : 11 - $resto;

            if ($cpf[$i] != $digito) {
                throw new \InvalidArgumentException('CPF inválido.');
            }
        }
    }
}
