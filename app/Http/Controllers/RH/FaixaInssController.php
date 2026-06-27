<?php

namespace App\Http\Controllers\RH;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Log};

use App\Http\Controllers\Controller;
use App\Models\Domain\RH\{FaixaInss, TabelaInss};

class FaixaInssController extends Controller
{
    /**
     * Lista todas as tabelas INSS com suas faixas.
     */
    public function index()
    {
        $tabelas = TabelaInss::with('faixas')
            ->orderBy('ano_vigencia', 'desc')
            ->paginate(10);

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

        return view('rh.faixas-inss.index', compact('tabelas', 'faixasPorTabela'));
    }

    /**
     * Formulário para criar nova tabela INSS com faixas.
     */
    public function create()
    {
        return view('rh.faixas-inss.create');
    }

    /**
     * Salva a tabela INSS e suas faixas.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ano_vigencia'    => 'required|digits:4|integer|min:2000|max:2100',
            'descricao'       => 'nullable|string|max:255',
            'ativo'           => 'boolean',
            'vigencia_inicio' => 'required|date',
            'vigencia_fim'    => 'nullable|date|after:vigencia_inicio',
            'faixas'          => 'required|array|min:1',
            'faixas.*.ordem'  => 'required|integer|min:1',
            'faixas.*.teto'   => 'required|numeric|min:0',
            'faixas.*.aliquota' => 'required|numeric|min:0|max:1',
            'faixas.*.deducao'  => 'nullable|numeric|min:0',
        ], [
            'faixas.required' => 'Adicione pelo menos uma faixa.',
        ]);

        DB::beginTransaction();
        try {
            // Se marcar como ativa, desativa as outras
            if ($request->boolean('ativo')) {
                TabelaInss::where('ativo', true)->update(['ativo' => false]);
            }

            $tabela = TabelaInss::create([
                'ano_vigencia'    => $validated['ano_vigencia'],
                'descricao'       => $validated['descricao'],
                'ativo'           => $request->boolean('ativo'),
                'vigencia_inicio' => $validated['vigencia_inicio'],
                'vigencia_fim'    => $validated['vigencia_fim'] ?? null,
            ]);

            foreach ($validated['faixas'] as $faixa) {
                FaixaInss::create([
                    'tabela_inss_id' => $tabela->id,
                    'ordem'          => $faixa['ordem'],
                    'teto'           => $faixa['teto'],
                    'aliquota'       => $faixa['aliquota'],
                    'deducao'        => $faixa['deducao'] ?? 0,
                ]);
            }

            DB::commit();

            activity()
                ->performedOn($tabela)
                ->log('Tabela INSS criada');

            return redirect()
                ->route('rh.faixa-inss.index')
                ->with('success', 'Tabela INSS criada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao criar tabela INSS: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Erro ao criar tabela INSS: ' . $e->getMessage());
        }
    }

    /**
     * Formulário para editar tabela INSS e suas faixas.
     */
    public function edit(TabelaInss $tabelaInss)
    {
        // O parâmetro $faixaInss na verdade é um TabelaInss
        // por causa do route model binding customizado
        $tabela = TabelaInss::with('faixas')->findOrFail($tabelaInss->id);

        return view('rh.faixas-inss.edit', compact('tabela'));
    }

    /**
     * Atualiza a tabela INSS e suas faixas.
     */
    public function update(Request $request, TabelaInss $tabelaInss)
    {
        $tabela = TabelaInss::with('faixas')->findOrFail($tabelaInss->id);

        $validated = $request->validate([
            'ano_vigencia'    => 'required|digits:4|integer|min:2000|max:2100',
            'descricao'       => 'nullable|string|max:255',
            'ativo'           => 'boolean',
            'vigencia_inicio' => 'required|date',
            'vigencia_fim'    => 'nullable|date|after:vigencia_inicio',
            'faixas'          => 'required|array|min:1',
            'faixas.*.ordem'  => 'required|integer|min:1',
            'faixas.*.teto'   => 'required|numeric|min:0',
            'faixas.*.aliquota' => 'required|numeric|min:0|max:1',
            'faixas.*.deducao'  => 'nullable|numeric|min:0',
        ], [
            'faixas.required' => 'Adicione pelo menos uma faixa.',
        ]);

        DB::beginTransaction();
        try {
            if ($request->boolean('ativo') && !$tabela->ativo) {
                TabelaInss::where('ativo', true)->update(['ativo' => false]);
            }

            $tabela->update([
                'ano_vigencia'    => $validated['ano_vigencia'],
                'descricao'       => $validated['descricao'],
                'ativo'           => $request->boolean('ativo'),
                'vigencia_inicio' => $validated['vigencia_inicio'],
                'vigencia_fim'    => $validated['vigencia_fim'] ?? null,
            ]);

            // Remove faixas antigas e recria
            $tabela->faixas()->delete();
            foreach ($validated['faixas'] as $faixa) {
                FaixaInss::create([
                    'tabela_inss_id' => $tabela->id,
                    'ordem'          => $faixa['ordem'],
                    'teto'           => $faixa['teto'],
                    'aliquota'       => $faixa['aliquota'],
                    'deducao'        => $faixa['deducao'] ?? 0,
                ]);
            }

            DB::commit();

            activity()
                ->performedOn($tabela)
                ->log('Tabela INSS atualizada');

            return redirect()
                ->route('rh.faixa-inss.index')
                ->with('success', 'Tabela INSS atualizada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao atualizar tabela INSS: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Erro ao atualizar: ' . $e->getMessage());
        }
    }

    /**
     * Remove a tabela INSS e suas faixas (soft delete).
     */
    public function destroy(TabelaInss $tabelaInss)
    {
        $tabela = TabelaInss::findOrFail($tabelaInss->id);

        DB::beginTransaction();
        try {
            $tabela->delete(); // Soft delete, cascata nas faixas

            DB::commit();

            activity()
                ->performedOn($tabela)
                ->log('Tabela INSS removida');

            return redirect()
                ->route('rh.faixa-inss.index')
                ->with('success', 'Tabela INSS removida com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao remover tabela INSS: ' . $e->getMessage());

            return redirect()
                ->back()
                ->with('error', 'Erro ao remover tabela.');
        }
    }
}