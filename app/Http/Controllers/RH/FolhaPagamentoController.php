<?php

namespace App\Http\Controllers\RH;

use Illuminate\Http\{RedirectResponse, Request, Response};
use Illuminate\Support\{Carbon, Collection, Str};
use Illuminate\View\View;
use Illuminate\Support\Facades\{Auth, Log};
use Illuminate\Validation\ValidationException;

use App\Http\Controllers\Controller;
use App\Http\Requests\RH\FolhaPagamentoRequest;
use App\Models\Domain\RH\{Cargo, FolhaLancamento, FolhaPagamento, Funcionario};
use App\Services\RH\{CalculoTrabalhistaService, FolhaPagamentoService};
use Barryvdh\DomPDF\Facade\Pdf;

class FolhaPagamentoController extends Controller
{
    public function __construct(
        protected FolhaPagamentoService $folhaService,
        protected CalculoTrabalhistaService $calculoFolhaService
    ) {}
    //
       // ─── INDEX ────────────────────────────────────────────────────
    public function indexOld(Request $request): View
    {
        // dd($request->all(),  'Index');
        $competencia = $request->input('competencia', now()->format('Y-m'));
        [$ano, $mes] = explode('-', $competencia);
        $status      = $request->input('status', '');

        // $competencia .= '-01';

        // dd($competencia, $status);

        // Query base
        // $query = FolhaPagamento::with('funcionario')
        //     ->whereRaw("DATE_FORMAT(competencia, '%Y-%m') = ?", [$competencia]);
        $query = FolhaPagamento::with('funcionario')
                                ->whereYear('competencia', $ano)
                                ->whereMonth('competencia', $mes);

        // dd($query->toSql(), $query->getBindings(), $query->get());

        // Filtro status
        if ($status !== '' && $status !== null && $request->filled('status')) {
            // dd('caiu dentro do if', $status);
            $query->where('status', $status);
        }

        // dd($query->get());

        $folhas = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        // dd($folhas);


        // Totais para o rodapé (respeita filtros)
        // $totaisQuery = FolhaPagamento::whereRaw("DATE_FORMAT(competencia, '%Y-%m') = ?", [$competencia]);
        $totaisQuery = FolhaPagamento::whereYear('competencia', $ano)->whereMonth('competencia', $mes);

        if ($status !== '') {
            $totaisQuery->where('status', $status);
        }

        // SUM(COALESCE(dsr_hora_extra, 0))                                AS total_dsr_hora_extra,
        // COALESCE(dsr_hora_extra, 0) +
        // COALESCE(dsr_hora_extra, 0) +
        $totais = $totaisQuery->selectRaw("
            COUNT(*)                                                        AS total_funcionarios,
            MAX(quinto_dia_util)                                            AS quinto_dia_util,

            SUM(salario_base)                                               AS total_salario_base,
            SUM(COALESCE(gratificacao_feriado, 0))                          AS total_gratificacao,
            SUM(COALESCE(salario_familia_hr_extra, 0))                      AS total_sal_familia_hr_extra,
            SUM(COALESCE(arredondamento_provento, 0))                       AS total_arred_provento,

            SUM(COALESCE(desconto_inss, 0))                                 AS total_desconto_inss,
            SUM(COALESCE(vale_dia_20, 0))                                   AS total_vale_dia_20,
            SUM(COALESCE(vale_extra, 0))                                    AS total_vale_extra,
            SUM(COALESCE(faltas_valor, 0))                                  AS total_faltas,
            SUM(COALESCE(dsr_faltas, 0))                                    AS total_dsr_faltas,
            SUM(COALESCE(arredondamento_desconto, 0))                       AS total_arred_desconto,

            SUM(
                salario_base +
                COALESCE(gratificacao_feriado, 0) +
                COALESCE(salario_familia_hr_extra, 0) +
                COALESCE(arredondamento_provento, 0)
            )                                                               AS total_proventos,

            SUM(
                COALESCE(desconto_inss, 0) +
                COALESCE(vale_dia_20, 0) +
                COALESCE(vale_extra, 0) +
                COALESCE(faltas_valor, 0) +
                COALESCE(dsr_faltas, 0) +
                COALESCE(arredondamento_desconto, 0)
            )                                                               AS total_descontos,

            SUM(
                (salario_base +
                COALESCE(gratificacao_feriado, 0) +
                COALESCE(salario_familia_hr_extra, 0) +
                COALESCE(arredondamento_provento, 0))
                -
                (COALESCE(desconto_inss, 0) +
                COALESCE(vale_dia_20, 0) +
                COALESCE(vale_extra, 0) +
                COALESCE(faltas_valor, 0) +
                COALESCE(dsr_faltas, 0) +
                COALESCE(arredondamento_desconto, 0))
            )                                                               AS total_salario_liquido
        ")->first();

        return view('rh.folha-pagamento.index', compact(
            'folhas', 'totais', 'competencia', 'status'
        ));
    }
     public function index(Request $request): View
    {
        $competencia = $request->input('competencia', now()->format('Y-m'));
        $status = $request->input('status', '');

        [$ano, $mes] = explode('-', $competencia);

        // Query base com lançamentos
        $query = FolhaPagamento::with(['funcionario.cargo', 'lancamentos'])
            ->whereYear('competencia', $ano)  // ✅ Seguro
            ->whereMonth('competencia', $mes); // ✅ Seguro

        if ($status !== '') {
            $query->where('status', $status);
        }

        $folhas = $query->orderBy('created_at', 'desc')->paginate(20);

        // Totais - calculados a partir dos lançamentos
        $totais = $this->calcularTotais($ano, $mes, $status);

        return view('rh.folha-pagamento.index', compact(
            'folhas', 'totais', 'competencia', 'status'
        ));
    }

    /**
     * Calcula os totais do rodapé baseado nos lançamentos
     */
    private function calcularTotais(int $ano, int $mes, string $status): object
    {
        // Busca todas as folhas do período
        $query = FolhaPagamento::whereYear('competencia', $ano)
            ->whereMonth('competencia', $mes);

        if ($status !== '') {
            $query->where('status', $status);
        }

        $folhasIds = $query->pluck('id');

        // Se não tem folhas, retorna zeros
        if ($folhasIds->isEmpty()) {
            return (object) [
                'total_funcionarios' => 0,
                'quinto_dia_util' => null,
                'total_salario_base' => 0,
                'total_gratificacao' => 0,
                'total_sal_familia_hr_extra' => 0,
                'total_arred_provento' => 0,
                'total_desconto_inss' => 0,
                'total_vale_dia_20' => 0,
                'total_vale_extra' => 0,
                'total_faltas' => 0,
                'total_dsr_faltas' => 0,
                'total_arred_desconto' => 0,
                'total_horas_extras' => 0,
                'total_proventos' => 0,
                'total_descontos' => 0,
                'total_salario_liquido' => 0,
            ];
        }

        // Busca os lançamentos de todas as folhas do período
        $lancamentos = FolhaLancamento::whereIn('folha_pagamento_id', $folhasIds)->get();

    // Função helper para somar por tipo
    $somar = function(string $tipo, string $categoria = null) use ($lancamentos) {
        $query = $lancamentos->where('tipo', $tipo);
        if ($categoria) {
            $query = $query->where('categoria', $categoria);
        }
        return $query->sum('valor_total');
    };

    // Totais por tipo de lançamento
    $totalSalarioBase = $somar('salario_base', 'provento');
    $totalGratificacao = $somar('gratificacao', 'provento');
    $totalSalFamilia = $somar('salario_familia', 'provento');
    $totalHoraExtraNormal = $somar('hora_extra_normal', 'provento');
    $totalHoraExtraSabado = $somar('hora_extra_sabado', 'provento');
    $totalHoraExtraFeriado = $somar('hora_extra_feriado', 'provento');
    $totalHorasExtras = $totalHoraExtraNormal + $totalHoraExtraSabado + $totalHoraExtraFeriado;
    $totalDsrHoraExtra = $somar('dsr_hora_extra', 'provento');
    $totalArredProvento = $somar('arredondamento', 'provento');

    $totalInss = $somar('inss', 'desconto');
    $totalValeDia20 = $somar('vale_dia_20', 'desconto');
    $totalValeExtra = $somar('vale_extra', 'desconto');
    $totalFaltas = $somar('falta', 'desconto');
    $totalDsrFaltas = $somar('dsr_falta', 'desconto');
    $totalArredDesconto = $somar('arredondamento', 'desconto');

    // Totais consolidados
    $totalProventos = $totalSalarioBase + $totalHorasExtras + $totalDsrHoraExtra +
                      $totalGratificacao + $totalSalFamilia + $totalArredProvento;
    $totalDescontos = $totalInss + $totalValeDia20 + $totalValeExtra +
                      $totalFaltas + $totalDsrFaltas + $totalArredDesconto;
    $totalLiquido = $totalProventos - $totalDescontos;

    // Quinto dia útil (pega do primeiro registro)
    $quintoDiaUtil = FolhaPagamento::whereIn('id', $folhasIds)
        ->max('quinto_dia_util');

    return (object) [
        'total_funcionarios' => $folhasIds->count(),
        'quinto_dia_util' => $quintoDiaUtil,
        'total_salario_base' => $totalSalarioBase,
        'total_gratificacao' => $totalGratificacao,
        'total_sal_familia_hr_extra' => $totalSalFamilia,
        'total_arred_provento' => $totalArredProvento,
        'total_desconto_inss' => $totalInss,
        'total_vale_dia_20' => $totalValeDia20,
        'total_vale_extra' => $totalValeExtra,
        'total_faltas' => $totalFaltas,
        'total_dsr_faltas' => $totalDsrFaltas,
        'total_arred_desconto' => $totalArredDesconto,
        'total_horas_extras' => $totalHorasExtras,
        'total_dsr_hora_extra' => $totalDsrHoraExtra,
        'total_proventos' => $totalProventos,
        'total_descontos' => $totalDescontos,
        'total_salario_liquido' => $totalLiquido,
    ];
}
    public function index3(Request $request): View
    {
        // O input month vem como '2026-05'
        $competencia = $request->input('competencia', now()->format('Y-m'));

        // Converte para o formato do banco (sempre dia 1)
        $competencia .=  '-01'; // '2026-05-01'

        $status = $request->input('status', '');

        // Query base - agora mais limpa e usando o campo diretamente
        $query = FolhaPagamento::with('funcionario')
            ->where('competencia', $competencia); // ✅ Comparação direta, sem whereRaw

        // Filtro status
        if ($status !== '' && $status !== null && $request->filled('status')) {
            $query->where('status', $status);
        }

        $folhas = $query->orderBy('created_at', 'desc')->paginate(20);

        // Totais para o rodapé (respeita filtros)
        $totaisQuery = FolhaPagamento::query()->where('competencia', $competencia); // ✅ Direto

        if ($status !== '' && $status !== null && $request->filled('status')) {
            $totaisQuery->where('status', $status);
        }

        $totais = $totaisQuery->selectRaw("
            COUNT(*)                                                        AS total_funcionarios,
            MAX(quinto_dia_util)                                            AS quinto_dia_util,

            SUM(salario_base)                                               AS total_salario_base,
            SUM(COALESCE(gratificacao_feriado, 0))                          AS total_gratificacao,
            SUM(COALESCE(salario_familia_hr_extra, 0))                      AS total_sal_familia_hr_extra,
            SUM(COALESCE(arredondamento_provento, 0))                       AS total_arred_provento,

            SUM(COALESCE(desconto_inss, 0))                                 AS total_desconto_inss,
            SUM(COALESCE(vale_dia_20, 0))                                   AS total_vale_dia_20,
            SUM(COALESCE(vale_extra, 0))                                    AS total_vale_extra,
            SUM(COALESCE(faltas_valor, 0))                                  AS total_faltas,
            SUM(COALESCE(dsr_faltas, 0))                                    AS total_dsr_faltas,
            SUM(COALESCE(arredondamento_desconto, 0))                       AS total_arred_desconto,

            SUM(
                salario_base +
                COALESCE(gratificacao_feriado, 0) +
                COALESCE(salario_familia_hr_extra, 0) +
                COALESCE(arredondamento_provento, 0)
            )                                                               AS total_proventos,

            SUM(
                COALESCE(desconto_inss, 0) +
                COALESCE(vale_dia_20, 0) +
                COALESCE(vale_extra, 0) +
                COALESCE(faltas_valor, 0) +
                COALESCE(dsr_faltas, 0) +
                COALESCE(arredondamento_desconto, 0)
            )                                                               AS total_descontos,

            SUM(
                (salario_base +
                COALESCE(gratificacao_feriado, 0) +
                COALESCE(salario_familia_hr_extra, 0) +
                COALESCE(arredondamento_provento, 0))
                -
                (COALESCE(desconto_inss, 0) +
                COALESCE(vale_dia_20, 0) +
                COALESCE(vale_extra, 0) +
                COALESCE(faltas_valor, 0) +
                COALESCE(dsr_faltas, 0) +
                COALESCE(arredondamento_desconto, 0))
            )                                                               AS total_salario_liquido
        ")->first();

        // Passa o input original para a view (formato Y-m)
        $competencia = Carbon::parse($competencia)->format('Y-m');
        return view('rh.folha-pagamento.index', compact(
            'folhas', 'totais', 'competencia', 'status'
        ));
    }
    public function index2(Request $request): View
    {
        $competencia = $request->input('competencia', now()->format('Y-m'));
        $status = $request->input('status', '');

        [$ano, $mes] = explode('-', $competencia);

        // Query base com lançamentos
        $query = FolhaPagamento::with(['funcionario.cargo', 'lancamentos'])
            ->whereYear('competencia', $ano)
            ->whereMonth('competencia', $mes);

        if ($status !== '') {
            $query->where('status', $status);
        }

        $folhas = $query->orderBy('created_at', 'desc')->paginate(20);

        // Totais para o rodapé
        $totaisQuery = FolhaPagamento::whereYear('competencia', $ano)
            ->whereMonth('competencia', $mes);

        if ($status !== '') {
            $totaisQuery->where('status', $status);
        }

        $totais = $totaisQuery->selectRaw("
            COUNT(*) as total_funcionarios,
            MAX(quinto_dia_util) as quinto_dia_util,
            SUM(salario_base) as total_salario_base,
            SUM(COALESCE(gratificacao_feriado, 0)) as total_gratificacao,
            SUM(COALESCE(salario_familia_hr_extra, 0)) as total_sal_familia_hr_extra,
            SUM(COALESCE(arredondamento_provento, 0)) as total_arred_provento,
            SUM(COALESCE(desconto_inss, 0)) as total_desconto_inss,
            SUM(COALESCE(vale_dia_20, 0)) as total_vale_dia_20,
            SUM(COALESCE(vale_extra, 0)) as total_vale_extra,
            SUM(COALESCE(faltas_valor, 0)) as total_faltas,
            SUM(COALESCE(dsr_faltas, 0)) as total_dsr_faltas,
            SUM(COALESCE(arredondamento_desconto, 0)) as total_arred_desconto,
            SUM(COALESCE(dsr_hora_extra, 0)) as total_dsr_hora_extra,
            SUM(COALESCE(horas_extras_totais * valor_hora_extra, 0)) as total_horas_extras
        ")->first();

        return view('rh.folha-pagamento.index', compact(
            'folhas', 'totais', 'competencia', 'status'
        ));
    }

    // ─── CREATE ───────────────────────────────────────────────────
    public function create(): View
    {
        $funcionarios = Funcionario::orderBy('nome_completo', 'asc')->get(['id', 'nome_completo']);

        return view('rh.folha-pagamento.create', compact('funcionarios'));
    }
    public function createNew()
    {
        $funcionarios = Funcionario::with(['departamento', 'cargo'])->orderBy('nome_completo', 'asc')->get();
        return view('rh.folha-pagamento.create-new', compact('funcionarios'));
    }

    // ─── STORE ────────────────────────────────────────────────────
    public function store(FolhaPagamentoRequest $request): RedirectResponse
    {
        dd('Stored antigo');
        $folha = FolhaPagamento::create($request->validated());

        activity('folha_pagamento')
            ->performedOn($folha)
            ->causedBy(Auth::user())
            ->withProperties(['competencia' => $folha->competencia, 'funcionario_id' => $folha->funcionario_id])
            ->log('Folha de pagamento criada');

        return redirect()
            ->route('rh.folha-pagamento.index', ['competencia' => now()->format('Y-m')])
            ->with('success', 'Folha de pagamento criada com sucesso!');
    }
    // public function storeNew(Request $request)
    // {
    //     $validated = $request->validate([
    //         'funcionario_id' => 'required|exists:funcionarios,id',
    //         'competencia' => 'required|date',
    //         'salario_base' => 'required|numeric|min:0',
    //         'horas_extras_totais' => 'nullable|numeric|min:0',
    //         'valor_hora_extra' => 'nullable|numeric|min:0',
    //         'gratificacao_feriado' => 'nullable|numeric|min:0',
    //         // ... outros campos
    //     ]);

    //     // O DSR é calculado automaticamente pelo Accessor
    //     FolhaPagamento::create($validated);

    //     return redirect()->route('rh.folha-pagamento.index')
    //         ->with('success', 'Folha criada com sucesso!');
    // }
    public function storeNew(Request $request)
    {
        // dd('stored novo');
        // Validação mínima - usuário só informa o essencial
        // $validated = $request->validate([
        //     'funcionario_id' => 'required|exists:funcionarios,id',
        //     'competencia' => 'required|date_format:Y-m',
        //     'horas_extras_totais' => 'nullable|numeric|min:0',
        //     'faltas_dias' => 'nullable|numeric|min:0',
        //     'eh_diarista' => 'nullable|boolean',
        //     'valor_diaria' => 'nullable|numeric|min:0',
        //     'gratificacao_feriado' => 'nullable|numeric|min:0',
        // ]);
        $validated = $request->validate([
            'funcionario_id' => 'required|exists:funcionarios,id',
            'competencia' => 'required|date_format:Y-m',
            'horas_extras_totais' => 'nullable|numeric|min:0',
            'horas_sabado' => 'nullable|numeric|min:0',
            'horas_feriado' => 'nullable|numeric|min:0',
            'faltas_dias' => 'nullable|numeric|min:0',
            'vale_dia_20' => 'nullable|numeric|min:0',
            'vale_extra' => 'nullable|numeric|min:0',
            'gratificacao_feriado' => 'nullable|numeric|min:0',
        ]);



        try {

            $funcionario = Funcionario::findOrFail($validated['funcionario_id']);
            $competencia = Carbon::createFromFormat('Y-m', $validated['competencia'])->startOfMonth();


            // ✅ Verifica se já existe folha para este funcionário nesta competência
            $existente = FolhaPagamento::query()->where('funcionario_id', $funcionario->id)
                ->where('competencia', $competencia->format('Y-m-d'))
                ->exists();

            if ($existente) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', "Já existe uma folha de pagamento para {$funcionario->nome_completo} na competência {$competencia->format('m/Y')}!");
            }

            $folha = $this->folhaService->criar($funcionario, $competencia, $validated);

            return redirect()
                ->route('rh.folha-pagamento.show', $folha->id)
                ->with('success', 'Folha de pagamento criada com sucesso!');

            // return redirect()->route('rh.folha-pagamento.show', $folha->id)
            // ->with('success', 'Folha de pagamento criada com sucesso!');

        }
        catch (ValidationException $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($e->validator);
        }

    }

    // ─── SHOW ─────────────────────────────────────────────────────
    public function show(FolhaPagamento $folha): View
    {
        dd($folha);

        $folhaPagamento = $folha->load([
                    'funcionario',


                    ]);
        // dd($folha);

            // 'activities.causer', // <- Carregar o log com quem fez a ação.

        return view('rh.folha-pagamento.show', compact('folhaPagamento'));
    }

    public function showNew(string $id)
    {

        $folhaPagamento = FolhaPagamento::with('funcionario')->findOrFail($id);

        // Dados calculados automaticamente pelos Accessors
        return view('rh.folha-pagamento.show-new', compact('folhaPagamento'));
    }

    // ─── EDIT ─────────────────────────────────────────────────────
    public function edit(FolhaPagamento $folha): View
    {
        $funcionarios = Funcionario::orderBy('nome_completo', 'asc')->get(['id', 'nome_completo']);

        $folhaPagamento = $folha->load('funcionario');

        return view('rh.folha-pagamento.edit-new', compact('folhaPagamento', 'funcionarios'));
    }

    // ─── UPDATE ───────────────────────────────────────────────────
    public function updateOld(FolhaPagamentoRequest $request, FolhaPagamento $folhaPagamento): RedirectResponse
    {
        $antes = $folhaPagamento->only([
            'salario_base', 'desconto_inss', 'status'
        ]);

        $folhaPagamento->update($request->validated());

        activity('folha_pagamento')
            ->performedOn($folhaPagamento)
            ->causedBy(Auth::user())
            ->withProperties(['antes' => $antes, 'depois' => $request->validated()])
            ->log('Folha de pagamento atualizada');

        return redirect()
            ->route('rh.folha-pagamento.index', ['competencia' => now()->format('Y-m')])
            ->with('success', 'Folha de pagamento atualizada com sucesso!');
    }
    public function update(Request $request, FolhaPagamento $folha)
    {
        $validated = $request->validate([
            'horas_extras_totais' => 'nullable|numeric|min:0',
            'horas_sabado' => 'nullable|numeric|min:0',
            'horas_feriado' => 'nullable|numeric|min:0',
            'faltas_dias' => 'nullable|numeric|min:0',
            'vale_dia_20' => 'nullable|numeric|min:0',
            'vale_extra' => 'nullable|numeric|min:0',
            'gratificacao_feriado' => 'nullable|numeric|min:0',
            'status' => 'required|in:aberta,fechada',
            'observacao' => 'nullable|string|max:500',
        ]);

        $antes = $folha->only([
            'salario_base',
            'desconto_inss',
            'status',
            'observacao',
            'horas_extras_totais',
            'horas_sabado',
            'horas_feriado',
            'faltas_dias',
            'vale_dia_20',
            'vale_extra',
            'gratificacao_feriado',
        ]);

        // dd($folha, $validated);

        $folha = $this->folhaService->atualizar($folha, $validated);

        activity('folha_pagamento')
            ->performedOn($folha)
            ->causedBy(Auth::user())
            ->withProperties(['antes' => $antes, 'depois' => $validated])
            ->log('Folha de pagamento atualizada');

        return redirect()
            ->route('rh.folha-pagamento.index', ['competencia' => now()->format('Y-m')])
            ->with('success', 'Folha de pagamento atualizada com sucesso!');


    }

    /**
     * API: Calendário do mês
     */
    public function calendario(Request $request)
    {
        $competencia = Carbon::createFromFormat('Y-m', $request->input('competencia'))->startOfMonth();

        return response()->json(
            $this->calculoFolhaService->getResumoCalendario($competencia)
        );
    }
    // public function update(Request $request, FolhaPagamento $folha)
    // {
    //     $validated = $request->validate([
    //         'vale_dia_20' => 'nullable|numeric|min:0',
    //         'vale_extra' => 'nullable|numeric|min:0',
    //         'faltas_dias' => 'nullable|numeric|min:0',
    //         'horas_extras_totais' => 'nullable|numeric|min:0',
    //         'horas_sabado' => 'nullable|numeric|min:0',
    //         'horas_feriado' => 'nullable|numeric|min:0',
    //         'gratificacao_feriado' => 'nullable|numeric|min:0',
    //         'status' => 'required|in:aberta,fechada',
    //         'observacao' => 'nullable|string|max:500',
    //     ]);

    //     $antes = $folha->only([
    //         'salario_base',
    //         'desconto_inss',
    //         'status',
    //         'vale_dia_20',
    //         'vale_extra',
    //         'faltas_dias',
    //         'horas_extras_totais',
    //         'horas_sabado',
    //         'horas_feriado',
    //         'gratificacao_feriado',
    //         'observacao',
    //     ]);

    //     // Recalcula tudo baseado nos novos valores
    //     $funcionario = $folha->funcionario;
    //     $competencia = Carbon::parse($folha->competencia);

    //     // ... mesmos cálculos do store ...
    //      // ─── CÁLCULOS AUTOMÁTICOS ─────────────────────

    //     // 1. Salário Base
    //     $salarioBase = $funcionario->salario_base ?? 0;

    //     // 2. Valor Hora Normal e Hora Extra
    //     $cargaHoraria = $funcionario->carga_horaria_semanal ?? 44;
    //     $horasMensais = ($cargaHoraria / 6) * 30; // Aproximação 220h para 44h semanais
    //     $valorHoraNormal = $horasMensais > 0 ? $salarioBase / $horasMensais : 0;
    //     $valorHoraExtra = $valorHoraNormal * 1.5; // 50% de acréscimo

    //     // 3. Horas Extras
    //     $horasExtrasTotais = $validated['horas_extras_totais'] ?? 0;
    //     $totalHorasExtras = $horasExtrasTotais * $valorHoraExtra;

    //     // 4. DSR Hora Extra (cálculo automático)
    //     $diasUteis = $this->calcularDiasUteis($competencia);
    //     $domingosFeriados = $this->calcularDomingosEFeriados($competencia);
    //     $dsrHoraExtra = 0;
    //     if ($horasExtrasTotais > 0 && $diasUteis > 0) {
    //         $mediaHoraExtraDia = $horasExtrasTotais / $diasUteis;
    //         $dsrHoraExtra = round($mediaHoraExtraDia * $domingosFeriados * $valorHoraExtra, 2);
    //     }

    //     // 5. Gratificação (opcional, informada pelo usuário)
    //     $gratificacaoFeriado = $validated['gratificacao_feriado'] ?? 0;

    //     // 6. Salário Família (automático baseado em dependentes)
    //     $salarioFamilia = $this->calcularSalarioFamilia($funcionario, $salarioBase);

    //     // 7. Faltas (dias não trabalhados)
    //     $faltasDias = $validated['faltas_dias'] ?? 0;
    //     $faltasValor = 0;
    //     $dsrFaltas = 0;
    //     if ($faltasDias > 0) {
    //         $valorDiaTrabalho = $diasUteis > 0 ? $salarioBase / $diasUteis : 0;
    //         $faltasValor = round($faltasDias * $valorDiaTrabalho, 2);
    //         // DSR sobre faltas (proporcional)
    //         $dsrFaltas = round($faltasValor * ($domingosFeriados / $diasUteis), 2);
    //     }

    //     // 8. Diarista
    //     $ehDiarista = $validated['eh_diarista'] ?? false;
    //     $valorDiaria = $validated['valor_diaria'] ?? 0;
    //     if ($ehDiarista && $valorDiaria > 0) {
    //         $diasTrabalhados = $diasUteis - $faltasDias;
    //         $salarioBase = $diasTrabalhados * $valorDiaria;
    //     }

    //     // 9. INSS (automático)
    //     $inss = $this->calcularInss($salarioBase);

    //     // 10. Quinto dia útil
    //     $quintoDiaUtil = $this->calcularQuintoDiaUtil($competencia);

    //     // 11. Arredondamentos (para fechar centavos)
    //     $totalProventos = $salarioBase + $totalHorasExtras + $dsrHoraExtra + $gratificacaoFeriado + $salarioFamilia;
    //     $totalDescontos = $inss + $faltasValor + $dsrFaltas;
    //     $salarioLiquido = $totalProventos - $totalDescontos;
    //     $arredondamentoProvento = round($salarioLiquido - floor($salarioLiquido * 100) / 100, 2);
    //     $arredondamentoDesconto = $arredondamentoProvento > 0.005 ? $arredondamentoProvento : 0;
    //     $arredondamentoProvento = $arredondamentoProvento > 0.005 ? 0 : abs($arredondamentoProvento);

    //     // 12. Salário Liquido
    //     $salarioLiquido = $totalProventos - $totalDescontos + $arredondamentoProvento - $arredondamentoDesconto;




    //     // ─── UPDATE ─────────────────────────────────────────────────

    //     $folha->update($request->validated());

    //     activity('folha_pagamento')
    //         ->performedOn($folha)
    //         ->causedBy(Auth::user())
    //         ->withProperties(['antes' => $antes, 'depois' => $request->validated()])
    //         ->log('Folha de pagamento atualizada');

    //     return redirect()->route('rh.folha-pagamento.show', $folha->id)
    //         ->with('success', 'Folha atualizada com sucesso!');
    // }

    // ─── DESTROY ─────────────────────────────────────────────────
    public function destroy(FolhaPagamento $folha): RedirectResponse
    {
        // Proteção: não permite excluir folha fechada
        if ($folha->status === 'fechada') {
            return back()->with('error', 'Não é possível excluir uma folha com status Fechada.');
        }

        // Proteção: não permite excluir folha paga
        if ($folha->status === 'pago') {
            return back()->with('error', 'Não é possível excluir uma folha com status Pago.');
        }

        activity('folha_pagamento')
            ->performedOn($folha)
            ->causedBy(Auth::user())
            ->withProperties(['competencia' => $folha->competencia])
            ->log('Folha de pagamento excluída');

        $folha->delete();

        return back()->with('success', 'Folha excluída com sucesso!');
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
    public function showOld(Funcionario $funcionario)
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
    public function createOld(): View
    {
        return view('rh.folha-pagamento.create');
    }

    /**
     * Cria nova folha para uma competência
     */
    public function storeOld(Request $request): RedirectResponse
    {
        $request->validate([
            'competencia' => ['required', 'date', 'date_format:Y-m-d'],
        ]);

        try {
            $folha = $this->folhaService->criarOuBuscarFolha($request->competencia);

            return redirect()
                ->route('rh.folha-pagamento.show', $folha)
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
        // dd($request->all());

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
    public function resumoGeral(Request $request)
    {
        // dd($request->all());
        // 'funcionario.localTrabalho', // ajuste conforme seu relacionamento

        $query = FolhaPagamento::with([
            'funcionario.cargo',
            'funcionario.departamento',
            'lancamentos'
            ])
        ->orderBy('competencia', 'desc')
        ->orderBy('created_at', 'desc');

        // dd($query->toSql(), $query->getBindings(), $query->get());

        // Filtro por competência (mês/ano)
        if ($request->filled('competencia')) {
            $query->where('competencia', $request->competencia . '-01');
        }

        // Filtro por local de trabalho
        if ($request->filled('local_trabalho')) {
            // dd('caiu aqu...');
            $query->whereHas('funcionario', function ($q) use ($request) {
                $q->where('local_trabalho', $request->local_trabalho);
            });
        }

        // Filtro por departamento/função
        if ($request->filled('cargo_id')) {
            $query->whereHas('funcionario', function ($q) use ($request) {
                $q->where('cargo_id', $request->cargo_id);
            });
        }

        $folhas = $query->paginate(50)->withQueryString();

        // Totalizadores do rodapé
        // ─── Totais para o rodapé ─────────────────────────────────
        // SUM(COALESCE(dsr_hora_extra, 0))                                    AS total_dsr_hora_extra,
        // COALESCE(dsr_hora_extra, 0) +
        // COALESCE(dsr_hora_extra, 0) +
        $totais = FolhaPagamento::whereRaw("DATE_FORMAT(competencia, '%Y-%m') = ?", [$request->competencia])
            ->selectRaw("
                COUNT(*)                                                            AS total_funcionarios,
                MAX(quinto_dia_util)                                                AS quinto_dia_util,

                SUM(salario_base)                                                   AS total_salario_base,
                SUM(COALESCE(gratificacao_feriado, 0))                              AS total_gratificacao,
                SUM(COALESCE(salario_familia_hr_extra, 0))                          AS total_sal_familia_hr_extra,
                SUM(COALESCE(arredondamento_provento, 0))                           AS total_arred_provento,

                SUM(COALESCE(desconto_inss, 0))                                     AS total_desconto_inss,
                SUM(COALESCE(vale_dia_20, 0))                                       AS total_vale_dia_20,
                SUM(COALESCE(vale_extra, 0))                                        AS total_vale_extra,
                SUM(COALESCE(faltas_valor, 0))                                      AS total_faltas,
                SUM(COALESCE(dsr_faltas, 0))                                        AS total_dsr_faltas,
                SUM(COALESCE(arredondamento_desconto, 0))                           AS total_arred_desconto,

                SUM(
                    salario_base +
                    COALESCE(gratificacao_feriado, 0) +
                    COALESCE(salario_familia_hr_extra, 0) +
                    COALESCE(arredondamento_provento, 0)
                )                                                                   AS total_proventos,

                SUM(
                    COALESCE(desconto_inss, 0) +
                    COALESCE(vale_dia_20, 0) +
                    COALESCE(vale_extra, 0) +
                    COALESCE(faltas_valor, 0) +
                    COALESCE(dsr_faltas, 0) +
                    COALESCE(arredondamento_desconto, 0)
                )                                                                   AS total_descontos,

                SUM(
                    (salario_base +
                    COALESCE(gratificacao_feriado, 0) +
                    COALESCE(salario_familia_hr_extra, 0) +
                    COALESCE(arredondamento_provento, 0))
                    -
                    (COALESCE(desconto_inss, 0) +
                    COALESCE(vale_dia_20, 0) +
                    COALESCE(vale_extra, 0) +
                    COALESCE(faltas_valor, 0) +
                    COALESCE(dsr_faltas, 0) +
                    COALESCE(arredondamento_desconto, 0))
                )                                                                   AS total_salario_liquido
            ")
            ->first();

        // dd();

        // Listas para os filtros
        $competencias = FolhaPagamento::selectRaw('DISTINCT competencia')
            ->orderByDesc('competencia')
            ->pluck('competencia');

        $cargos = Cargo::orderBy('titulo', 'asc')->get();

        $locaisTrabalho = Funcionario::selectRaw('DISTINCT local_trabalho')
            ->whereNotNull('local_trabalho')
            ->orderBy('local_trabalho')
            ->pluck('local_trabalho');

        activity()
            ->causedBy(Auth::user())
            ->log('Acessou o resumo geral da folha de pagamento');

        return view('rh.folha-pagamento.resumo-geral', compact(
            'folhas',
            'totais',
            'competencias',
            'cargos',
            'locaisTrabalho'
        ));
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
        return $pdf->stream("folha_diaristas_$periodo.pdf");
    }

    // ─── PDF INDIVIDUAL (uma folha) ───────────────────────────────────
    public function pdf(FolhaPagamento $folhaPagamento): Response
    {
        $folhaPagamento->load('funcionario');

        $pdf = Pdf::loadView('rh.folha-pagamento.pdf.individual', compact('folhaPagamento'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont'    => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled'   => false,
                'dpi'            => 150,
            ]);

        $nomeArquivo = sprintf(
            'folha-%s-%s.pdf',
            Str::slug($folhaPagamento->funcionario->nome ?? 'funcionario'),
            Carbon::parse($folhaPagamento->competencia)->format('m-Y')
        );

        return $pdf->stream($nomeArquivo);
    }

    // ─── PDF GERAL (todos da competência) ────────────────────────────
    public function pdfGeral(Request $request): Response
    {
        $competencia = $request->input('competencia', now()->format('Y-m'));
        $status      = $request->input('status', '');

        $query = FolhaPagamento::with('funcionario')
            ->whereRaw("DATE_FORMAT(competencia, '%Y-%m') = ?", [$competencia]);

        if ($status !== '') {
            $query->where('status', $status);
        }

        $folhas = $query->orderBy('created_at')->get();

        // Totais
        $totais = (object) [
            'total_funcionarios'       => $folhas->count(),
            'total_proventos'          => $folhas->sum('total_proventos'),
            'total_descontos'          => $folhas->sum('total_descontos'),
            'total_salario_liquido'    => $folhas->sum('salario_liquido'),
            'total_salario_base'       => $folhas->sum('salario_base'),
            'total_desconto_inss'      => $folhas->sum('desconto_inss'),
        ];

        $pdf = Pdf::loadView('rh.folha-pagamento.pdf.geral', compact('folhas', 'totais', 'competencia'))
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'defaultFont'          => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled'         => false,
                'dpi'                  => 150,
            ]);

        $nomeArquivo = sprintf('folha-geral-%s.pdf', str_replace('-', '', $competencia));

        return $pdf->stream($nomeArquivo);
    }


    public function buscar(Request $request)
    {
        $query = $request->input('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        try {
            $funcionarios = Funcionario::with(['cargo', 'contrato', 'dependentes', 'documentos'])->where('ativo', true) // ✅ 'ativo' é boolean
                ->where(function($q) use ($query) {
                    $q->where('nome_completo', 'like', "%{$query}%"); // ✅ nome_completo
                    // ->orWhere('cpf', 'like', "%{$query}%");
                })
                // ->select([
                //     'id',
                //     'nome_completo',  // ✅
                //     'cpf',
                //     'salario_base',
                //     'cargo_id',       // ✅ É uma FK, não o nome do cargo
                //     'ativo',
                //     'local_trabalho',
                //     'tipo_contratacao'
                // ])
                ->limit(10)
                ->get();

            // dd($funcionarios);

            // Formata a resposta para incluir o nome do cargo
            $resultado = $funcionarios->map(function($funcionario) {
                return [
                    'id' => $funcionario->id,
                    'nome_completo' => $funcionario->nome_completo,
                    'cpf' => $funcionario->documentos->cpf,
                    'salario_base' => $funcionario->contrato->salario_base,
                    'cargo' => $funcionario->cargo->titulo ?? 'Sem cargo', // ✅
                    'ativo' => $funcionario->ativo,
                    'local_trabalho' => $funcionario->contrato->local_trabalho,
                    'tipo_contratacao' => $funcionario->contrato->tipo_contratacao
                ];
            });

            return response()->json($resultado);

        } catch (\Exception $e) {
            Log::error('Erro na busca de funcionários: ' . $e->getMessage());
            return response()->json([
                'error' => 'Erro ao buscar funcionários: ' . $e->getMessage()
            ], 500);
        }
    }

    // ─── MÉTODOS AUXILIARES ────────────────────────

    private function calcularDiasUteis(Carbon $data): int
    {
        $uteis = 0;
        $inicio = $data->copy()->startOfMonth();
        $fim = $data->copy()->endOfMonth();

        while ($inicio->lte($fim)) {
            if (!$inicio->isWeekend()) {
                $uteis++;
            }
            $inicio->addDay();
        }

        return $uteis;
    }

    private function calcularDomingosEFeriados(Carbon $data): int
    {
        $dsrs = 0;
        $inicio = $data->copy()->startOfMonth();
        $fim = $data->copy()->endOfMonth();

        while ($inicio->lte($fim)) {
            if ($inicio->isSunday() || $this->isFeriadoNacional($inicio)) {
                $dsrs++;
            }
            $inicio->addDay();
        }

        return $dsrs;
    }

    private function isFeriadoNacional(Carbon $data): bool
    {
        $feriadosFixos = [
            '01-01', // Confraternização Universal
            '21-04', // Tiradentes
            '01-05', // Dia do Trabalho
            '07-09', // Independência
            '12-10', // Nossa Senhora Aparecida
            '02-11', // Finados
            '15-11', // Proclamação da República
            '25-12', // Natal
        ];

        $dataFormat = $data->format('d-m');

        // Feriados fixos
        if (in_array($dataFormat, $feriadosFixos)) {
            return true;
        }

        // Feriados móveis (Carnaval, Sexta-feira Santa, Corpus Christi)
        $ano = $data->year;
        $pascoa = $this->calcularPascoa($ano);

        $feriadosMoveis = [
            $pascoa->copy()->subDays(48)->format('d-m'), // Segunda Carnaval
            $pascoa->copy()->subDays(47)->format('d-m'), // Terça Carnaval
            $pascoa->copy()->subDays(2)->format('d-m'),  // Sexta-feira Santa
            $pascoa->copy()->addDays(60)->format('d-m'), // Corpus Christi
        ];

        return in_array($dataFormat, $feriadosMoveis);
    }

    private function calcularPascoa(int $ano): Carbon
    {
        $a = $ano % 19;
        $b = intdiv($ano, 100);
        $c = $ano % 100;
        $d = intdiv($b, 4);
        $e = $b % 4;
        $f = intdiv(($b + 8), 25);
        $g = intdiv(($b - $f + 1), 3);
        $h = (19 * $a + $b - $d - $g + 15) % 30;
        $i = intdiv($c, 4);
        $k = $c % 4;
        $l = (32 + 2 * $e + 2 * $i - $h - $k) % 7;
        $m = intdiv(($a + 11 * $h + 22 * $l), 451);
        $mes = intdiv(($h + $l - 7 * $m + 114), 31);
        $dia = (($h + $l - 7 * $m + 114) % 31) + 1;

        return Carbon::create($ano, $mes, $dia);
    }

    private function calcularInss(float $salario): float
    {
        // Tabela INSS 2024
        $faixas = [
            ['limite' => 1412.00, 'aliquota' => 0.075],
            ['limite' => 2666.68, 'aliquota' => 0.09],
            ['limite' => 4000.03, 'aliquota' => 0.12],
            ['limite' => 7786.02, 'aliquota' => 0.14],
        ];

        $inss = 0;
        $salarioRestante = $salario;

        foreach ($faixas as $faixa) {
            $valorFaixa = min($salarioRestante, $faixa['limite']);
            $inss += $valorFaixa * $faixa['aliquota'];
            $salarioRestante -= $valorFaixa;
            if ($salarioRestante <= 0) break;
        }

        return round($inss, 2);
    }

    private function calcularSalarioFamilia(Funcionario $funcionario, float $salarioBase): float
    {
        $dependentes = $funcionario->qtd_dependentes_salario_familia ?? 0;

        if ($dependentes === 0 || $salarioBase > 1819.26) {
            return 0;
        }

        // Valor por dependente 2024
        $valorPorDependente = 62.04;

        return round($dependentes * $valorPorDependente, 2);
    }

    private function calcularQuintoDiaUtil(Carbon $data): Carbon
    {
        $dia = $data->copy()->startOfMonth();
        $uteis = 0;

        while ($uteis < 5) {
            if (!$dia->isWeekend() && !$this->isFeriadoNacional($dia)) {
                $uteis++;
            }
            if ($uteis < 5) {
                $dia->addDay();
            }
        }

        // return $dia->format('Y-m-d');
        return $dia;
    }

    // public function calendario(Request $request)
    // {
    //     $competencia = Carbon::createFromFormat('Y-m', $request->input('competencia'))->startOfMonth();

    //     return response()->json([
    //         'dias_uteis' => $this->calcularDiasUteis($competencia),
    //         'domingos_feriados' => $this->calcularDomingosEFeriados($competencia),
    //         'quinto_dia_util' => $this->calcularQuintoDiaUtil($competencia)->format('d/m/Y'),
    //     ]);
    // }

    public function verificarFolhaExistente(Request $request)
    {
        $funcionarioId = $request->input('funcionario_id');
        $competencia = $request->input('competencia');

        if (!$funcionarioId || !$competencia) {
            return response()->json(['existe' => false]);
        }

        $competenciaDate = Carbon::createFromFormat('Y-m', $competencia)->startOfMonth()->format('Y-m-d');

        $existe = FolhaPagamento::query()->where('funcionario_id', $funcionarioId)
            ->where('competencia', $competenciaDate)
            ->exists();

        return response()->json(['existe' => $existe]);
    }

}