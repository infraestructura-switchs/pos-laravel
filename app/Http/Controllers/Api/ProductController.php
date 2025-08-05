<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ProductService;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function getById(int $id)
    {
        try {
            $product = $this->productService->getById($id);

            return response()->json([
                'success' => true,
                'message' => 'Productos encontrados con éxito.',
                'data' => response()->json($product)->original
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el producto',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function getByFilters(Request $request)
    {
        $filters = $request->only([
            'name', 'reference', 'status', 'has_inventory',
            'sort_field', 'sort_order', 'per_page'
        ]);

        $data = $this->productService->getByFilters($request->all());
        return response()->json($data);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $product = $this->productService->store($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Producto creado con éxito',
                'data' => $product
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error interno',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        try {
            $updatedProduct = $this->productService->update($product, $request->all());

            return response()->json([
                'success' => true,
                'message' => 'Producto actualizado con éxito',
                'data' => $updatedProduct
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error inesperado al actualizar el producto',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function categories()
    {
        $categories = Category::all();

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    public function import(Request $request): JsonResponse
    {
        if (!$this->productService->canImport()) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede importar porque existen facturas o compras registradas.',
            ], 403);
        }

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xlsm|max:30720'
        ]);

        try {
            $this->productService->import($request->file('file'));

            return response()->json([
                'success' => true,
                'message' => 'Productos importados con éxito.'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al importar productos',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function downloadTemplate(): JsonResponse
    {
        try {
            $fileBase64 = $this->productService->downloadTemplate();

            return response()->json([
                'success' => true,
                'message' => 'Plantilla descargada con éxito.',
                'data' => $fileBase64,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la plantilla',
                'errors' => [$e->getMessage()],
            ], 500);
        }
    }

    public function exportExcel(Request $request)
    {
        try {
            $fileBase64 = $this->productService->exportFiltered($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Productos encontrados con éxito.',
                'data' => $fileBase64,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => "Error al obtener el producto, Error: {$e}",
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }
} 