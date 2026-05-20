<?php

namespace App\Http\Controllers\RH;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Http\Controllers\Controller;
use App\Models\Domain\RH\{Cargo, Departamento, FolhaPagamento, Funcionario, PeriodoFerias};

class RhDashboardController extends Controller
{
    //
    public function dashboard(Request $request)
    {
        // No controller ou direto na view
        $alertasFerias = [
            'vencendo_30_dias' => Funcionario::feriasVencendo(30)->count('id'),
            'vencidas' => Funcionario::feriasVencidas()->count('id'),
            'total_funcionarios' => Funcionario::ativos()->count('id')
        ];

        return view('rh.dashboard', compact('alertasFerias'));
    }

    // public function index()
    // {
    //     // Estatísticas gerais
    //     $totalFuncionarios = Funcionario::ativos()->count();
    //     $totalDepartamentos = Departamento::where('ativo', true)->count();
    //     $totalCargos = Cargo::where('ativo', true)->count();
    //     $novosFuncionariosEsseMes = $this->getNovosFuncionariosEsseMes();

    //     // Funcionários por departamento
    //     $funcionariosPorDepartamento = $this->getFuncionariosPorDepartamento();

    //     // Funcionários por status
    //     $funcionariosPorStatus = $this->getFuncionariosPorStatus();

    //     // Aniversariantes do mês
    //     $aniversariantesDoMes = $this->getAniversariantesDoMes();

    //     // Próximas férias (próximos 30 dias)
    //     $proximasFerias = $this->getProximasFerias();

    //     // Folha de pagamento dos últimos 6 meses
    //     $dadosFolhaPagamento = $this->getDadosFolhaPagamento();

    //     // Admissões vs Demissões nos últimos 12 meses
    //     $admissoesVsDemissoes = $this->getAdmissoesVsDemissoes();

    //     // Distribuição salarial
    //     $distribuicaoSalarial = $this->getDistribuicaoSalarial();

    //     // Funcionários por gênero
    //     $funcionariosPorGenero = $this->getFuncionariosPorGenero();

    //     // Funcionários por faixa etária
    //     $funcionariosPorIdade = $this->getFuncionariosPorIdade();

    //     return view('rh.dashboard', compact(
    //         'totalFuncionarios',
    //         'totalDepartamentos',
    //         'totalCargos',
    //         'novosFuncionariosEsseMes',
    //         'funcionariosPorDepartamento',
    //         'funcionariosPorStatus',
    //         'aniversariantesDoMes',
    //         'proximasFerias',
    //         'dadosFolhaPagamento',
    //         'admissoesVsDemissoes',
    //         'distribuicaoSalarial',
    //         'funcionariosPorGenero',
    //         'funcionariosPorIdade'
    //     ));
    // }
      public function index()
    {
        // Estatísticas gerais
        $totalFuncionarios = Funcionario::ativos()->count();
        $totalDepartamentos = Departamento::where('ativo', true)->count();
        $totalCargos = Cargo::where('ativo', true)->count();
        $novosFuncionariosEsseMes = $this->getNovosFuncionariosEsseMes();

        // Funcionários por departamento
        $funcionariosPorDepartamento = $this->getFuncionariosPorDepartamento();

        // Funcionários por status
        $funcionariosPorStatus = $this->getFuncionariosPorStatus();

        // Aniversariantes do mês
        $aniversariantesDoMes = $this->getAniversariantesDoMes();

        // Próximas férias (próximos 30 dias)
        $proximasFerias = $this->getProximasFerias();

        // Folha de pagamento dos últimos 6 meses
        $dadosFolhaPagamento = $this->getDadosFolhaPagamento();

        // Admissões vs Demissões nos últimos 12 meses
        $admissoesVsDemissoes = $this->getAdmissoesVsDemissoes();

        // Distribuição salarial
        $distribuicaoSalarial = $this->getDistribuicaoSalarial();

        // Funcionários por gênero
        $funcionariosPorGenero = $this->getFuncionariosPorGenero();

        // Funcionários por faixa etária
        $funcionariosPorIdade = $this->getFuncionariosPorIdade();

        return view('rh.dashboard', compact(
            'totalFuncionarios',
            'totalDepartamentos',
            'totalCargos',
            'novosFuncionariosEsseMes',
            'funcionariosPorDepartamento',
            'funcionariosPorStatus',
            'aniversariantesDoMes',
            'proximasFerias',
            'dadosFolhaPagamento',
            'admissoesVsDemissoes',
            'distribuicaoSalarial',
            'funcionariosPorGenero',
            'funcionariosPorIdade'
        ));
    }

