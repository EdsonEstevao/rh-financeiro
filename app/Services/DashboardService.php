<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;

use App\Models\User;
use App\Models\Domain\RH\{FolhaPagamento, Funcionario, PeriodoFerias};

class DashboardService
{

public function getData(): array
    {
        $now = now();
        $inicioMes = $now->copy()->startOfMonth();
        $fimMes = $now->copy()->endOfMonth();

        // Usuários / Acessos (sessions) — só se usar database session driver
        $totalUsers = User::count();

        $onlineUsers = $this->countOnlineUsers();

        // RH (do seu DDL)
        $funcAtivos = Funcionario::where('ativo', 1)->count();
        $funcInativos = Funcionario::where('ativo', 0)->count();

        $feriasProximos30 = PeriodoFerias::whereBetween('data_inicio', [$now->toDateString(), $now->copy()->addDays(30)->toDateString()])
            ->whereIn('status', ['planejada','aprovada'])
            ->count();

        $folhasAbertasMes = FolhaPagamento::whereBetween('competencia', [$inicioMes->toDateString(), $fimMes->toDateString()])
            ->where('status', 'aberta')
            ->count();

        $folhasFechadasMes = FolhaPagamento::whereBetween('competencia', [$inicioMes->toDateString(), $fimMes->toDateString()])
            ->where('status', 'fechada')
            ->count();

        // Auditoria (activity_log)
        $logsUltimas24h = DB::table('activity_log')
            ->where('created_at', '>=', $now->copy()->subDay())
            ->count();

        $topEventosLog = DB::table('activity_log')
            ->select('event', DB::raw('COUNT(*) as total'))
            ->where('created_at', '>=', $now->copy()->subDays(7))
            ->groupBy('event')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // Jobs falhos
        $failedJobs = DB::table('failed_jobs')->count();

        // Cache locks (se usar)
        $cacheLocks = DB::table('cache_locks')->count();

        return [
            'totais' => [
                'users' => $totalUsers,
                'online_users' => $onlineUsers,
                'func_ativos' => $funcAtivos,
                'func_inativos' => $funcInativos,
                'ferias_30d' => $feriasProximos30,
                'folhas_abertas_mes' => $folhasAbertasMes,
                'folhas_fechadas_mes' => $folhasFechadasMes,
                'logs_24h' => $logsUltimas24h,
                'failed_jobs' => $failedJobs,
                'cache_locks' => $cacheLocks,
            ],
            'charts' => [
                'top_eventos_log_7d' => $topEventosLog,
            ],
        ];
    }

    private function countOnlineUsers(): int
    {
        // Só funciona se sessions estiver no banco e tiver user_id e last_activity
        // Considera online quem teve atividade nos últimos 10 min.
        $threshold = now()->subMinutes(10)->timestamp;

        try {
            return DB::table('sessions')
                ->whereNotNull('user_id')
                ->where('last_activity', '>=', $threshold)
                ->distinct('user_id')
                ->count('user_id');
        } catch (\Throwable $e) {
            return 0; // caso não esteja usando sessions em DB
        }
    }
}