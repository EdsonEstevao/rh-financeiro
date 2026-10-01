<?php

namespace App\Http\Controllers\RH;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use App\Http\Controllers\Controller;
use App\Http\Requests\RH\{FuncionarioStoreRequest, FuncionarioUpdateRequest};
use App\Models\Domain\RH\{Cargo, Departamento, Funcionario};
use App\Services\RH\{FuncionarioService, PeriodoFeriasService};

class FuncionarioController extends Controller
{
    //
    public function __construct(
        private FuncionarioService $funcionarioService,
        private PeriodoFeriasService $periodoService
    ) {}

    public function index(Request $request)
    {
        $funcionarios = Funcionario::query()
            ->with(['departamento', 'cargo', 'documentos'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('nome_completo', 'like', "%{$request->search}%")
                    ->orWhereHas('documentos', function ($q) use ($request) {
                        $q->where('cpf', 'like', "%{$request->search}%");
                    });
            })
            ->when($request->filled('departamento_id'), function ($query) use ($request) {
                $query->where('departamento_id', $request->departamento_id);
            })
            ->when($request->filled('cargo_id'), function ($query) use ($request) {
                $query->where('cargo_id', $request->cargo_id);
            })
            // ✅ Filtro de Status (Ativo/Inativo/Todos)
            ->when($request->filled('status'), function ($query) use ($request) {
                if ($request->status === 'ativo') {
                    $query->where('ativo', true);
                } elseif ($request->status === 'inativo') {
                    $query->where('ativo', false);
                }
                // Se for 'todos', não aplica filtro
            })
            // ✅ Removeu o filtro 'apenas_ativos' antigo (substituído pelo status)
            ->when($request->boolean('ferias_vencendo'), function ($query) {
                $query->feriasVencendo(30);
            })
            ->orderBy('nome_completo')
            ->paginate(20)
            ->withQueryString();

        $departamentos = Departamento::query()->where('ativo', true)->orderBy('nome', 'desc')->get();
        $cargos = Cargo::query()->where('ativo', true)->orderBy('titulo')->get();

        return view('rh.funcionarios.index', compact('funcionarios', 'departamentos', 'cargos'));
    }

    public function create()
    {

        $departamentos = Departamento::query()->where('ativo', true)->orderBy('nome')->get();
        $cargos = Cargo::query()->where('ativo', true)->orderBy('titulo')->get();

        return view('rh.funcionarios.create', compact('departamentos', 'cargos'));
    }

    public function store(FuncionarioStoreRequest $request)
    // public function store(Request $request)
    {

        try {
            $funcionario = $this->funcionarioService->criarFuncionario($request->validated());

            // ✅ Se clicou em "Salvar e Continuar", redireciona para edição
            $departamentos = Departamento::query()->where('ativo', true)->orderBy('nome')->get();
            $cargos = Cargo::query()->where('ativo', true)->orderBy('titulo')->get();
            if ($request->action === 'continue') {
                return redirect()
                    ->route('rh.funcionarios.create', compact('departamentos', 'cargos'))
                    ->with('success', "Funcionário {$funcionario->nome_completo} cadastrado! Continue preenchendo os dados.");
            }

            // Padrão: redireciona para a listagem
            return redirect()
                ->route('rh.funcionarios.index')
                ->with('success', "Funcionário {$funcionario->nome_completo} cadastrado com sucesso!");

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erro ao cadastrar funcionário: '.$e->getMessage());
        }
    }

    public function show(Funcionario $funcionario)
    {
        // $id = $funcionario->id;

        $funcionario->loadMissing(['cargo', 'departamento',
            'usuario', 'periodoFerias',
            'contrato', 'dependentes',
            'contatos', 'documentos',
            'endereco', 'dadosBancarios',
            'beneficios', 'folhasPagamento',
            'folhasPagamento.lancamentos']); // load(['departamento', 'cargo', 'usuario']);
        // $funcionario = Funcionario::with(['cargo', 'departamento', 'usuario', 'periodoFerias'])
        //                             ->findOrFail($id);

        return view('rh.funcionarios.show', compact('funcionario'));
    }

    public function edit(Funcionario $funcionario, Request $request)
    {
        // $funcionario->load(['departamento', 'cargo', 'dependentes', 'periodoFerias']);
        // Carrega TODOS os relacionamentos necessários

        $currentPage = $request->query('page', 1); // Obtém a página atual da query string, padrão é 1

        $funcionario->load([
            'endereco',
            'contatos',
            'documentos',
            'dadosBancarios',
            'contrato',
            'beneficios',
            'dependentes', // ✅ ESSENCIAL!
            'cargo',
            'departamento',
        ]);

        // dd($funcionario->dependentes); // Verifica se os dependentes estão sendo carregados corretamente

        $departamentos = Departamento::where('ativo', true)->orderBy('nome', 'asc')->get();
        $cargos = Cargo::where('ativo', true)->orderBy('titulo', 'asc')->get();

        return view('rh.funcionarios.edit', compact(
            'funcionario',
            'departamentos',
            'cargos',
            'currentPage'
        ));
    }

