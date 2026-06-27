<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\{Auth, Log};
use RuntimeException;
use Throwable;

use App\Http\Requests\RH\Api\{CalcularInssRequest, CalcularIrrfRequest};
use App\Http\Controllers\Controller;
use App\Models\Domain\RH\Funcionario;
use App\Services\RH\CalculoTributarioService;

class CalculoTributarioController extends Controller
{
    public function __construct(
        private readonly CalculoTributarioService $calculoService
    ) {}

    /**
     * Calcula o INSS com base na tabela vigente.
     */
    public function calcularInss(CalcularInssRequest $request): JsonResponse
    {
        try {
            $salario = (float) $request->input('salario');
            $competencia = $request->input('competencia');

            $resultado = $this->calculoService->calcularInss($salario, $competencia);

            return response()->json([
                'success' => true,
                'inss' => $resultado['inss'],
                'aliquota_efetiva' => $resultado['aliquota_efetiva'],
                'detalhamento' => $resultado['detalhamento'],
            ]);

        } catch (RuntimeException $e) {
            Log::warning('Cálculo INSS: ' . $e->getMessage(), [
                'user_id' => Auth::id(), //auth()->id(),
                'payload' => $request->validated(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);

        } catch (Throwable $e) {
            Log::error('Erro inesperado ao calcular INSS', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(), //auth()->id(),
                'payload' => $request->validated(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao calcular o INSS. Tente novamente.',
            ], 500);
        }
    }

    /**
     * Calcula o IRRF com base na tabela vigente.
     */
    // public function calcularIrrf(float $baseIrrf, int $dependentes = 0, ?string $dataReferencia = null): array
    // {
    //     return $this->calculoService->calcularIrrf($baseIrrf, $dependentes, $dataReferencia);
    // }

    public function calcularIrrf(CalcularIrrfRequest $request): JsonResponse
    {

        $salario = (float) $request->input('salario');
        $inss = (float) $request->input('inss');
        // $dependentes = (int) $request->input('dependentes', 0);
        $dependentes = Funcionario::find($request->funcionario_id)->dependentes()->count();
        $competencia = $request->input('competencia');

        $resultado = $this->calculoService->calcularIrrf(
            $salario,
            $inss,
            $dependentes,
            $competencia
        );

        return response()->json([
            'success' => true,
            'irrf' => $resultado['irrf'],
            'base_calculo' => $resultado['base_calculo'],
            'aliquota' => $resultado['aliquota'],
            'parcela_deduzir' => $resultado['parcela_deduzir'],
            'detalhamento' => $resultado['detalhamento'],
        ]);
    }
}