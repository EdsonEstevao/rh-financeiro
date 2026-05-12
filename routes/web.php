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
        Route::middleware('permission:departamentos.gerenciar')->group(function () {
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
        // Visualização
        Route::middleware('permission:folha.view')->group(function () {
            Route::get('/folha-pagamento', [FolhaPagamentoController::class, 'index'])->name('folha-pagamento.index');

            Route::get('/folha-pagamento/calcular', [FolhaPagamentoController::class, 'calcular'])->name('folha-pagamento.calcular');
            Route::get('/folha-pagamento/{folha}', [FolhaPagamentoController::class, 'show'])->name('folha-pagamento.show');
        });

        // Criação
        Route::middleware('permission:folha.create')->group(function () {
            Route::get('/folha-pagamento/create', [FolhaPagamentoController::class, 'create'])->name('folha-pagamento.create');
            Route::post('/folha-pagamento', [FolhaPagamentoController::class, 'store'])->name('folha-pagamento.store');
        });

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