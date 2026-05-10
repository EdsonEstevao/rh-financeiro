<?php
// app/Console/Commands/AtualizarStatusFerias.php

namespace App\Console\Commands;



// use Symfony\Component\Console\Command\Command;

use Illuminate\Console\Command;

use App\Models\Domain\RH\Funcionario;

class AtualizarStatusFerias extends Command
{
    protected $signature = 'rh:atualizar-ferias';
    protected $description = 'Atualiza status de férias vencidas e em dobro de todos os funcionários ativos';

    public function handle()
    {
        $funcionarios = Funcionario::query()->where('ativo', true)->get();
        $atualizados = 0;

        foreach ($funcionarios as $funcionario) {
            $statusAnterior = $funcionario->ferias_em_dobro;
            $funcionario->verificarFeriasEmDobro();

            if ($funcionario->isDirty('ferias_em_dobro')) {

                $funcionario->save();
                $atualizados++;

                if ($funcionario->ferias_em_dobro && !$statusAnterior) {
                    $this->warn("⚠️  {$funcionario->nome_completo} - Férias agora EM DOBRO!");
                }
            }
        }

        $this->info("✅ Status de férias atualizado para {$atualizados} funcionários.");

        // Relatório de férias vencendo nos próximos 30 dias
        $vencendo = Funcionario::feriasVencendo(30)->count();
        $vencidas = Funcionario::feriasVencidas()->count();

        $this->table(
            ['Status', 'Quantidade'],
            [
                ['Férias vencendo (30 dias)', $vencendo],
                ['Férias vencidas (em dobro)', $vencidas]
            ]
        );

        return Command::SUCCESS; //::SUCCESS;
    }
}
