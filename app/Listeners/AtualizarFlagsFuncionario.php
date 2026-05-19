<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Log;

use App\Events\PeriodoFeriasGozado;

class AtualizarFlagsFuncionario
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PeriodoFeriasGozado $event): void
    {
        $periodo = $event->periodo;
        $funcionario = $periodo->funcionario;

        $funcionario->update([
            'ferias_vencidas' => false,
            'ferias_gozadas' => false,
        ]);


        Log::info("✅ Flags de férias atualizadas para funcionário #{$funcionario->id} ({$funcionario->nome_completo})");
    }
}
