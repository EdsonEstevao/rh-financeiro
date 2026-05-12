<?php

namespace App\Http\Controllers\RH;

use Illuminate\Http\{RedirectResponse, Request};
use Illuminate\Support\{Carbon, Collection};
use Illuminate\View\View;

use App\Http\Controllers\Controller;
use App\Models\Domain\RH\{Cargo, Departamento, FolhaPagamento, Funcionario};
use App\Services\RH\FolhaPagamentoService;
use Barryvdh\DomPDF\Facade\Pdf;

class FolhaPagamentoController extends Controller
{
    public function __construct(
        protected FolhaPagamentoService $folhaService
    ) {}
    //
    public function index(Request $request)
    {
        $funcionarios = Funcionario::query()
            ->with(['departamento', 'cargo'])
            ->when($request->filled('departamento_id'), function ($query) use ($request) {
                $query->where('departamento_id', $request->departamento_id);
            })
            ->when($request->filled('cargo_id'), function ($query) use ($request) {
                $query->where('cargo_id', $request->cargo_id);
            })
            ->when($request->filled('local_trabalho'), function ($query) use ($request) {
                $query->where('local_trabalho', 'like', "%{$request->local_trabalho}%");
            })
            ->when($request->boolean('apenas_ativos'), function ($query) {
                $query->where('ativo', true);
            })
            ->orderBy('nome_completo')
            ->paginate(20)
            ->withQueryString();

        // Totalizadores
        $totalizadores = $this->calcularTotalizadores($funcionarios->getCollection());

        $departamentos = Departamento::query()->where('ativo', true)->orderBy('nome')->get();
        $cargos = Cargo::query()->where('ativo', true)->orderBy('titulo')->get();
        $locaisTrabalho = Funcionario::query()->where('ativo', true)
                                   ->whereNotNull('local_trabalho')
                                   ->distinct()
                                   ->pluck('local_trabalho');

        // Usando a nova view
        return view('rh.funcionarios.folha-index', compact(
            'funcionarios', 'departamentos', 'cargos', 'locaisTrabalho', 'totalizadores'
        ));
        // return view('rh.funcionarios.index', compact(
        //     'funcionarios', 'departamentos', 'cargos', 'locaisTrabalho', 'totalizadores'
        // ));
    }

    /**
     * Calcula os totalizadores da lista de funcionários.
     */
    // private function calcularTotalizadores( $funcionarios): array
    // {
    //     return [
    //         'total_funcionarios' => $funcionarios->count(),
    //         'total_salario_base' => $funcionarios->sum('salario_base'),
    //         'media_salarial' => $funcionarios->avg('salario_base'),
    //         'total_ativos' => $funcionarios->where('ativo', true)->count(),
    //     ];
    // }

    /**
     * Exibe o formulário para calcular folha.
     */
    public function calcular()
    {
        $funcionarios = Funcionario::query()->where('ativo', true)->get();
        return view('rh.folha-pagamento.calcular', compact('funcionarios'));
    }

    /**
     * Exibe detalhes completos de um funcionário para folha
     */
    public function show(Funcionario $funcionario)
    {
        $funcionario->load(['departamento', 'cargo', 'usuario']);

        return view('rh.folha-pagamento.show', compact('funcionario'));
    }

     /**
     * Lista folhas de pagamento
     */
    // public function index(Request $request): View
    // {
    //     $folhas = FolhaPagamento::with('holerites.funcionario')
    //         ->orderBy('competencia', 'desc')
    //         ->paginate(12);

    //     return view('rh.folhas.index', compact('folhas'));
    // }

    // /**
    //  * Exibe detalhes da folha
    //  */
    // public function show(FolhaPagamento $folha): View
    // {
    //     $folha->load(['holerites.funcionario']);

    //     $totais = [
    //         'funcionarios' => $folha->holerites()->count(),
    //         'salario_bruto' => $folha->totalSalarioBruto(),
    //         'inss' => $folha->totalInss(),
    //         'irrf' => $folha->totalIrrf(),
    //         'salario_liquido' => $folha->totalSalarioLiquido(),
    //     ];

    //     return view('rh.folhas.show', compact('folha', 'totais'));
    // }

    /**
     * Formulário para criar nova folha
     */
    public function create(): View
    {
        return view('rh.folha-pagamento.create');
    }

