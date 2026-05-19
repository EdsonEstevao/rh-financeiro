<?php

namespace App\Http\Controllers\RH;

use Illuminate\Http\Request;

use App\Events\PeriodoFeriasGozado;
use App\Http\Requests\RH\StorePeriodoFeriasRequest;
use App\Http\Controllers\Controller;
use App\Models\Domain\RH\{Funcionario, PeriodoFerias};
use App\Services\RH\PeriodoFeriasService;

class PeriodoFeriasController extends Controller
{
    // public  $periodoFeriasService;
    // public function __construct(
    //     protected PeriodoFeriasService $periodoService)
    // {

    // }

    public function __construct(
        protected PeriodoFeriasService $periodoService,

    ) {}


    /**
     * Dashboard - Visão geral de férias
     */
    public function dashboard()
    {
        $hoje = now()->startOfDay();
        $daqui30dias = $hoje->copy()->addDays(30);
        $daqui60dias = $hoje->copy()->addDays(60);
        $daqui90dias = $hoje->copy()->addDays(90);

        // ✅ Base query reutilizável para funcionários ativos
        $funcionariosAtivos = fn() => Funcionario::where('ativo', true)
            ->with(['cargo', 'contrato']);

        // Férias vencidas (em dobro)
        $feriasVencidas = $funcionariosAtivos()
            ->where('ferias_vencidas', true)
            ->get();

        // Férias vencendo em 30 dias
        $vencendo30dias = $funcionariosAtivos()
            ->whereBetween('ferias_vencimento', [$hoje, $daqui30dias])
            ->where('ferias_vencidas', false)
            ->get();

        // Férias vencendo em 60 dias
        $vencendo60dias = $funcionariosAtivos()
            ->whereBetween('ferias_vencimento', [$daqui30dias, $daqui60dias])
            ->where('ferias_vencidas', false)
            ->get();

        // Férias vencendo em 90 dias
        $vencendo90dias = $funcionariosAtivos()
            ->whereBetween('ferias_vencimento', [$daqui60dias, $daqui90dias])
            ->where('ferias_vencidas', false)
            ->get();

        // ✅ Próximas férias agendadas - adicionado 'programada' também
        $feriasAgendadas = PeriodoFerias::with('funcionario.cargo')
            ->whereIn('status', ['aprovada', 'planejada'])
            ->where('data_inicio', '>=', $hoje)
            ->orderBy('data_inicio')
            ->limit(10)
            ->get();

        // ✅ Férias em andamento - status pode ser 'gozada' ou 'aprovada' (se já iniciou)
        $feriasEmAndamento = PeriodoFerias::with('funcionario.cargo')
            ->whereIn('status', ['gozada', 'aprovada'])
            ->where('data_inicio', '<=', $hoje)
            ->where('data_fim', '>=', $hoje)
            ->get();

        return view('rh.ferias.dashboard', compact(
            'feriasVencidas',
            'vencendo30dias',
            'vencendo60dias',
            'vencendo90dias',
            'feriasAgendadas',
            'feriasEmAndamento'
        ));
    }

    /**
     * Listagem de todos os períodos de férias
     */
    public function index(Request $request)
    {
        $status = $request->get('status', '');
        $funcionario = $request->get('funcionario', '');

        $query = PeriodoFerias::with(['funcionario.cargo', 'funcionario.contrato'])
            ->orderBy('data_inicio', 'desc');

        // ✅ Filtro por status
        if ($status) {
            $query->where('status', $status);
        }

        // ✅ Filtro por nome do funcionário
        if ($funcionario) {
            $query->whereHas('funcionario', function($q) use ($funcionario) {
                $q->where('nome_completo', 'like', "%{$funcionario}%");
            });
        }

        $periodos = $query->paginate(20)->withQueryString(); // ✅ Mantém filtros na paginação
        $funcionarios = Funcionario::where('ativo', true)->orderBy('nome_completo')->get();

        return view('rh.ferias.index', compact('periodos', 'funcionarios', 'status'));
    }

    /**
     * Formulário de criação de período de férias
     */
    public function create(Funcionario $funcionario)
    {
        $funcionario->load(['cargo', 'contrato', 'periodoFerias' => function($query) {
            $query->orderBy('data_inicio', 'desc');
        }]);

        return view('rh.ferias.create', compact('funcionario'));
    }

    /**
     * Armazenar novo período de férias
     */
    public function store(StorePeriodoFeriasRequest $request, Funcionario $funcionario)
    {
        $periodo = $this->periodoService->criarPeriodo($funcionario, $request->validated());

        return redirect()
            ->route('rh.ferias.index')
            ->with('success', "Período de férias criado para {$funcionario->nome_completo}!");
    }

