<?php

namespace App\Http\Controllers\RH;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Models\Domain\RH\Cargo;

class CargoController extends Controller
{
    //
    // public function index(Request $request)
    // {
    //     return view('rh.cargos.index');
    // }
    public function index()
    {
        $cargos = Cargo::orderBy('titulo', 'asc')->paginate(15);
        return view('rh.cargos.index', compact('cargos'));
    }

    public function create()
    {
        return view('rh.cargos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255|unique:cargos,titulo',
            'ativo' => 'sometimes|boolean'
        ]);

        Cargo::create($request->only(['titulo', 'ativo']));

        return redirect()->route('rh.cargos.index')
                        ->with('success', 'Cargo criado com sucesso!');
    }

    public function edit(Cargo $cargo)
    {
        return view('rh.cargos.edit', compact('cargo'));
    }

    public function update(Request $request, Cargo $cargo)
    {
        $request->validate([
            'titulo' => 'required|string|max:255|unique:cargos,titulo,' . $cargo->id,
            'ativo' => 'sometimes|boolean'
        ]);

        $cargo->update($request->only(['titulo', 'ativo']));

        return redirect()->route('rh.cargos.index')
                        ->with('success', 'Cargo atualizado com sucesso!');
    }

    public function destroy(Cargo $cargo)
    {
        if ($cargo->funcionarios()->count() > 0) {
            return back()->with('error', 'Não é possível excluir cargo com funcionários vinculados.');
        }

        $cargo->delete();
        return redirect()->route('rh.cargos.index')
                        ->with('success', 'Cargo excluído com sucesso!');
    }
}