    /**
     * Cria nova folha para uma competência
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'competencia' => ['required', 'date', 'date_format:Y-m-d'],
        ]);

        try {
            $folha = $this->folhaService->criarOuBuscarFolha($request->competencia);

            return redirect()
                ->route('rh.folhas.show', $folha)
                ->with('success', 'Folha de pagamento criada com sucesso!');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erro ao criar folha: ' . $e->getMessage());
        }
    }

    /**
     * Gera holerites para a folha
     */
    public function gerarHolerites(FolhaPagamento $folha, Request $request): RedirectResponse
    {
        $recalcular = $request->boolean('recalcular', false);

        try {
            $resultado = $this->folhaService->gerarHolerites($folha, $recalcular);

            $message = "Holerites processados: {$resultado['processados']}";

            if (!empty($resultado['erros'])) {
                $message .= ". Erros: " . count($resultado['erros']);
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao gerar holerites: ' . $e->getMessage());
        }
    }

    /**
     * Fecha a folha
     */
    public function fechar(FolhaPagamento $folha): RedirectResponse
    {
        try {
            $this->folhaService->fecharFolha($folha);

            return back()->with('success', 'Folha fechada com sucesso!');

        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao fechar folha: ' . $e->getMessage());
        }
    }

    /**
     * Reabre a folha
     */
    public function reabrir(FolhaPagamento $folha): RedirectResponse
    {
        try {
            $this->folhaService->reabrirFolha($folha);

            return back()->with('success', 'Folha reaberta com sucesso!');

        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao reabrir folha: ' . $e->getMessage());
        }
    }

    /**
     * Relatório resumido da folha de pagamento
     */
    public function resumo(Request $request)
    {
        $funcionarios = Funcionario::ativos()->with(['departamento', 'cargo'])->get();

        $resumo = [
            'total_funcionarios' => $funcionarios->count(),
            'folha_bruta' => $funcionarios->sum('salario_bruto'),
            'total_descontos' => $funcionarios->sum('total_descontos'),
            'folha_liquida' => $funcionarios->sum('salario_liquido'),
            'total_inss' => $funcionarios->sum('desconto_inss_8_porcento'),
            'total_vale_transporte' => $funcionarios->sum('valor_vale_transporte'),
            'total_vale_alimentacao' => $funcionarios->sum('valor_vale_alimentacao'),
            'total_horas_extras' => $funcionarios->sum('hora_extra'),
            'total_salario_familia' => $funcionarios->sum('salario_familia'),
        ];

        return view('rh.folha-pagamento.resumo', compact('funcionarios', 'resumo'));
    }

    /**
     * Calcula totalizadores da folha
     */
    private function calcularTotalizadores(Collection $funcionarios): array
    {
        return [
            'total_funcionarios' => $funcionarios->count(),
            'soma_salario_base' => $funcionarios->sum('salario_base'),
            'soma_bruto' => $funcionarios->sum('salario_bruto'),
            'soma_descontos' => $funcionarios->sum('total_descontos'),
            'soma_liquido' => $funcionarios->sum('salario_liquido'),
            'soma_inss' => $funcionarios->sum('desconto_inss_8_porcento'),
            'soma_horas_extras' => $funcionarios->sum('hora_extra'),
            'soma_salario_familia' => $funcionarios->sum('salario_familia'),
        ];
    }

    public function folhaDiarista(int $funcionarioId, string $ano, string $mes, FolhaPagamentoService $folhaService)
    {
        $funcionario = Funcionario::findOrFail($funcionarioId);

        // Primeiro/ultimo dia do mês
        $inicioMes = Carbon::create($ano, $mes, 1)->startOfMonth();
        $fimMes    = Carbon::create($ano, $mes, 1)->endOfMonth();

        $total = $folhaService->calcularFolhaDiarista($funcionario, $inicioMes, $fimMes);

        // montar view ou retornar JSON
        return response()->json([
            'funcionario' => $funcionario->nome,
            'valor_total' => $total,
        ]);
    }

    public function exportarFolhaDiaristasPdf(Request $request, FolhaPagamentoService $folhaService)
    {
        $inicio = $request->input('inicio') ?? now()->startOfMonth()->toDateString();
        $fim    = $request->input('fim') ?? now()->endOfMonth()->toDateString();

        $relatorio = $folhaService->gerarRelatorioDiaristas($inicio, $fim);

        $data = [
            'relatorio' => $relatorio,
            'inicio'    => $inicio,
            'fim'       => $fim,
        ];

        $pdf = Pdf::loadView('rh.folha_diaristas_pdf', $data);

        $periodo = Carbon::parse($inicio)->format('m-Y');
        return $pdf->download("folha_diaristas_$periodo.pdf");
    }
}