    private function getNovosFuncionariosEsseMes()
    {
        return Funcionario::whereHas('contrato', function($query) {
                $query->whereMonth('data_admissao', Carbon::now()->month)
                      ->whereYear('data_admissao', Carbon::now()->year);
            })
            ->count();
    }

    private function getFuncionariosPorDepartamento()
    {
        return Departamento::with('funcionariosAtivos')
                          ->where('ativo', true)
                          ->get()
                          ->map(function ($dept) {
                              return [
                                  'name' => $dept->nome,
                                  'count' => $dept->funcionariosAtivos->count()
                              ];
                          })
                          ->filter(function ($item) {
                              return $item['count'] > 0;
                          })
                          ->values();
    }

    private function getFuncionariosPorStatus()
    {
        $funcionarios = Funcionario::with(['periodoFerias' => function($query) {
            $query->where('status', 'gozada')
                  ->where('data_inicio', '<=', now())
                  ->where('data_fim', '>=', now());
        }])->get();

        $statusCount = [
            'ativo' => 0,
            'inativo' => 0,
            'ferias' => 0
        ];

        foreach ($funcionarios as $funcionario) {
            if (!$funcionario->ativo) {
                $statusCount['inativo']++;
            } elseif ($funcionario->periodoFerias->count() > 0) {
                $statusCount['ferias']++;
            } else {
                $statusCount['ativo']++;
            }
        }

        return $statusCount;
    }

    private function getAniversariantesDoMes()
    {
        return Funcionario::with(['contatos'])
                         ->whereMonth('data_nascimento', Carbon::now()->month)
                         ->ativos()
                         ->orderBy('data_nascimento')
                         ->take(10)
                         ->get(['id', 'nome_completo', 'data_nascimento']);
    }

    private function getProximasFerias()
    {
        return PeriodoFerias::with(['funcionario:id,nome_completo'])
                           ->where('data_inicio', '>=', Carbon::now())
                           ->where('data_inicio', '<=', Carbon::now()->addDays(30))
                           ->whereIn('status', ['planejada', 'aprovada'])
                           ->orderBy('data_inicio')
                           ->take(10)
                           ->get();
    }

