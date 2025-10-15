<?php

namespace App\Services\Contracts;

use Illuminate\Http\UploadedFile;

interface ImageServiceInterface
{
    public function uploadProductImage(int $productId, UploadedFile $file): array;
    public function deleteProductImage(int $productId): array;
    public function getProductImageUrl(int $productId, array $options = []): string;
    public function getProductThumbnailUrl(int $productId, int $size = 80): string;
}

