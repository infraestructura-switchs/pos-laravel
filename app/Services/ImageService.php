<?php

namespace App\Services;

use App\Models\Product;
use App\Services\Contracts\CloudinaryClientInterface;
use App\Services\Contracts\ImageServiceInterface;
use Illuminate\Http\UploadedFile;

class ImageService implements ImageServiceInterface
{
    private CloudinaryClientInterface $cloudClient;

    public function __construct(CloudinaryClientInterface $cloudClient)
    {
        $this->cloudClient = $cloudClient;
    }

    public function uploadProductImage(int $productId, UploadedFile $file): array
    {
        if (!$file) {
            return ['success' => false, 'error' => 'error', 'Selecciona una imagen primero'];
        }

        $product = Product::find($productId);
        if (!$product) {
            return ['success' => false, 'error' => 'Producto no existe'];
        }

        if (!$this->isValidImageExtension($file)) {
            return ['success' => false, 'error' => 'Formato inválido. Solo .jpeg, .jpg, .png'];
        }

        $maxBytes = config('filesystems.images_max_bytes', 2 * 1024 * 1024); // 2MB default
        if ($file->getSize() > $maxBytes) {
            return ['success' => false, 'error' => 'El archivo supera el tamaño permitido'];
        }

        $path = $file->getRealPath();
        $publicId = 'product_' . $product->id . '_' . time();

        $result = $this->cloudClient->uploadImage($path, [
                'public_id' => $publicId
            ]);

        if (!($result['success'] ?? false)) {
            return $result;
        }

        $product->cloudinary_public_id = $result['public_id'];
        $product->save();

        // Limpia el archivo temporal generado por Livewire tras subirlo a Cloudinary
        try {
            if (is_string($path) && file_exists($path)) {
                @unlink($path);
            }
        } catch (\Throwable $e) {
            // No detener el flujo si falla el borrado del temporal
        }

        return $result;
    }

    public function deleteProductImage(int $productId): array
    {
        $product = Product::find($productId);
        if (!$product) {
            return ['success' => false, 'error' => 'Producto no existe'];
        }

        if (empty($product->cloudinary_public_id)) {
            return ['success' => true, 'result' => 'no_image'];
        }

        $result = $this->cloudClient->deleteImage($product->cloudinary_public_id);
        if ($result['success'] ?? false) {
            $product->cloudinary_public_id = null;
            $product->save();
        }

        return $result;
    }

    public function getProductImageUrl(int $productId, array $options = []): string
    {
        $product = Product::find($productId);
        if (!$product || empty($product->cloudinary_public_id)) {
            return asset('images/no-product-image.svg');
        }
        return $this->cloudClient->getImageUrl($product->cloudinary_public_id, $options);
    }

    public function getProductThumbnailUrl(int $productId, int $size = 80): string
    {
        $product = Product::find($productId);
        if (!$product || empty($product->cloudinary_public_id)) {
            return asset('images/no-product-image.svg');
        }
        return $this->cloudClient->getThumbnailUrl($product->cloudinary_public_id, $size);
    }

    private function isValidImageExtension(UploadedFile $file): bool
    {
        $allowed = ['jpeg', 'jpg', 'png'];
        $ext = strtolower($file->getClientOriginalExtension());
        if (!in_array($ext, $allowed, true)) {
            return false;
        }
        $mime = $file->getMimeType();
        return in_array($mime, ['image/jpeg', 'image/png'], true);
    }
}

