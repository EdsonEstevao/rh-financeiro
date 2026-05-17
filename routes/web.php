<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RH\{CargoController, DepartamentoController, FolhaPagamentoController, FuncionarioController, PeriodoFeriasController};


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});



/*
|--------------------------------------------------------------------------
| MÓDULO RH (Recursos Humanos)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])
    ->prefix('rh')
    ->name('rh.')
    ->group(function () {

        // Dashboard RH
        Route::get('/', function () {
            return view('rh.dashboard');
        })->middleware('permission:rh.dashboard.view')->name('dashboard');

        // ===================
        // Funcionários
        // ===================
        // Visualização (permissão básica)
        // Route::middleware('permission:funcionarios.view')->group(function () {

        // Gerenciamento completo (CRUD)
        // Route::middleware('permission:funcionarios.create')->group(function () {
                Route::get('/funcionarios', [FuncionarioController::class, 'index'])->name('funcionarios.index')->middleware('permission:funcionarios.view');
                Route::get('/funcionarios/create', [FuncionarioController::class, 'create'])->name('funcionarios.create')->middleware('permission:funcionarios.create');
                Route::post('/funcionarios', [FuncionarioController::class, 'store'])->name('funcionarios.store')->middleware('permission:funcionarios.create');
                Route::get('/funcionarios/{funcionario}', [FuncionarioController::class, 'show'])->name('funcionarios.show')->middleware('permission:funcionarios.view');
                Route::get('/funcionarios/{funcionario}/edit', [FuncionarioController::class, 'edit'])->name('funcionarios.edit')->middleware('permission:funcionarios.edit');
                Route::put('/funcionarios/{funcionario}', [FuncionarioController::class, 'update'])->name('funcionarios.update')->middleware('permission:funcionarios.edit');
                Route::delete('/funcionarios/{funcionario}', [FuncionarioController::class, 'destroy'])->name('funcionarios.destroy')->middleware('permission:funcionarios.delete');

                Route::get('/funcionarios/buscar', [FuncionarioController::class, 'buscar'])->name('funcionarios.buscar');
            // });


        // });



        /*
        |--------------------------------------------------------------------------
        | Férias dos Funcionários (Nested Resource)
        |--------------------------------------------------------------------------
        */
        Route::resource('funcionarios.periodos-ferias', PeriodoFeriasController::class)
            ->middleware(['auth'])
            ->parameters(['funcionarios' => 'funcionario']);

        // ===================
        // Departamentos (Resource)
        // ===================
        Route::middleware('permission:departamentos.view')->group(function () {
            Route::resource('departamentos', DepartamentoController::class)->except(['show']);
        });

        // ===================
        // Cargos (Resource)
        // ===================
        Route::middleware(['auth','permission:cargos.manage'])->group(function () {
            Route::resource('cargos', CargoController::class);
        });

        // ===================
        // Folha de Pagamento
        // ===================

        // Criação
        // Route::middleware('permission:folha.create')->group(function () {
            Route::get('/folha-pagamento', [FolhaPagamentoController::class, 'index'])->name('folha-pagamento.index')->middleware('permission:folha.view');
            Route::get('/folha-pagamento/create', [FolhaPagamentoController::class, 'createNew'])->name('folha-pagamento.create');
            Route::get('/folha-pagamento/buscar', [FolhaPagamentoController::class, 'buscar'])->name('folha-pagamento.buscar');
            Route::get('/folha-pagamento/calendario', [FolhaPagamentoController::class, 'calendario'])->name('folha-pagamento.calendario');
            Route ::get('/folha-pagamento/resumo', [FolhaPagamentoController::class, 'resumo'])->name('folha-pagamento.resumo')->middleware('permission:folha.view');
            Route::get('/folha-pagamento/resumo-geral', [FolhaPagamentoController::class, 'resumoGeral'])->name('folha-pagamento.resumo-geral')->middleware('permission:folha.view');

            Route::get('/folha-pagamento/verificar', [FolhaPagamentoController::class, 'verificarFolhaExistente'])->name('folha-pagamento.verificar');

            Route::get('/folha-pagamento/{folha}', [FolhaPagamentoController::class, 'showNew'])->name('folha-pagamento.show')->middleware('permission:folha.view');
            Route::post('/folha-pagamento', [FolhaPagamentoController::class, 'storeNew'])->name('folha-pagamento.store');
            Route::get('/folha-pagamento/{folha}/edit', [FolhaPagamentoController::class, 'edit'])->name('folha-pagamento.edit')->middleware('permission:folha.generate');
            Route::put('/folha-pagamento/{folha}', [FolhaPagamentoController::class, 'update'])->name('folha-pagamento.update')->middleware('permission:folha.generate');
            Route::delete('/folha-pagamento/{folha}', [FolhaPagamentoController::class, 'destroy'])->name('folha-pagamento.destroy')->middleware('permission:folha.close');
        // });

        // Geração de holerites
        Route::middleware('permission:folha.generate')->group(function () {
            Route::post('/folha-pagamento/{folha}/gerar-holerites', [FolhaPagamentoController::class, 'gerarHolerites'])->name('folha-pagamento.gerar-holerites');
        });

        // Fechamento
        Route::middleware('permission:folha.close')->group(function () {
            Route::patch('/folha-pagamento/{folha}/fechar', [FolhaPagamentoController::class, 'fechar'])->name('folha-pagamento.fechar');
        });

        // Reabertura
        Route::middleware('permission:folha.reopen')->group(function () {
            Route::patch('/folha-pagamento/{folha}/reabrir', [FolhaPagamentoController::class, 'reabrir'])->name('folha-pagamento.reabrir');
        });

        // Exportação PDF para diaristas (permissão específica)
        Route::get('/folha-pagamento/diaristas/pdf', [FolhaPagamentoController::class, 'exportarFolhaDiaristasPdf'])
            ->middleware('can:ver_folha_rh')
            ->name('folha-pagamento.diaristas.pdf');

         // PDFs
        Route::get('folha-pagamento/{folhaPagamento}/pdf',[FolhaPagamentoController::class, 'pdf'])->name('folha-pagamento.pdf');

        Route::get('folha-pagamento-geral/pdf', [FolhaPagamentoController::class, 'pdfGeral'])->name('folha-pagamento.pdf.geral');


    });



/*
|--------------------------------------------------------------------------
| MÓDULO FINANCEIRO (Em desenvolvimento)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])
    ->prefix('financeiro')
    ->name('financeiro.')
    ->group(function () {

        // Dashboard Financeiro
        Route::get('/', function () {
            return view('financeiro.dashboard');
        })->middleware('permission:financeiro.dashboard.view')->name('dashboard');

        // Boletos, cartões e outras funcionalidades
        // Route::resource('boletos', BoletoController::class);
        // Route::resource('cartoes', CartaoController::class);
    });


require __DIR__.'/auth.php';