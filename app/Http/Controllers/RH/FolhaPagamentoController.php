<?php

namespace App\Http\Controllers\RH;

use Illuminate\Http\Request;
use Illuminate\Support\{Carbon, Collection};

use App\Http\Controllers\Controller;
use App\Models\Domain\RH\{Cargo, Departamento, Funcionario};
use App\Services\RH\FolhaPagamentoService;
use Barryvdh\DomPDF\Facade\Pdf;
class FolhaPagamentoController extends Controller
{
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

        $departamentos = Departamento::qurey()->where('ativo', true)->orderBy('nome')->get();
        $cargos = Cargo::query()->where('ativo', true)->orderBy('titulo')->get();
        $locaisTrabalho = Funcionario::query()->where('ativo', true)
                                   ->whereNotNull('local_trabalho')
                                   ->distinct()
                                   ->pluck('local_trabalho');

        return view('rh.folha-pagamento.index', compact(
            'funcionarios', 'departamentos', 'cargos', 'locaisTrabalho', 'totalizadores'
        ));
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
