<?php

namespace App\Http\Controllers\RH;

use Illuminate\Http\Request;

use App\Http\Requests\RH\{FuncionarioStoreRequest, FuncionarioUpdateRequest};
use App\Http\Controllers\Controller;
use App\Services\RH\FuncionarioService;
use App\Models\Domain\RH\{Cargo, Departamento, Funcionario};
class FuncionarioController extends Controller
{
    //
     public function __construct(
        private FuncionarioService $funcionarioService
    ) {}

    public function index(Request $request)
    {


        $funcionarios = Funcionario::query()
            ->with(['departamento', 'cargo'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('nome_completo', 'like', "%{$request->search}%")
                      ->orWhere('cpf', 'like', "%{$request->search}%");
            })
            ->when($request->filled('departamento_id'), function ($query) use ($request) {
                $query->where('departamento_id', $request->departamento_id);
            })
            ->when($request->filled('cargo_id'), function ($query) use ($request) {
                $query->where('cargo_id', $request->cargo_id);
            })
            ->when($request->boolean('apenas_ativos'), function ($query) {
                $query->where('ativo', true);
            })
            ->when($request->boolean('ferias_vencendo'), function ($query) {
                $query->feriasVencendo(30);
            })
            ->orderBy('nome_completo')
            ->paginate(20)
            ->withQueryString();

        $departamentos = Departamento::query()->where('ativo', true)->orderBy('nome', 'desc')->get();
        $cargos = Cargo::query()->where('ativo', true)->orderBy('titulo')->get();


        // No controller ou direto na view
        $alertasFerias = [
            'vencendo_30_dias' => Funcionario::feriasVencendo(30)->count('id'),
            'vencidas' => Funcionario::feriasVencidas()->count('id'),
            'total_funcionarios' => Funcionario::ativos()->count('id')
        ];



        return view('rh.funcionarios.index', compact('funcionarios', 'departamentos', 'cargos', 'alertasFerias'));
    }

    public function create()
    {

        $departamentos = Departamento::query()->where('ativo', true)->orderBy('nome')->get();
        $cargos = Cargo::query()->where('ativo', true)->orderBy('titulo')->get();

        return view('rh.funcionarios.create', compact('departamentos', 'cargos'));
    }

    public function store(FuncionarioStoreRequest $request)
    {
        try {
            $funcionario = $this->funcionarioService->criarFuncionario($request->validated());

            return redirect()
                ->route('rh.funcionarios.show', $funcionario)
                ->with('success', 'Funcionário cadastrado com sucesso! Período de férias calculado automaticamente.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erro ao cadastrar funcionário: ' . $e->getMessage());
        }
    }

    public function show(Funcionario $funcionario)
    {
        // $id = $funcionario->id;

        $funcionario->loadMissing(['cargo', 'departamento', 'usuario', 'periodoFerias']); //load(['departamento', 'cargo', 'usuario']);
        // $funcionario = Funcionario::with(['cargo', 'departamento', 'usuario', 'periodoFerias'])
        //                             ->findOrFail($id);

        return view('rh.funcionarios.show', compact('funcionario'));
    }

    public function edit(Funcionario $funcionario)
    {
        // dd($funcionario);

        $departamentos = Departamento::query()->where('ativo', true)->orderBy('nome')->get();
        $cargos = Cargo::query()->where('ativo', true)->orderBy('titulo')->get();

        return view('rh.funcionarios.edit', compact('funcionario', 'departamentos', 'cargos'));
    }

    public function update(FuncionarioUpdateRequest $request, Funcionario $funcionario)
    {
        // dd($request->all());

        try {
            $funcionario = $this->funcionarioService->atualizarFuncionario($funcionario, $request->validated());

            return redirect()
                ->route('rh.funcionarios.show', $funcionario)
                ->with('success', 'Funcionário atualizado com sucesso!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erro ao atualizar funcionário: ' . $e->getMessage());
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
}
