<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;


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





// /*
// --------------------------------------------------------------------------------------------------------------------------
// | RH GESTÃO
// --------------------------------------------------------------------------------------------------------------------------
// */

// Route::middleware(['auth', 'verified'])
//     ->prefix('rh')
//     ->name('rh.')
//     ->group(function () {

//     Route::middleware('permission:rh.visualizar')->group(function () {
//         Route::get('/', fn () => view('rh.dashboard'))->name('dashboard');
//     });




//     Route::middleware('permission:rh.gerenciar')->group(function () {
//         Route::resource('funcionarios', FuncionarioController::class);
//         Route::resource('departamentos', DepartamentoController::class)->except(['show']);
//         Route::resource('cargos', CargoController::class)->except(['show']);
//     });

//     Route::middleware('permission:folha.visualizar')->group(function () {
//         Route::get('/folha-pagamento', [FolhaPagamentoController::class, 'index'])->name('folha-pagamento.index');
//         Route::get('/folha-pagamento/resumo', [FolhaPagamentoController::class, 'resumo'])->name('folha-pagamento.resumo');
//         Route::get('/folha-pagamento/{funcionario}', [FolhaPagamentoController::class, 'show'])->name('folha-pagamento.show');
//     });
// });


require __DIR__.'/auth.php';

require __DIR__.'/rh.php';