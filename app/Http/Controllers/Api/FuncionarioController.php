<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Models\Domain\RH\Funcionario;
use App\Http\Controllers\Controller;

class FuncionarioController extends Controller
{
    public function buscar(Request $request)
    {
        $query = $request->input('q', '');
        // $query = $request->q ?? '';

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $funcionarios = Funcionario::where('status', 'ativo')
            ->where(function($q) use ($query) {
                $q->where('nome_completo', 'like', "%{$query}%")
                  ->orWhere('matricula', 'like', "%{$query}%")
                  ->orWhere('cpf', 'like', "%{$query}%");
            })
            ->select('id', 'nome', 'matricula', 'cargo', 'salario_base', 'status')
            ->limit(10)
            ->get();

        return response()->json($funcionarios);
    }
}