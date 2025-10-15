<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CustomerService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CustomerController extends Controller
{
    protected CustomerService $service;

    public function __construct(CustomerService $service)
    {
        $this->service = $service;
    }

    public function create(Request $request)
    {
        try {
            $id = $this->service->store($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Cliente creado con éxito.',
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
                'message' => 'Cliente actualizado con éxito.',
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
                'message' => 'Cliente no encontrado.',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function getById(int $id)
    {
        try {
            $customer = $this->service->getById($id);

            return response()->json([
                'success' => true,
                'message' => 'Cliente obtenido con éxito.',
                'data' => $customer
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cliente no encontrado.',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function getByFilters(Request $request)
    {
        $results = $this->service->getByFilters($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Listado de clientes.',
            'data' => $results
        ]);
    }
}
