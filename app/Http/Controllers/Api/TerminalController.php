<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TerminalService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TerminalController extends Controller
{
    protected $service;

    public function __construct(TerminalService $service)
    {
        $this->service = $service;
    }

    public function create(Request $request)
    {
        try {
            $id = $this->service->create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Terminal creada con éxito.',
                'data' => ['id' => $id]
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación.',
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function update(Request $request, int $id)
    {
        try {
            $result = $this->service->update($id, $request->all());

            return response()->json([
                'success' => true,
                'message' => 'Terminal actualizada con éxito.',
                'data' => $result
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terminal no encontrada.'
            ], 404);
        }
    }

    public function getById(int $id)
    {
        try {
            $terminal = $this->service->getById($id);

            return response()->json([
                'success' => true,
                'message' => 'Terminal obtenida con éxito.',
                'data' => $terminal
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terminal no encontrada.'
            ], 404);
        }
    }

    public function getByFilters(Request $request)
    {
        $results = $this->service->getByFilters($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Listado de terminales.',
            'data' => $results
        ]);
    }

    public function verifyTerminal()
    {
        try {
            $this->service->verifyTerminal();

            return response()->json([
                'success' => true,
                'message' => 'Terminal verificada con éxito.',
                'data' => ""
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        }
    }
}
