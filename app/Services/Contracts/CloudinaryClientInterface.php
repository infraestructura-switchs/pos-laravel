<?php

namespace App\Services\Contracts;

interface CloudinaryClientInterface
{
    public function uploadImage(string $filePath, array $options = []): array;
    public function deleteImage(string $publicId): array;
    public function getImageUrl(string $publicId, array $options = []): string;
    public function getThumbnailUrl(string $publicId, int $size = 80): string;
}

