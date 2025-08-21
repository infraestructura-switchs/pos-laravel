<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\Contracts\ImageServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductImageController extends Controller
{
    private ImageServiceInterface $imageService;

    public function __construct(ImageServiceInterface $imageService)
    {
        $this->imageService = $imageService;
    }

    public function uploadBase64(Request $request)
    {
        $rules = [
            'product_id' => 'required|integer|exists:products,id',
            'file_name' => 'required|string',
            'file_base64' => 'required|string',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors(),
            ], 422);
        }

        $productId = (int) $request->input('product_id');
        $fileName = $request->input('file_name');
        $fileB64 = $request->input('file_base64');

        // Decode base64 and create a temporary UploadedFile
        try {
            if (Str::startsWith($fileB64, 'data:')) {
                [$meta, $data] = explode(',', $fileB64, 2);
                $fileB64 = $data;
            }
            $binary = base64_decode($fileB64, true);
            if ($binary === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Base64 inválido',
                ], 422);
            }

            $tmpPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('img_', true) . '_' . $fileName;
            file_put_contents($tmpPath, $binary);

            $mimeType = mime_content_type($tmpPath) ?: 'application/octet-stream';
            $uploaded = new \Illuminate\Http\UploadedFile(
                $tmpPath,
                $fileName,
                $mimeType,
                null,
                true
            );
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'No se pudo procesar el archivo',
                'errors' => [$e->getMessage()],
            ], 422);
        }

        $result = $this->imageService->uploadProductImage($productId, $uploaded);

        // Clean up temporary file
        try { @unlink($tmpPath); } catch (\Throwable $e) {}

        if (!($result['success'] ?? false)) {
            return response()->json([
                'success' => false,
                'message' => 'Error al subir imagen',
                'errors' => [$result['error'] ?? 'Desconocido'],
            ], 400);
        }

        $product = Product::find($productId);

        return response()->json([
            'success' => true,
            'message' => 'Imagen subida exitosamente',
            'data' => [
                'public_id' => $product->cloudinary_public_id,
            ],
        ]);
    }

    public function show(int $productId)
    {
        $product = Product::find($productId);
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado',
            ], 404);
        }

        $url = $this->imageService->getProductImageUrl($productId, [
            'width' => 400,
            'height' => 400,
            'crop' => 'fit',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Imagen obtenida exitosamente',
            'data' => [ 'url' => $url ],
        ]);
    }

    public function destroy(int $productId)
    {
        $product = Product::find($productId);
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado',
            ], 404);
        }

        $result = $this->imageService->deleteProductImage($productId);
        if (!($result['success'] ?? false)) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar imagen',
                'errors' => [$result['error'] ?? 'Desconocido'],
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Imagen eliminada exitosamente',
        ]);
    }
}

