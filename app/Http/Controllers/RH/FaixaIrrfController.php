<?php

namespace App\Http\Controllers\RH;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Log};

use App\Http\Controllers\Controller;
use App\Models\Domain\RH\{FaixaIrrf, TabelaIrrf};

class FaixaIrrfController extends Controller
{
    /**
     * Lista todas as tabelas IRRF com suas faixas.
     */
    public function index()
    {
        $tabelas = TabelaIrrf::with('faixas')
            ->orderBy('ano_vigencia', 'desc')
            ->paginate(10);

       // Prepara dados para o front-end (evita expor tudo, só o necessário)
        $faixasPorTabela = $tabelas->mapWithKeys(function ($tabela) {
            return [
                $tabela->id => [
                    'descricao' => $tabela->ano_vigencia . ($tabela->descricao ? ' - ' . $tabela->descricao : ''),
                    'faixas'    => $tabela->faixas->map(fn($f) => [
                        'ordem'    => $f->ordem,
                        'teto'     => $f->teto,
                        'aliquota' => $f->aliquota,
                        'deducao'  => $f->deducao,
                    ])->toArray(),
                ],
            ];
        });

    // ⚠️ Importante: apenas os IDs que aparecem na página atual
    // Como está paginado, só as tabelas da página atual estarão no mapWithKeys

        // dd($tabelas);

        return view('rh.faixas-irrf.index', compact('tabelas', 'faixasPorTabela'));
    }

    /**
     * Formulário para criar nova tabela IRRF.
     */
    public function create()
    {
        return view('rh.faixas-irrf.create');
    }

    /**
     * Salva a tabela IRRF e suas faixas.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ano_vigencia'       => 'required|digits:4|integer|min:2000|max:2100',
            'descricao'          => 'nullable|string|max:255',
            'ativo'              => 'boolean',
            'vigencia_inicio'    => 'required|date',
            'vigencia_fim'       => 'nullable|date|after:vigencia_inicio',
            'deducao_dependente' => 'nullable|numeric|min:0',
            'deducao_pensao'     => 'nullable|numeric|min:0',
            'faixas'             => 'required|array|min:1',
            'faixas.*.ordem'     => 'required|integer|min:1',
            'faixas.*.teto'      => 'required|numeric|min:0',
            'faixas.*.aliquota'  => 'required|numeric|min:0|max:1',
            'faixas.*.deducao'   => 'nullable|numeric|min:0',
        ], [
            'faixas.required' => 'Adicione pelo menos uma faixa.',
        ]);

        DB::beginTransaction();
        try {
            if ($request->boolean('ativo')) {
                TabelaIrrf::where('ativo', true)->update(['ativo' => false]);
            }

            $tabela = TabelaIrrf::create([
                'ano_vigencia'       => $validated['ano_vigencia'],
                'descricao'          => $validated['descricao'],
                'ativo'              => $request->boolean('ativo'),
                'vigencia_inicio'    => $validated['vigencia_inicio'],
                'vigencia_fim'       => $validated['vigencia_fim'] ?? null,
                'deducao_dependente' => $validated['deducao_dependente'] ?? 189.59,
                'deducao_pensao'     => $validated['deducao_pensao'] ?? 0,
            ]);

            foreach ($validated['faixas'] as $faixa) {
                FaixaIrrf::create([
                    'tabela_irrf_id' => $tabela->id,
                    'ordem'          => $faixa['ordem'],
                    'teto'           => $faixa['teto'],
                    'aliquota'       => $faixa['aliquota'],
                    'deducao'        => $faixa['deducao'] ?? 0,
                ]);
            }

            DB::commit();

            activity()
                ->performedOn($tabela)
                ->log('Tabela IRRF criada');

            return redirect()
                ->route('rh.faixa-irrf.index')
                ->with('success', 'Tabela IRRF criada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao criar tabela IRRF: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Erro ao criar tabela IRRF: ' . $e->getMessage());
        }
    }

    /**
     * Formulário para editar tabela IRRF.
     */
    public function edit(TabelaIrrf $tabelaIrrf)
    {
        $tabela = TabelaIrrf::with('faixas')->findOrFail($tabelaIrrf->id);

        return view('rh.faixas-irrf.edit', compact('tabela'));
    }

    /**
     * Atualiza a tabela IRRF e suas faixas.
     */
    public function update(Request $request, TabelaIrrf $tabelaIrrf)
    {
        $tabela = TabelaIrrf::with('faixas')->findOrFail($tabelaIrrf->id);

        $validated = $request->validate([
            'ano_vigencia'       => 'required|digits:4|integer|min:2000|max:2100',
            'descricao'          => 'nullable|string|max:255',
            'ativo'              => 'boolean',
            'vigencia_inicio'    => 'required|date',
            'vigencia_fim'       => 'nullable|date|after:vigencia_inicio',
            'deducao_dependente' => 'nullable|numeric|min:0',
            'deducao_pensao'     => 'nullable|numeric|min:0',
            'faixas'             => 'required|array|min:1',
            'faixas.*.ordem'     => 'required|integer|min:1',
            'faixas.*.teto'      => 'required|numeric|min:0',
            'faixas.*.aliquota'  => 'required|numeric|min:0|max:1',
            'faixas.*.deducao'   => 'nullable|numeric|min:0',
        ], [
            'faixas.required' => 'Adicione pelo menos uma faixa.',
        ]);

        DB::beginTransaction();
        try {
            if ($request->boolean('ativo') && !$tabela->ativo) {
                TabelaIrrf::where('ativo', true)->update(['ativo' => false]);
            }

            $tabela->update([
                'ano_vigencia'       => $validated['ano_vigencia'],
                'descricao'          => $validated['descricao'],
                'ativo'              => $request->boolean('ativo'),
                'vigencia_inicio'    => $validated['vigencia_inicio'],
                'vigencia_fim'       => $validated['vigencia_fim'] ?? null,
                'deducao_dependente' => $validated['deducao_dependente'] ?? 189.59,
                'deducao_pensao'     => $validated['deducao_pensao'] ?? 0,
            ]);

            $tabela->faixas()->delete();
            foreach ($validated['faixas'] as $faixa) {
                FaixaIrrf::create([
                    'tabela_irrf_id' => $tabela->id,
                    'ordem'          => $faixa['ordem'],
                    'teto'           => $faixa['teto'],
                    'aliquota'       => $faixa['aliquota'],
                    'deducao'        => $faixa['deducao'] ?? 0,
                ]);
            }

            DB::commit();

            activity()
                ->performedOn($tabela)
                ->log('Tabela IRRF atualizada');

            return redirect()
                ->route('rh.faixa-irrf.index')
                ->with('success', 'Tabela IRRF atualizada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao atualizar tabela IRRF: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Erro ao atualizar: ' . $e->getMessage());
        }
    }

    /**
     * Remove a tabela IRRF e suas faixas.
     */
    public function destroy(TabelaIrrf $tabelaIrrf)
    {
        $tabela = TabelaIrrf::with('faixas')->findOrFail($tabelaIrrf->id);

        DB::beginTransaction();
        try {
            $tabela->delete();

            DB::commit();

            activity()
                ->performedOn($tabela)
                ->log('Tabela IRRF removida');

            return redirect()
                ->route('rh.faixa-irrf.index')
                ->with('success', 'Tabela IRRF removida com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao remover tabela IRRF: ' . $e->getMessage());

            return redirect()
                ->back()
                ->with('error', 'Erro ao remover tabela.');
        }
    }
}