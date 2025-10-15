<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CategoryService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function store(Request $request)
    {
        try {
            $category = $this->categoryService->create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Categoría creada con éxito.',
                'data' => $category,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la categoría.',
                'errors' => [$e->getMessage()],
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $category = $this->categoryService->update($id, $request->all());

            return response()->json([
                'success' => true,
                'message' => 'Categoría actualizada con éxito.',
                'data' => $category,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la categoría.',
                'errors' => [$e->getMessage()],
            ], 500);
        }
    }

    public function getById($id)
    {
        try {
            $category = $this->categoryService->getById($id);

            return response()->json([
                'success' => true,
                'message' => 'Categoría encontrada.',
                'data' => $category,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la categoría.',
                'errors' => [$e->getMessage()],
            ], 404);
        }
    }

    public function getByFilters(Request $request)
    {
        try {
            $categories = $this->categoryService->getByFilters($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Categorías encontradas.',
                'data' => $categories,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las categorías.',
                'errors' => [$e->getMessage()],
            ], 500);
        }
    }
}