    /**
     * Exibir período de férias específico
     */
    public function show(PeriodoFerias $periodo)
    {
        $periodo->load(['funcionario.cargo', 'funcionario.contrato']);

        $dias = $this->periodoService->diasDoPeriodo($periodo);

        return view('rh.ferias.show', compact('periodo', 'dias'));
    }

    /**
     * Editar período de férias
     */
    public function edit(PeriodoFerias $periodo)
    {
        // ✅ Carregar contrato também (necessário para mostrar data_admissao)
        $periodo->load('funcionario.cargo', 'funcionario.contrato');

        return view('rh.ferias.edit', compact('periodo'));
    }

    /**
     * Atualizar período de férias
     */
    public function update(Request $request, PeriodoFerias $periodo)
    {
        $validated = $request->validate([
            'data_inicio'       => 'required|date',
            'data_fim'          => 'required|date|after_or_equal:data_inicio',
            'tipo'              => 'sometimes|in:prevista,programada,efetiva',
            'status'            => 'required|in:planejada,aprovada,gozada,cancelada',
            'abono_pecuniario'  => 'nullable|boolean',
            'observacao'        => 'nullable|string|max:500',
            'ferias_vencimento' => 'nullable|date|after:data_fim',
            'numero_periodo'    => 'nullable|integer|min:1',
        ]);

        $statusAnterior = $periodo->status;

        $this->periodoService->atualizarPeriodo($periodo, $validated);

        // ✅ Dispara evento quando muda para "Gozada"
        if($validated['status'] === 'gozada' && $statusAnterior != 'gozada') {
            event(new PeriodoFeriasGozado($periodo->fresh()));
        }

        // ✅ Atualiza flags do funcionário conforme status
        // $this->atualizarFlagsFerias($periodo->funcionario, $validated['status']);



        return redirect()
            ->route('rh.ferias.index')
            ->with('success', 'Período de férias atualizado com sucesso!');
    }

    /**
     * Gerar novo período de férias para um funcionário (atalho do RH)
     */
    public function gerarNovoPeriodo(Request $request, Funcionario $funcionario)
    {
        $validated = $request->validate([
            'data_inicio'       => 'required|date',
            'data_fim'          => 'required|date|after_or_equal:data_inicio',
            'observacao'        => 'nullable|string|max:500',
            'abono_pecuniario'  => 'nullable|boolean',
            'ferias_vencimento' => 'nullable|date|after:data_fim',
        ]);

        // ✅ Garante que seja programada e planejada
        $validated['tipo'] = 'programada';
        $validated['status'] = 'planejada';

        $periodo = $this->periodoService->criarPeriodo($funcionario, $validated);

        return redirect()
            ->route('rh.ferias.index')
            ->with('success', "Novo período de férias gerado para {$funcionario->nome_completo}!");
    }

    /**
     * Cancelar período de férias
     */
    public function cancelar(PeriodoFerias $periodo)
    {
        if ($periodo->status === 'gozada') {
            return redirect()
                ->back()
                ->with('error', 'Não é possível cancelar um período de férias já gozado.');
        }

        $periodo->update([
            'status' => 'cancelada',
            'observacao' => trim(($periodo->observacao ?? '') . ' | Cancelado em ' . now()->format('d/m/Y')),
        ]);

        return redirect()
            ->route('rh.ferias.index')
            ->with('success', 'Período de férias cancelado com sucesso!');
    }

    /**
     * Remover período de férias
     */
    public function destroy(PeriodoFerias $periodo)
    {
        if ($periodo->status === 'gozada') {
            return redirect()
                ->back()
                ->with('error', 'Não é possível excluir um período de férias já gozado.');
        }

        $nomeFuncionario = $periodo->funcionario->nome_completo;
        $periodo->delete();

        return redirect()
            ->route('rh.ferias.index')
            ->with('success', "Período de férias de {$nomeFuncionario} excluído com sucesso!");
    }

    /**
     * ✅ Atualiza flags de férias do funcionário baseado no status do período
     */
    private function atualizarFlagsFerias(Funcionario $funcionario, string $status): void
    {
        match ($status) {
            'gozada' => $funcionario->update([
                'ferias_vencidas' => false,
                'ferias_em_dobro' => false,
            ]),
            'cancelada' => $funcionario->update([
                'ferias_vencidas' => true,  // Volta a ficar vencida se foi cancelada
            ]),
            default => null // planejada, aprovada - não altera flags
        };
    }
}