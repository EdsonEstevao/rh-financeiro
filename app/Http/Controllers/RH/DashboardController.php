<?php

namespace App\Http\Controllers\RH;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Models\Domain\RH\Funcionario;

class DashboardController extends Controller
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


}
