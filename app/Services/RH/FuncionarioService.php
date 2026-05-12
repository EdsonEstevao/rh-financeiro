<?php

namespace App\Services\RH;

use Illuminate\Support\Facades\{DB, Log};
use Illuminate\Support\Carbon;

use App\Models\Domain\RH\Funcionario;

class FuncionarioService
{
    public function __construct(protected PeriodoFeriasService $periodoFeriasService) {}
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

            /**
             * Cria funcionário e já registra um período de férias prevista (planejada)
             * 12 meses após a admissão, por padrão 30 dias corridos.
             */
            $this->criarFeriasPrevistaAoAdmitir($funcionario);

            return $funcionario;
        });
    }

    private function criarFeriasPrevistaAoAdmitir(Funcionario $funcionario): void
    {
        $admissao = Carbon::parse($funcionario->data_admissao)->startOfDay();

        // 12 meses depois, preservando o "dia" quando possível
        // addYearNoOverflow evita cair em mês inválido (ex.: 31/03 -> 31/03 ok; 31/01 -> 31/01 ok; 31/08 -> 31/08 ok)
        // mas para fevereiro, ele ajusta para o último dia do mês.
        $inicioPrevisto = $admissao->copy()->addYearNoOverflow();

        // Padrão: 30 dias corridos (ajuste se sua regra for 30 "dias de férias" e não corridos)
        $fimPrevisto = $inicioPrevisto->copy()->addDays(29);

        // Evitar duplicar caso o fluxo rode novamente
        $jaExistePrevista = $funcionario->periodosFerias()
            ->where('status', 'planejada')
            ->whereDate('data_inicio', $inicioPrevisto->toDateString())
            ->whereDate('data_fim', $fimPrevisto->toDateString())
            ->exists();

        if ($jaExistePrevista) {
            return;
        }

        $this->periodoFeriasService->criarPeriodo($funcionario, [
            'data_inicio' => $inicioPrevisto->toDateString(),
            'data_fim'    => $fimPrevisto->toDateString(),
            'status'      => 'planejada',
            'observacao'  => 'Gerado automaticamente na admissão (férias previstas).',
            'numero_periodo' => 1,
            'abono_pecuniario' => false,
        ]);
    }

    /**
     * Atualiza funcionário e recalcula férias prevista se a data de admissão mudou
     */
    public function atualizar(Funcionario $funcionario, array $dados): Funcionario
    {
        return DB::transaction(function () use ($funcionario, $dados) {
            $dataAdmissaoAnterior = $funcionario->data_admissao;

            $funcionario->update($dados);

            // Se mudou a data de admissão, recalcula as férias previstas
            if (isset($dados['data_admissao']) && $dados['data_admissao'] != $dataAdmissaoAnterior) {
                $this->criarOuAtualizarFeriasPrevista($funcionario->fresh());
            }

            return $funcionario->fresh();
        });
    }

    /**
     * Cria ou atualiza a férias prevista baseada na data de admissão
     * Regra: 12 meses após admissão, sempre 30 dias corridos
     */
    private function criarOuAtualizarFeriasPrevista(Funcionario $funcionario): void
    {
        $admissao = Carbon::parse($funcionario->data_admissao)->startOfDay();

        // 12 meses depois, preservando o "dia" quando possível
        $inicioPrevisto = $admissao->copy()->addYearNoOverflow();

        // Sempre 30 dias corridos (inicio + 29)
        $fimPrevisto = $inicioPrevisto->copy()->addDays(29);

        // Busca se já existe uma férias prevista para este funcionário
        $feriasPrevista = $funcionario->periodosFerias()
            ->where('tipo', 'prevista')
            ->first();

        $dadosPeriodo = [
            'data_inicio' => $inicioPrevisto->toDateString(),
            'data_fim'    => $fimPrevisto->toDateString(),
            'tipo'        => 'prevista',
            'status'      => 'planejada',
            'observacao'  => 'Gerado automaticamente baseado na data de admissão.',
            'numero_periodo' => 1,
            'abono_pecuniario' => false,
        ];

        if ($feriasPrevista) {
            // ATUALIZA a existente
            $this->periodoFeriasService->atualizarPeriodo($feriasPrevista, $dadosPeriodo);
        } else {
            // CRIA nova
            $this->periodoFeriasService->criarPeriodo($funcionario, $dadosPeriodo);
        }
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