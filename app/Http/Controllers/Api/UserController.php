<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    public function create(Request $request)
    {
        try {
            $id = $this->service->create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Usuario creado con éxito.',
                'data' => $id
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el usuario.',
                'errors' => [$e->getMessage()]
            ], 422);
        }
    }

    public function update(Request $request, int $id)
    {
        try {
            $this->service->update($id, $request->all());

            return response()->json([
                'success' => true,
                'message' => 'Usuario actualizado con éxito.',
                'data' => true
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el usuario.',
                'errors' => [$e->getMessage()]
            ], 422);
        }
    }

    public function getById(int $id)
    {
        try {
            $user = $this->service->getById($id);

            return response()->json([
                'success' => true,
                'message' => 'Usuario encontrado con éxito.',
                'data' => $user
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el usuario.',
                'errors' => [$e->getMessage()]
            ], 404);
        }
    }

    public function getByFilters(Request $request)
    {
        try {
            $users = $this->service->getByFilters($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Usuarios encontrados con éxito.',
                'data' => $users
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los usuarios.',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }
}
