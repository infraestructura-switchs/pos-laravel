<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TributeService;
use Illuminate\Http\Request;

class TributeController extends Controller
{
    protected $tributeService;

    public function __construct(TributeService $tributeService)
    {
        $this->tributeService = $tributeService;
    }

    public function create(Request $request)
    {
        try {
            $id = $this->tributeService->create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Tributo creado correctamente.',
                'data' => ['id' => $id],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el tributo.',
                'errors' => [$e->getMessage()],
            ], 500);
        }
    }

    public function update($id, Request $request)
    {
        try {
            $this->tributeService->update($id, $request->all());

            return response()->json([
                'success' => true,
                'message' => 'Tributo actualizado correctamente.',
                'data' => true
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el tributo.',
                'errors' => [$e->getMessage()],
            ], 500);
        }
    }

    public function getById($id)
    {
        try {
            $tribute = $this->tributeService->getById($id);

            return response()->json([
                'success' => true,
                'message' => 'Tributo encontrado.',
                'data' => $tribute,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el tributo.',
                'errors' => [$e->getMessage()],
            ], 500);
        }
    }

    public function getByFilters(Request $request)
    {
        try {
            $tributes = $this->tributeService->getByFilters($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Tributos encontrados.',
                'data' => $tributes,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los tributos.',
                'errors' => [$e->getMessage()],
            ], 500);
        }
    }
}
