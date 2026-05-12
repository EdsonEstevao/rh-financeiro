<?php

namespace App\Http\Controllers\RH;

use Illuminate\Http\{JsonResponse, RedirectResponse, Request};
use Illuminate\View\View;

use App\Http\Controllers\Controller;
use App\Models\Domain\RH\Cargo;

class CargoController extends Controller
{
    /**
     * Exibe a lista de cargos.
     */
    public function index(): View
    {
        $cargos = Cargo::orderBy('titulo', 'asc')->paginate(15);
        return view('rh.cargos.index', compact('cargos'));
    }

    /**
     * Exibe o formulário para criar um novo cargo.
     */
    public function create(): View
    {
        return view('rh.cargos.create');
    }

    /**
     * Armazena um novo cargo no banco.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'titulo' => 'required|string|max:255|unique:cargos,titulo',
            'ativo' => 'nullable|boolean',
        ]);

        $data['ativo'] = $request->boolean('ativo');

        Cargo::create($data);

        return redirect()->route('rh.cargos.index')
            ->with('success', 'Cargo criado com sucesso!');
    }

    /**
     * Exibe o formulário para editar um cargo.
     */
    public function edit(Cargo $cargo): View
    {
        return view('rh.cargos.edit', compact('cargo'));
    }

    /**
     * Atualiza um cargo existente.
     */
    public function update(Request $request, Cargo $cargo): RedirectResponse
    {
        $data = $request->validate([
            'titulo' => 'required|string|max:255|unique:cargos,titulo,' . $cargo->id,
            'ativo' => 'nullable|boolean',
        ]);

        $data['ativo'] = $request->boolean('ativo');

        $cargo->update($data);

        return redirect()->route('rh.cargos.index')
            ->with('success', 'Cargo atualizado com sucesso!');
    }

    /**
     * Remove um cargo do banco.
     */
    public function destroy(Cargo $cargo): RedirectResponse
    {
        // Verificar se há funcionários vinculados
        if ($cargo->funcionarios()->exists()) {
            return back()->with('error', 'Não é possível excluir este cargo pois existem funcionários vinculados a ele.');
        }

        $cargo->delete();

        return redirect()->route('rh.cargos.index')
            ->with('success', 'Cargo excluído com sucesso!');
    }
}
