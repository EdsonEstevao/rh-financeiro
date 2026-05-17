<?php

namespace App\Http\Controllers\RH;

use Illuminate\Http\{RedirectResponse, Request};
use Illuminate\View\View;

use App\Http\Controllers\Controller;
use App\Models\Domain\RH\Departamento;

class DepartamentoController extends Controller
{
    public function index(): View
    {
        $departamentos = Departamento::withCount('funcionarios')
            ->orderBy('nome')
            ->paginate(15);

        return view('rh.departamentos.index', compact('departamentos'));
    }

    public function create(): View
    {
        return view('rh.departamentos.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255|unique:departamentos,nome',
            'ativo' => 'sometimes|boolean',
        ]);

        // Garante que ativo seja false se o checkbox não veio
        $validated['ativo'] = $request->boolean('ativo');

        Departamento::create($validated);

        return redirect()
            ->route('rh.departamentos.index')
            ->with('success', 'Departamento criado com sucesso!');
    }

    public function edit(Departamento $departamento): View
    {
        return view('rh.departamentos.edit', compact('departamento'));
    }

    public function update(Request $request, Departamento $departamento): RedirectResponse
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255|unique:departamentos,nome,' . $departamento->id,
            'ativo' => 'sometimes|boolean',
        ]);

        $validated['ativo'] = $request->boolean('ativo');

        $departamento->update($validated);

        return redirect()
            ->route('rh.departamentos.index')
            ->with('success', 'Departamento atualizado com sucesso!');
    }

    public function destroy(Departamento $departamento): RedirectResponse
    {
        if ($departamento->funcionarios()->exists()) {
            return back()->with(
                'error',
                "Não é possível excluir \"{$departamento->nome}\": há funcionários vinculados."
            );
        }

        $departamento->delete(); // ✅ CORRIGIDO

        return redirect()
            ->route('rh.departamentos.index')
            ->with('success', 'Departamento excluído com sucesso!');
    }
}
