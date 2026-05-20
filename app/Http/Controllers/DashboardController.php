<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Spatie\Activitylog\Models\Activity;

use App\Models\Domain\RH\{Departamento, FolhaPagamento, Funcionario, PeriodoFerias};

class DashboardController extends Controller
{
    public function index()
    {
        $hoje = now()->startOfDay();

        // 📊 Cards principais
        $totalFuncionarios = Funcionario::where('ativo', true)->count();
        $totalAtivos = Funcionario::where('ativo', true)->count();

        $feriasHoje = PeriodoFerias::whereIn('status', ['gozada', 'aprovada'])
            ->where('data_inicio', '<=', $hoje)
            ->where('data_fim', '>=', $hoje)
            ->count();

        $folhaMesCount = FolhaPagamento::whereMonth('competencia', now()->month)
            ->whereYear('competencia', now()->year)
            ->count();

        $alertasFerias = Funcionario::where('ativo', true)
            ->where('ferias_vencidas', true)
            ->count();

        // 📅 Próximas férias (30 dias)
        $proximasFerias = PeriodoFerias::with(['funcionario.departamento'])
            ->whereIn('status', ['aprovada', 'planejada'])
            ->where('data_inicio', '>=', $hoje)
            ->where('data_inicio', '<=', $hoje->copy()->addDays(30))
            ->orderBy('data_inicio')
            ->limit(8)
            ->get();

        // 🎂 Aniversariantes do mês
        $aniversariantes = Funcionario::with('departamento')
            ->where('ativo', true)
            ->whereMonth('data_nascimento', now()->month)
            ->orderByRaw('DAY(data_nascimento)')
            ->limit(8)
            ->get();

        // 📝 Últimas atividades
        $ultimasAtividades = Activity::with('causer')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // 🏢 Departamentos
        $departamentos = Departamento::withCount(['funcionarios' => function($q) {
                $q->where('ativo', true);
            }])
            ->orderBy('nome')
            ->get();

        // 🔔 NOVO: Funcionários com direito a férias (período aquisitivo completado)
        // 🔔 Funcionários com direito a férias (período aquisitivo completado)
        $feriasDireito = Funcionario::with(['cargo', 'departamento', 'contrato'])
                        ->where('ativo', true)
                        ->whereNotNull('periodo_aquisitivo_fim')
                        ->where(function($query) use ($hoje) {
                            $query->where('periodo_aquisitivo_fim', '<=', $hoje->copy()->addDays(30)->toDateString())
                                ->where('periodo_aquisitivo_fim', '>=', $hoje->copy()->subDays(365)->toDateString());
                        })
                        ->where('ferias_vencidas', false)
                        // ✅ Corrigido
                        ->whereDoesntHave('periodoFerias', function($query) {
                            $query->where(function($q) {
                                $q->where('tipo', 'programada')
                                ->where('status', 'aprovada');
                            })->orWhere('status', 'gozada');
                        })
                        ->get()
                        ->map(function($funcionario) use ($hoje) {
                            $fimPeriodo = Carbon::parse($funcionario->periodo_aquisitivo_fim);
                            $diasRestantes = $hoje->diffInDays($fimPeriodo, false);
                            
                            $funcionario->dias_para_vencer = $diasRestantes;
                            $funcionario->status_alerta = match(true) {
                                $diasRestantes <= 0 => 'disponivel',
                                $diasRestantes <= 15 => 'urgente',
                                $diasRestantes <= 30 => 'atencao',
                                default => 'normal'
                            };
                            
                            return $funcionario;
                        })
                        ->sortBy('dias_para_vencer');


        return view('dashboard', compact(
            'totalFuncionarios',
            'totalAtivos',
            'feriasHoje',
            'folhaMesCount',
            'alertasFerias',
            'proximasFerias',
            'aniversariantes',
            'ultimasAtividades',
            'departamentos',
            'feriasDireito'
        ));
    }
}