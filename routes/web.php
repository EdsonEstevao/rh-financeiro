<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\{ActivityLogController, PermissionController, RoleController, UserController};
use App\Http\Controllers\{DashboardController, ProfileController};
use App\Http\Controllers\RH\{CargoController, DepartamentoController, FolhaPagamentoController, FuncionarioController, PeriodoFeriasController, RhDashboardController};


// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

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
        Route::get('/dashboard', [RhDashboardController::class, 'index'])->middleware('permission:rh.dashboard.view')->name('dashboard');

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

                Route::get('/funcionarios/{funcionario}/demitir', [FuncionarioController::class, 'formDemitir'])
                    ->middleware('can:funcionarios.edit')
                    ->name('funcionarios.demitir.form');

                Route::post('/funcionarios/{funcionario}/demitir', [FuncionarioController::class, 'demitir'])
                    ->middleware('can:funcionarios.edit')
                    ->name('funcionarios.demitir');
            // });


        // });



        /*
        |--------------------------------------------------------------------------
        | Férias dos Funcionários (Nested Resource)
        |--------------------------------------------------------------------------
        */
        // Route::resource('funcionarios.periodos-ferias', PeriodoFeriasController::class)
        //     ->middleware(['auth'])
        //     ->parameters(['funcionarios' => 'funcionario']);


        // Férias
        // Route::get('/ferias', [PeriodoFeriasController::class, 'index'])->name('ferias.index')->middleware('permission:ferias.view');
        // Route::get('/ferias/dashboard', [PeriodoFeriasController::class, 'dashboard'])->name('ferias.dashboard');
        // Route::get('/ferias/create', [PeriodoFeriasController::class, 'create'])->name('ferias.create');
        // Route::get('/ferias/{periodo}/edit', [PeriodoFeriasController::class, 'edit'])->name('ferias.edit');
        // Route::put('/ferias/{periodo}', [PeriodoFeriasController::class, 'update'])->name('ferias.update');
        // Route::get('/ferias/{periodo}', [PeriodoFeriasController::class, 'show'])->name('ferias.show');
        // Route::delete('/ferias/{periodo}', [PeriodoFeriasController::class, 'destroy'])->name('ferias.destroy');
        // Route::post('/ferias/{funcionario}/gerar', [PeriodoFeriasController::class, 'gerarNovoPeriodo'])->name('ferias.gerar');

        // ============================================
        // MÓDULO FÉRIAS - ROTAS CORRIGIDAS
        // ============================================

        // Dashboard de Férias
        Route::get('/ferias/dashboard', [PeriodoFeriasController::class, 'dashboard'])
            ->name('ferias.dashboard')
            ->middleware('permission:ferias.view');

        // Listagem de todos os períodos
        Route::get('/ferias', [PeriodoFeriasController::class, 'index'])
            ->name('ferias.index')
            ->middleware('permission:ferias.view');

        // ✅ Criar período para um funcionário específico (com parâmetro)
        Route::get('/ferias/create/{funcionario}', [PeriodoFeriasController::class, 'create'])
            ->name('ferias.create')
            ->middleware('permission:ferias.create');

        // ✅ Salvar novo período (estava faltando!)
        Route::post('/ferias/{funcionario}', [PeriodoFeriasController::class, 'store'])
            ->name('ferias.store')
            ->middleware('permission:ferias.create');

        // Visualizar período
        Route::get('/ferias/{periodo}', [PeriodoFeriasController::class, 'show'])
            ->name('ferias.show')
            ->middleware('permission:ferias.view');

        // Editar período
        Route::get('/ferias/{periodo}/edit', [PeriodoFeriasController::class, 'edit'])
            ->name('ferias.edit')
            ->middleware('permission:ferias.edit');

        // Atualizar período
        Route::put('/ferias/{periodo}', [PeriodoFeriasController::class, 'update'])
            ->name('ferias.update')
            ->middleware('permission:ferias.edit');

        // Cancelar período
        Route::patch('/ferias/{periodo}/cancelar', [PeriodoFeriasController::class, 'cancelar'])
            ->name('ferias.cancelar')
            ->middleware('permission:ferias.cancel');

        // Gerar novo período (atalho)
        Route::post('/ferias/{funcionario}/gerar', [PeriodoFeriasController::class, 'gerarNovoPeriodo'])
            ->name('ferias.gerar')
            ->middleware('permission:ferias.create');

        // Excluir período
        Route::delete('/ferias/{periodo}', [PeriodoFeriasController::class, 'destroy'])
            ->name('ferias.destroy')
            ->middleware('permission:ferias.delete');

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
    // ============================================
    // 🔐 MÓDULO ADMIN (USUÁRIOS, PERFIS, PERMISSÕES)
    // ============================================
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {

        // 👥 Usuários
        Route::get('/users', [UserController::class, 'index'])
            ->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])
            ->name('users.create');
        Route::post('/users', [UserController::class, 'store'])
            ->name('users.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])
            ->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])
            ->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])
            ->name('users.destroy');

        // 🔑 Perfis (Roles)
        Route::get('/roles', [RoleController::class, 'index'])
            ->name('roles.index');
        Route::get('/roles/create', [RoleController::class, 'create'])
            ->name('roles.create');
        Route::post('/roles', [RoleController::class, 'store'])
            ->name('roles.store');
        Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])
            ->name('roles.edit');
        Route::put('/roles/{role}', [RoleController::class, 'update'])
            ->name('roles.update');
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])
            ->name('roles.destroy');

        // 🔒 Permissões
        Route::get('/permissions', [PermissionController::class, 'index'])
            ->name('permissions.index');
        Route::get('/permissions/create', [PermissionController::class, 'create'])
            ->name('permissions.create');
        Route::post('/permissions', [PermissionController::class, 'store'])
            ->name('permissions.store');
        Route::get('/permissions/{permission}/edit', [PermissionController::class, 'edit'])
            ->name('permissions.edit');
        Route::put('/permissions/{permission}', [PermissionController::class, 'update'])
            ->name('permissions.update');
        Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy'])
            ->name('permissions.destroy');

        // 📝 Logs de atividade
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])
            ->name('activity-logs.index');
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
            // return view('financeiro.dashboard');
        })->middleware('permission:financeiro.dashboard.view')->name('dashboard');

        // Boletos, cartões e outras funcionalidades
        // Route::resource('boletos', BoletoController::class);
        // Route::resource('cartoes', CartaoController::class);
    });


require __DIR__.'/auth.php';