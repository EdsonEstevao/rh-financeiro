<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\RH\{CargoController, DepartamentoController, FolhaPagamentoController, FuncionarioController};

Route::middleware(['auth', 'verified'])
    ->prefix('rh')
    ->name('rh.')
    ->group(function () {

        Route::middleware('permission:rh.visualizar')->group(function () {
            Route::get('/', function () {
                return view('rh.dashboard');
            })->name('dashboard');
        });

        Route::middleware('permission:funcionarios.visualizar')->group(function () {
            Route::get('/funcionarios', [FuncionarioController::class, 'index'])->name('funcionarios.index');
            Route::get('/funcionarios/{funcionario}', [FuncionarioController::class, 'show'])->name('funcionarios.show');
        });

        Route::middleware('permission:funcionarios.gerenciar')->group(function () {
            Route::get('/funcionarios/create', [FuncionarioController::class, 'create'])->name('funcionarios.create');
            Route::post('/funcionarios', [FuncionarioController::class, 'store'])->name('funcionarios.store');
            Route::get('/funcionarios/{funcionario}/edit', [FuncionarioController::class, 'edit'])->name('funcionarios.edit');
            Route::put('/funcionarios/{funcionario}', [FuncionarioController::class, 'update'])->name('funcionarios.update');
        });

        Route::middleware('permission:folha.visualizar')->group(function () {
            Route::get('/folha-pagamento', [FolhaPagamentoController::class, 'index'])->name('folha-pagamento.index');
        });
        Route::get('/folha/diaristas/pdf', [FolhaPagamentoController::class, 'exportarFolhaDiaristasPdf'])
        ->middleware(['auth', 'can:ver_folha_rh']);

        Route::middleware('permission:departamentos.gerenciar')->group(function () {
            Route::resource('departamentos', DepartamentoController::class)->except(['show']);
        });

        Route::middleware('permission:cargos.gerenciar')->group(function () {
            Route::resource('cargos', CargoController::class)->except(['show']);
        });

});