    private function getDadosFolhaPagamento()
    {
        return FolhaPagamento::with('holerite')
                           ->select(
                               DB::raw('MONTH(competencia) as mes'),
                               DB::raw('YEAR(competencia) as ano'),
                               DB::raw('COUNT(*) as total_funcionarios')
                           )
                           ->where('competencia', '>=', Carbon::now()->subMonths(6))
                           ->where('status', 'fechada')
                           ->groupBy('mes', 'ano')
                           ->orderBy('ano', 'asc')
                           ->orderBy('mes', 'asc')
                           ->get()
                           ->map(function ($item) {
                               // Buscar dados dos holerites para essa competência
                               $competencia = Carbon::createFromDate($item->ano, $item->mes, 1);
                               $holerites = DB::table('holerites')
                                          ->join('folha_pagamentos', 'holerites.folha_pagamento_id', '=', 'folha_pagamentos.id')
                                          ->where('folha_pagamentos.competencia', $competencia->format('Y-m-d'))
                                          ->selectRaw('
                                              SUM(holerites.salario_bruto) as total_bruto,
                                              SUM(holerites.salario_liquido) as total_liquido
                                          ')
                                          ->first();

                               return [
                                   'periodo' => $competencia->format('M/Y'),
                                   'total_bruto' => $holerites->total_bruto ?? 0,
                                   'total_liquido' => $holerites->total_liquido ?? 0,
                                   'total_funcionarios' => $item->total_funcionarios
                               ];
                           });
    }

    private function getAdmissoesVsDemissoes()
    {
        // Admissões nos últimos 12 meses
        $admissoes = DB::table('funcionario_contratos')
                      ->selectRaw('MONTH(data_admissao) as mes, YEAR(data_admissao) as ano, COUNT(*) as count')
                      ->where('data_admissao', '>=', Carbon::now()->subYear())
                      ->groupBy('mes', 'ano')
                      ->get()
                      ->keyBy(function ($item) {
                          return $item->ano . '-' . str_pad($item->mes, 2, '0', STR_PAD_LEFT);
                      });

        // Demissões nos últimos 12 meses
        $demissoes = DB::table('funcionario_contratos')
                      ->selectRaw('MONTH(data_demissao) as mes, YEAR(data_demissao) as ano, COUNT(*) as count')
                      ->whereNotNull('data_demissao')
                      ->where('data_demissao', '>=', Carbon::now()->subYear())
                      ->groupBy('mes', 'ano')
                      ->get()
                      ->keyBy(function ($item) {
                          return $item->ano . '-' . str_pad($item->mes, 2, '0', STR_PAD_LEFT);
                      });

        $resultado = collect();
        for ($i = 11; $i >= 0; $i--) {
            $data = Carbon::now()->subMonths($i);
            $chave = $data->format('Y-m');
            
            $resultado->push([
                'periodo' => $data->format('M/Y'),
                'admissoes' => $admissoes->get($chave)->count ?? 0,
                'demissoes' => $demissoes->get($chave)->count ?? 0
            ]);
        }

        return $resultado;
    }

    private function getDistribuicaoSalarial()
    {
        $faixas = [
            'Até R$ 2.000' => [0, 2000],
            'R$ 2.001 - R$ 4.000' => [2001, 4000],
            'R$ 4.001 - R$ 6.000' => [4001, 6000],
            'R$ 6.001 - R$ 10.000' => [6001, 10000],
            'Acima de R$ 10.000' => [10001, 999999]
        ];

        $dados = [];
        foreach ($faixas as $label => $range) {
            $count = DB::table('funcionarios')
                      ->join('funcionario_contratos', 'funcionarios.id', '=', 'funcionario_contratos.funcionario_id')
                      ->where('funcionarios.ativo', true)
                      ->whereBetween('funcionario_contratos.salario_base', $range)
                      ->count();

            $dados[] = [
                'label' => $label,
                'value' => $count
            ];
        }

        return $dados;
    }

    private function getFuncionariosPorGenero()
    {
        return Funcionario::select('genero', DB::raw('count(*) as count'))
                         ->ativos()
                         ->groupBy('genero')
                         ->get()
                         ->mapWithKeys(function ($item) {
                             $generoLabel = [
                                 'masculino' => 'Masculino',
                                 'feminino' => 'Feminino',
                                 'outro' => 'Outro'
                             ];
                             return [$generoLabel[$item->genero] ?? $item->genero => $item->count];
                         });
    }

    private function getFuncionariosPorIdade()
    {
        $faixas = [
            '18-25' => [18, 25],
            '26-35' => [26, 35],
            '36-45' => [36, 45],
            '46-55' => [46, 55],
            '56+' => [56, 100]
        ];

        $dados = [];
        foreach ($faixas as $label => $range) {
            $count = Funcionario::whereRaw('TIMESTAMPDIFF(YEAR, data_nascimento, CURDATE()) BETWEEN ? AND ?', $range)
                               ->ativos()
                               ->count();
            $dados[] = [
                'label' => $label,
                'value' => $count
            ];
        }

        return $dados;
    }

    public function getChartData(Request $request)
    {
        $type = $request->get('type');
        
        switch ($type) {
            case 'departamento-distribuicao':
                return response()->json($this->getFuncionariosPorDepartamento());
                
            case 'distribuicao-salarial':
                return response()->json($this->getDistribuicaoSalarial());
                
            case 'distribuicao-idade':
                return response()->json($this->getFuncionariosPorIdade());

            case 'funcionarios-genero':
                return response()->json($this->getFuncionariosPorGenero());
                
            default:
                return response()->json(['error' => 'Tipo de gráfico inválido'], 400);
        }
    }
    public function index2()
    {
        $hoje = now()->startOfDay();
        $inicioMes = now()->startOfMonth();
        $fimMes = now()->endOfMonth();

        // 📊 Cards principais
        $totalFuncionarios = Funcionario::count();
        $funcionariosAtivos = Funcionario::where('ativo', true)->count();
        
        $feriasVencidas = Funcionario::where('ativo', true)
            ->where('ferias_vencidas', true)
            ->count();

        $feriasEmAndamento = PeriodoFerias::whereIn('status', ['gozada', 'aprovada'])
            ->where('data_inicio', '<=', $hoje)
            ->where('data_fim', '>=', $hoje)
            ->count();

        $folhaMesAtual = FolhaPagamento::whereMonth('competencia', now()->month)
            ->whereYear('competencia', now()->year)
            ->count();

        // 📅 Próximas férias (30 dias)
        $proximasFerias = PeriodoFerias::with('funcionario.cargo')
            ->whereIn('status', ['aprovada', 'planejada'])
            ->where('data_inicio', '>=', $hoje)
            ->where('data_inicio', '<=', $hoje->copy()->addDays(30))
            ->orderBy('data_inicio')
            ->limit(10)
            ->get();

        // 🎂 Aniversariantes do mês
        $aniversariantes = Funcionario::with('cargo')
            ->where('ativo', true)
            ->whereMonth('data_nascimento', now()->month)
            ->orderByRaw('DAY(data_nascimento)')
            ->get();

        // 🆕 Últimas admissões
        $ultimasAdmissoes = Funcionario::with(['cargo', 'contrato'])
            ->where('ativo', true)
            ->whereHas('contrato', function($q) {
                $q->whereNotNull('data_admissao');
            })
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // 🏢 Distribuição por departamento
        $departamentos = Departamento::withCount(['funcionarios' => function($q) {
                $q->where('ativo', true);
            }])
            ->orderBy('nome')
            ->get();

        return view('rh.dashboard', compact(
            'totalFuncionarios',
            'funcionariosAtivos',
            'feriasVencidas',
            'feriasEmAndamento',
            'folhaMesAtual',
            'proximasFerias',
            'aniversariantes',
            'ultimasAdmissoes',
            'departamentos'
        ));
    }

    private function getNovosFuncionariosEsseMes2()
    {
        return Funcionario::whereHas('contrato', function($query) {
                $query->whereMonth('data_admissao', Carbon::now()->month)
                      ->whereYear('data_admissao', Carbon::now()->year);
            })
            ->count();
    }

    private function getFuncionariosPorDepartamento2()
    {
        return Departamento::with('funcionariosAtivos')
                          ->where('ativo', true)
                          ->get()
                          ->map(function ($dept) {
                              return [
                                  'name' => $dept->nome,
                                  'count' => $dept->funcionariosAtivos->count()
                              ];
                          })
                          ->filter(function ($item) {
                              return $item['count'] > 0;
                          })
                          ->values();
    }

    private function getFuncionariosPorStatus2()
    {
        $funcionarios = Funcionario::with(['periodosFerias' => function($query) {
            $query->where('status', 'gozada')
                  ->where('data_inicio', '<=', now())
                  ->where('data_fim', '>=', now());
        }])->get();

        $statusCount = [
            'ativo' => 0,
            'inativo' => 0,
            'ferias' => 0
        ];

        foreach ($funcionarios as $funcionario) {
            if (!$funcionario->ativo) {
                $statusCount['inativo']++;
            } elseif ($funcionario->periodosFerias->count() > 0) {
                $statusCount['ferias']++;
            } else {
                $statusCount['ativo']++;
            }
        }

        return $statusCount;
    }

    private function getAniversariantesDoMes2()
    {
        return Funcionario::with(['contatos'])
                         ->whereMonth('data_nascimento', Carbon::now()->month)
                         ->ativos()
                         ->orderBy('data_nascimento')
                         ->take(10)
                         ->get(['id', 'nome_completo', 'data_nascimento']);
    }

    private function getProximasFerias2()
    {
        return PeriodoFerias::with(['funcionario:id,nome_completo'])
                           ->where('data_inicio', '>=', Carbon::now())
                           ->where('data_inicio', '<=', Carbon::now()->addDays(30))
                           ->whereIn('status', ['planejada', 'aprovada'])
                           ->orderBy('data_inicio')
                           ->take(10)
                           ->get();
    }

    private function getDadosFolhaPagamento2()
    {
        return FolhaPagamento::with('holerite')
                           ->select(
                               DB::raw('MONTH(competencia) as mes'),
                               DB::raw('YEAR(competencia) as ano'),
                               DB::raw('COUNT(*) as total_funcionarios')
                           )
                           ->where('competencia', '>=', Carbon::now()->subMonths(6))
                           ->where('status', 'fechada')
                           ->groupBy('mes', 'ano')
                           ->orderBy('ano', 'asc')
                           ->orderBy('mes', 'asc')
                           ->get()
                           ->map(function ($item) {
                               // Buscar dados dos holerites para essa competência
                               $competencia = Carbon::createFromDate($item->ano, $item->mes, 1);
                               $holerites = DB::table('holerites')
                                          ->join('folha_pagamentos', 'holerites.folha_pagamento_id', '=', 'folha_pagamentos.id')
                                          ->where('folha_pagamentos.competencia', $competencia->format('Y-m-d'))
                                          ->selectRaw('
                                              SUM(holerites.salario_bruto) as total_bruto,
                                              SUM(holerites.salario_liquido) as total_liquido
                                          ')
                                          ->first();

                               return [
                                   'periodo' => $competencia->format('M/Y'),
                                   'total_bruto' => $holerites->total_bruto ?? 0,
                                   'total_liquido' => $holerites->total_liquido ?? 0,
                                   'total_funcionarios' => $item->total_funcionarios
                               ];
                           });
    }

    private function getAdmissoesVsDemissoes2()
    {
        // Admissões nos últimos 12 meses
        $admissoes = DB::table('funcionario_contratos')
                      ->selectRaw('MONTH(data_admissao) as mes, YEAR(data_admissao) as ano, COUNT(*) as count')
                      ->where('data_admissao', '>=', Carbon::now()->subYear())
                      ->groupBy('mes', 'ano')
                      ->get()
                      ->keyBy(function ($item) {
                          return $item->ano . '-' . str_pad($item->mes, 2, '0', STR_PAD_LEFT);
                      });

        // Demissões nos últimos 12 meses
        $demissoes = DB::table('funcionario_contratos')
                      ->selectRaw('MONTH(data_demissao) as mes, YEAR(data_demissao) as ano, COUNT(*) as count')
                      ->whereNotNull('data_demissao')
                      ->where('data_demissao', '>=', Carbon::now()->subYear())
                      ->groupBy('mes', 'ano')
                      ->get()
                      ->keyBy(function ($item) {
                          return $item->ano . '-' . str_pad($item->mes, 2, '0', STR_PAD_LEFT);
                      });

        $resultado = collect();
        for ($i = 11; $i >= 0; $i--) {
            $data = Carbon::now()->subMonths($i);
            $chave = $data->format('Y-m');
            
            $resultado->push([
                'periodo' => $data->format('M/Y'),
                'admissoes' => $admissoes->get($chave)->count ?? 0,
                'demissoes' => $demissoes->get($chave)->count ?? 0
            ]);
        }

        return $resultado;
    }

    private function getDistribuicaoSalarial2()
    {
        $faixas = [
            'Até R$ 2.000' => [0, 2000],
            'R$ 2.001 - R$ 4.000' => [2001, 4000],
            'R$ 4.001 - R$ 6.000' => [4001, 6000],
            'R$ 6.001 - R$ 10.000' => [6001, 10000],
            'Acima de R$ 10.000' => [10001, 999999]
        ];

        $dados = [];
        foreach ($faixas as $label => $range) {
            $count = DB::table('funcionarios')
                      ->join('funcionario_contratos', 'funcionarios.id', '=', 'funcionario_contratos.funcionario_id')
                      ->where('funcionarios.ativo', true)
                      ->whereBetween('funcionario_contratos.salario_base', $range)
                      ->count();

            $dados[] = [
                'label' => $label,
                'value' => $count
            ];
        }

        return $dados;
    }

    private function getFuncionariosPorGenero2()
    {
        return Funcionario::select('genero', DB::raw('count(*) as count'))
                        ->ativos()
                        ->groupBy('genero')
                        ->get()
                        ->mapWithKeys(function ($item) {
                            $generoLabel = [
                                'masculino' => 'Masculino',
                                'feminino' => 'Feminino',
                                'outro' => 'Outro'
                            ];
                            return [$generoLabel[$item->genero] ?? $item->genero => $item->count];
                        });
    }

    private function getFuncionariosPorIdade2()
    {
        $faixas = [
            '18-25' => [18, 25],
            '26-35' => [26, 35],
            '36-45' => [36, 45],
            '46-55' => [46, 55],
            '56+' => [56, 100]
        ];

        $dados = [];
        foreach ($faixas as $label => $range) {
            $count = Funcionario::whereRaw('TIMESTAMPDIFF(YEAR, data_nascimento, CURDATE()) BETWEEN ? AND ?', $range)
                            ->ativos()
                            ->count();
            $dados[] = [
                'label' => $label,
                'value' => $count
            ];
        }

        return $dados;
    }

    public function getChartData2(Request $request)
    {
        $type = $request->type;
        
        switch ($type) {
            case 'departamento-distribuicao':
                return response()->json($this->getFuncionariosPorDepartamento());
                
            case 'distribuicao-salarial':
                return response()->json($this->getDistribuicaoSalarial());
                
            case 'distribuicao-idade':
                return response()->json($this->getFuncionariosPorIdade());

            case 'funcionarios-genero':
                return response()->json($this->getFuncionariosPorGenero());
                
            default:
                return response()->json(['error' => 'Tipo de gráfico inválido'], 400);
        }
    }


}