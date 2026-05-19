<?php

namespace App\Listeners;

use Illuminate\Support\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

use App\Services\RH\PeriodoFeriasService;
use App\Events\PeriodoFeriasGozado;

class GerarProximoPeriodoAquisitivo implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct(private PeriodoFeriasService $periodoService)
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PeriodoFeriasGozado $event): void
    {
        //
        $periodo = $event->periodo;
        $funcionario = $periodo->funcionario->load('contrato');

        $dataFimFerias = Carbon::parse($periodo->data_fim);

        // Novo periodo aquisitivo
        $novoInicio = $dataFimFerias->copy()->addDays()->startOfDay();
        $novoFim = $novoInicio->copy()->addYear()->subDay()->startOfDay();
        $novoVencimento = $novoFim->copy()->addYear()->startOfDay();

        // Atualiza Funcionario
        $funcionario->update([
            'periodo_aquisitivo_inicio' => $novoInicio->toDateString(),
            'periodo_aquisitivo_fim' => $novoFim->toDateString(),
            'ferias_vencimento' => $novoVencimento->toDateString(),//$novoFim->copy()->addYear()->toDateString(),
        ]);

        // ✅ Cria periodo "prevista" para o funcionário automaticamente
        $novoNumero = $funcionario->periodoFerias()->max('numero_periodo') + 1;

        $data = [
            'data_inicio' => $novoInicio->toDateString(),
            'data_fim' => $novoFim->copy()->addDays(29)->toDateString(),
            'tipo' => 'prevista',
            'status' => 'planejada',
            'abono_pecuniario' => false,
            'numero_periodo' => $novoNumero,
            'observacao' => 'Gerado automaticamente após gozar férias em '.$dataFimFerias->format('d/m/Y')
        ];

        // Cria Periodo Aquisitivo
        $novoPeriodo = $this->periodoService->criarPeriodo($funcionario, $data);

        Log::info("👨 Novo período aquisitivo gerado para funcionario #{$funcionario->nome_completo}: {$novoInicio->format('d/m/Y')} a {$novoFim->format('d/m/Y')}");
        Log::info("📅 Novo período aquisitivo #{$novoPeriodo->id} gerado para funcionário #{$funcionario->id} ({$funcionario->nome_completo})");
        Log::info("   Período: {$novoInicio->format('d/m/Y')} a {$novoFim->format('d/m/Y')}");
        Log::info("   Vencimento: {$novoVencimento->format('d/m/Y')}");
    }
}
