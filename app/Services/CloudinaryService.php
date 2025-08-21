<?php

namespace App\Services;

use App\Services\Contracts\CloudinaryClientInterface;
use Cloudinary\Cloudinary;
use Cloudinary\Transformation\Resize;
use Cloudinary\Transformation\Format;
use Cloudinary\Transformation\Quality;
use Exception;
use Illuminate\Support\Facades\Log;

class CloudinaryService implements CloudinaryClientInterface
{
    protected $cloudinary;

    public function __construct()
    {
        $this->cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => config('cloudinary.cloud_name'),
                'api_key' => config('cloudinary.api_key'),
                'api_secret' => config('cloudinary.api_secret'),
                'secure' => config('cloudinary.secure', true)
            ]
        ]);
    }

    /**
     * Upload an image to Cloudinary
     */
    public function uploadImage($filePath, $options = []): array
    {
        try {
            $defaultOptions = [
                'folder' => config('cloudinary.folder', 'pos-images'),
                'transformation' => [
                    'width' => 800,
                    'height' => 800,
                    'crop' => 'limit',
                    'quality' => 'auto',
                    'format' => 'auto'
                ]
            ];

            $options = array_merge($defaultOptions, $options);

            $result = $this->cloudinary->uploadApi()->upload($filePath, $options);

            return [
                'success' => true,
                'public_id' => $result['public_id'],
                'secure_url' => $result['secure_url'],
                'url' => $result['url']
            ];
        } catch (Exception $e) {
            Log::error('Cloudinary upload error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Delete an image from Cloudinary
     */
    public function deleteImage($publicId): array
    {
        try {
            $result = $this->cloudinary->uploadApi()->destroy($publicId);
            
            return [
                'success' => $result['result'] === 'ok',
                'result' => $result['result']
            ];
        } catch (Exception $e) {
            Log::error('Cloudinary delete error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get optimized image URL
     */
    public function getImageUrl($publicId, $options = []): string
    {
        if (empty($publicId)) {
            return asset('images/no-image.png');
        }

        $defaultOptions = [
            'width' => 200,
            'height' => 200,
            'crop' => 'fill',
            'quality' => 'auto',
            'format' => 'auto'
        ];

        $options = array_merge($defaultOptions, $options);

        return $this->cloudinary->image($publicId)
            ->resize(Resize::fill($options['width'], $options['height']))
            ->format(Format::auto())
            ->quality(Quality::auto())
            ->toUrl();
    }

    /**
     * Get thumbnail URL
     */
    public function getThumbnailUrl($publicId, $size = 80): string
    {
        return $this->getImageUrl($publicId, [
            'width' => $size,
            'height' => $size,
            'crop' => 'fill'
        ]);
    }
}