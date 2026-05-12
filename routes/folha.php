<?php

use Illuminate\Routing\Route;

use App\Http\Controllers\RH\FolhaPagamentoController;

Route::middleware(['auth'])->prefix('rh')->name('rh.')->group(function () {
    Route::get('folhas', [FolhaPagamentoController::class, 'index'])
        ->middleware('permission:folha.visualizar')
        ->name('folhas.index');

    Route::get('folhas/create', [FolhaPagamentoController::class, 'create'])
        ->middleware('permission:folha.criar')
        ->name('folhas.create');

    Route::post('folhas', [FolhaPagamentoController::class, 'store'])
        ->middleware('permission:folha.criar')
        ->name('folhas.store');

    Route::get('folhas/{folha}', [FolhaPagamentoController::class, 'show'])
        ->middleware('permission:folha.visualizar')
        ->name('folhas.show');

    Route::post('folhas/{folha}/gerar-holerites', [FolhaPagamentoController::class, 'gerarHolerites'])
        ->middleware('permission:folha.gerar')
        ->name('folhas.gerar-holerites');

    Route::patch('folhas/{folha}/fechar', [FolhaPagamentoController::class, 'fechar'])
        ->middleware('permission:folha.fechar')
        ->name('folhas.fechar');

    Route::patch('folhas/{folha}/reabrir', [FolhaPagamentoController::class, 'reabrir'])
        ->middleware('permission:folha.reabrir')
        ->name('folhas.reabrir');
});
