<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TaxRateService;
use Illuminate\Http\Request;

class TaxRateController extends Controller
{
    protected $service;

    public function __construct(TaxRateService $service)
    {
        $this->service = $service;
    }

    public function create(Request $request)
    {
        try {
            $id = $this->service->create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'TaxRate creado con éxito.',
                'data' => $id,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el TaxRate.',
                'errors' => [$e->getMessage()],
            ], 500);
        }
    }

    public function update(Request $request, int $id)
    {
        try {
            $result = $this->service->update($id, $request->all());

            return response()->json([
                'success' => true,
                'message' => 'TaxRate actualizado con éxito.',
                'data' => $result,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el TaxRate.',
                'errors' => [$e->getMessage()],
            ], 500);
        }
    }

    public function getById(int $id)
    {
        try {
            $taxRate = $this->service->getById($id);

            return response()->json([
                'success' => true,
                'message' => 'TaxRate obtenido con éxito.',
                'data' => $taxRate,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el TaxRate.',
                'errors' => [$e->getMessage()],
            ], 500);
        }
    }

    public function getByFilters(Request $request)
    {
        try {
            $taxRates = $this->service->getByFilters($request->all());

            return response()->json([
                'success' => true,
                'message' => 'TaxRates obtenidos con éxito.',
                'data' => $taxRates,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los TaxRates.',
                'errors' => [$e->getMessage()],
            ], 500);
        }
    }
}
