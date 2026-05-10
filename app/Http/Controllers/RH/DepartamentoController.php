<?php

namespace App\Http\Controllers\RH;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Models\Domain\RH\Departamento;

class DepartamentoController extends Controller
{
    //
    public function index()
    {
        $departamentos = Departamento::orderBy('nome', 'asc')->paginate(15);
        return view('rh.departamentos.index', compact('departamentos'));
    }

    public function create()
    {
        return view('rh.departamentos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255|unique:departamentos,nome',
            'ativo' => 'sometimes|boolean'
        ]);

        Departamento::create($request->only(['nome', 'ativo']));

        return redirect()->route('rh.departamentos.index')
                        ->with('success', 'Departamento criado com sucesso!');
    }

    public function edit(Departamento $departamento)
    {
        return view('rh.departamentos.edit', compact('departamento'));
    }

    public function update(Request $request, Departamento $departamento)
    {
        $request->validate([
            'nome' => 'required|string|max:255|unique:departamentos,nome,' . $departamento->id,
            'ativo' => 'sometimes|boolean'
        ]);

        $departamento->update($request->only(['nome', 'ativo']));

        return redirect()->route('rh.departamentos.index')
                        ->with('success', 'Departamento atualizado com sucesso!');
    }

    public function destroy(Departamento $departamento)
    {
        if ($departamento->funcionarios()->count() > 0) {
            return back()->with('error', 'Não é possível excluir departamento com funcionários vinculados.');
        }

        $departamento->destroy(['id', $departamento->id]);
        return redirect()->route('rh.departamentos.index')
                        ->with('success', 'Departamento excluído com sucesso!');
    }

}