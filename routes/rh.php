<!-- routes/rh.php
/*
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\RH\{CargoController, DepartamentoController, FolhaPagamentoController, FuncionarioController, PeriodoFeriasController};
PeriodoFeriasController};


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
Route::middleware('permission:funcionarios.view')->group(function () {
Route::get('/funcionarios', [FuncionarioController::class, 'index'])->name('funcionarios.index');
Route::get('/funcionarios/{funcionario}', [FuncionarioController::class, 'show'])->name('funcionarios.show');
});

// Gerenciamento completo (CRUD)
Route::middleware('permission:funcionarios.create')->group(function () {
Route::get('/funcionarios/create', [FuncionarioController::class, 'create'])->name('funcionarios.create');
Route::post('/funcionarios', [FuncionarioController::class, 'store'])->name('funcionarios.store');
Route::get('/funcionarios/{funcionario}/edit', [FuncionarioController::class, 'edit'])->name('funcionarios.edit');
Route::put('/funcionarios/{funcionario}', [FuncionarioController::class, 'update'])->name('funcionarios.update');
Route::delete('/funcionarios/{funcionario}', [FuncionarioController::class, 'destroy'])->name('funcionarios.destroy');
});

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
Route::get('/folhas', [FolhaPagamentoController::class, 'index'])->name('folhas.index');
Route::get('/folhas/{folha}', [FolhaPagamentoController::class, 'show'])->name('folhas.show');
});

// Criação
Route::middleware('permission:folha.create')->group(function () {
Route::get('/folhas/create', [FolhaPagamentoController::class, 'create'])->name('folhas.create');
Route::post('/folhas', [FolhaPagamentoController::class, 'store'])->name('folhas.store');
});

// Geração de holerites
Route::middleware('permission:folha.generate')->group(function () {
Route::post('/folhas/{folha}/gerar-holerites', [FolhaPagamentoController::class,
'gerarHolerites'])->name('folhas.gerar-holerites');
});

// Fechamento
Route::middleware('permission:folha.close')->group(function () {
Route::patch('/folhas/{folha}/fechar', [FolhaPagamentoController::class, 'fechar'])->name('folhas.fechar');
});

// Reabertura
Route::middleware('permission:folha.reopen')->group(function () {
Route::patch('/folhas/{folha}/reabrir', [FolhaPagamentoController::class, 'reabrir'])->name('folhas.reabrir');
});

// Exportação PDF para diaristas (permissão específica)
Route::get('/folha/diaristas/pdf', [FolhaPagamentoController::class, 'exportarFolhaDiaristasPdf'])
->middleware('can:ver_folha_rh')
->name('folha.diaristas.pdf');
});

/*
|--------------------------------------------------------------------------
| Férias dos Funcionários (Nested Resource)
|--------------------------------------------------------------------------
*/
Route::resource('funcionarios.periodos-ferias', PeriodoFeriasController::class)
->middleware(['auth'])
->parameters(['funcionarios' => 'funcionario']);

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
}); -->