    public function update(FuncionarioUpdateRequest $request, Funcionario $funcionario)
    {

        // se não enviar dependentes, envia um array vazio para evitar erros
        if (! $request->has('dependentes')) {
            $request->merge(['dependentes' => []]);

        }

        try {
            // dd($request->validated());
            $funcionario = $this->funcionarioService->atualizarFuncionario($funcionario, $request->validated());

            // --------- Activity Log (já vem configurado) ----------
            activity()
                ->causedBy(Auth::user())
                ->performedOn($funcionario)
                ->withProperties(['attributes' => $request->validated()])
                ->log('Atualizou dados do funcionário');
            // -------------------------------------------------------

            // página de origem (default = 1)
            $page = $request->input('page', 1);

            return redirect()
                ->route('rh.funcionarios.index', ['page' => $page])
                ->with('success', 'Funcionário atualizado com sucesso!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erro ao atualizar funcionário: '.$e->getMessage());
        }
    }

    /**
     * Relatório de funcionários com férias vencendo/vencidas
     */
    public function relatorioFerias(Request $request)
    {
        $feriasVencendo = Funcionario::feriasVencendo(30)->with(['departamento', 'cargo'])->get();
        $feriasVencidas = Funcionario::feriasVencidas()->with(['departamento', 'cargo'])->get();

        return view('rh.funcionarios.relatorio-ferias', compact('feriasVencendo', 'feriasVencidas'));
    }

    public function buscar(Request $request)
    {
        $query = $request->input('q', '');
        // $query = $request->q ?? '';

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $funcionarios = Funcionario::with(['contrato'])->where('status', 'ativo')
            ->where(function ($q) use ($query) {
                $q->where('nome_completo', 'like', "%{$query}%");

            })
            // ->select('id', 'nome', 'matricula', 'cargo', 'salario_base', 'status')
            ->limit(10)
            ->get();

        // dd($funcionarios);

        return response()->json($funcionarios);
    }

    // Adicione este scope ao modelo Funcionario
    public function scopeComDireitoFerias(Builder $query, $diasAntes = 30)
    {
        $hoje = now()->startOfDay();
        $dataAlerta = $hoje->copy()->addDays($diasAntes);

        return $query->where('ativo', true)
            ->where(function ($q) use ($hoje, $dataAlerta) {
                // Funcionários que COMPLETARAM o período aquisitivo
                // (periodo_aquisitivo_fim <= hoje + 30 dias)
                $q->whereNotNull('periodo_aquisitivo_fim')
                    ->where('periodo_aquisitivo_fim', '<=', $dataAlerta->toDateString())
                    ->where('periodo_aquisitivo_fim', '>=', $hoje->copy()->subDays(30)->toDateString());
            })
            ->where('ferias_vencidas', false) // Não está vencida
            ->whereDoesntHave('periodoFerias', function ($q) {
                // Não tem férias agendadas/gozadas para este período
                $q->whereIn('status', ['aprovada', 'gozada'])
                    ->where('data_inicio', '>=', now()->subYear()->toDateString());
            });
    }

    /**
     * Demitir funcionário
     */
    public function formDemitir(Funcionario $funcionario)
    {
        return view('rh.funcionarios.demitir', compact('funcionario'));
    }

    public function demitir(Request $request, Funcionario $funcionario)
    {
        $validated = $request->validate([
            'data_demissao' => 'required|date|after_or_equal:'.$funcionario->contrato->data_admissao,
            'motivo' => 'required|string|max:500',
        ]);

        $dataDemissao = Carbon::parse($validated['data_demissao']);

        // Calcula férias rescisórias
        $feriasRescisorias = $this->periodoService->calcularFeriasRescisorias($funcionario, $dataDemissao);

        // Atualiza o contrato
        $funcionario->contrato->update([
            'data_demissao' => $dataDemissao->toDateString(),
        ]);

        // Inativa o funcionário
        $funcionario->update([
            'ativo' => false,
            'observacoes' => trim(($funcionario->observacoes ?? '').
                "\nDemitido em {$dataDemissao->format('d/m/Y')}. Motivo: {$validated['motivo']}"),
        ]);

        // Cancela férias futuras
        $funcionario->periodoFerias()
            ->whereIn('status', ['planejada', 'aprovada'])
            ->where('data_inicio', '>', $dataDemissao)
            ->update([
                'status' => 'cancelada',
                'observacao' => 'Cancelada devido à demissão em '.$dataDemissao->format('d/m/Y'),
            ]);

        return redirect()
            ->route('rh.funcionarios.show', $funcionario)
            ->with('success', "Funcionário demitido em {$dataDemissao->format('d/m/Y')}. ".
                    "Férias rescisórias: {$feriasRescisorias['total_dias_pagar']} dias a pagar.");
    }
}
