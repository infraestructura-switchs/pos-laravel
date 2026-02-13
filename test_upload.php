<?php

require 'vendor/autoload.php';

// Bootstrap Laravel
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\CloudinaryService;
use App\Models\Product;

echo "🚀 Probando subida a Cloudinary...\n";

try {
    $cloudinaryService = new CloudinaryService();
    
    // Usar la imagen placeholder que ya existe
    $imagePath = public_path('images/no-product-image.svg');
    
    if (!file_exists($imagePath)) {
        echo "❌ No se encontró la imagen de prueba en: $imagePath\n";
        exit;
    }
    
    echo "📤 Subiendo imagen de prueba...\n";
    $result = $cloudinaryService->uploadImage($imagePath, [
        'public_id' => 'test_product_' . time(),
        'folder' => 'pos-images'
    ]);
    
    if ($result['success']) {
        echo "✅ ¡Imagen subida exitosamente!\n";
        echo "🔗 Public ID: " . $result['public_id'] . "\n";
        echo "🌐 URL: " . $result['secure_url'] . "\n";
        
        // Obtener URL optimizada
        $optimizedUrl = $cloudinaryService->getThumbnailUrl($result['public_id'], 150);
        echo "🖼️ URL optimizada: " . $optimizedUrl . "\n";
        
    } else {
        echo "❌ Error al subir imagen: " . $result['error'] . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}