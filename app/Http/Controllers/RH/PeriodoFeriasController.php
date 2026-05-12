<?php

namespace App\Http\Controllers\RH;



use App\Http\Requests\RH\StorePeriodoFeriasRequest;
use App\Http\Controllers\Controller;
use App\Models\Domain\RH\Funcionario;
use App\Services\RH\PeriodoFeriasService;

class PeriodoFeriasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     //
    // }

    // /**
    //  * Show the form for creating a new resource.
    //  */
    // public function create()
    // {
    //     //
    // }

    /**
     * Store a newly created resource in storage.
     */

    public function store(StorePeriodoFeriasRequest $request, Funcionario $funcionario, PeriodoFeriasService $service)
    {
        $periodo = $service->criarPeriodo($funcionario, $request->validated());

        return redirect()
            ->back()
            ->with('success', 'Período de férias criado com sucesso.');
    }


    /**
     * Display the specified resource.
     */
    // public function show(string $id)
    // {
    //     //
    // }

    // /**
    //  * Show the form for editing the specified resource.
    //  */
    // public function edit(string $id)
    // {
    //     //
    // }

    // /**
    //  * Update the specified resource in storage.
    //  */
    // public function update(Request $request, string $id)
    // {
    //     //
    // }

    // /**
    //  * Remove the specified resource from storage.
    //  */
    // public function destroy(string $id)
    // {
    //     //
    // }
}