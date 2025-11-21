<?php

namespace App\Observers;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class ProductObserver
{
    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        $this->clearCache();
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        $this->clearCache();
    }

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product): void
    {
        $this->clearCache();
    }

    /**
     * Limpiar caché de productos
     */
    private function clearCache(): void
    {
        try {
            Cache::forget('products_list_' . tenant('id'));
            Cache::forget('categories_list_' . tenant('id'));
            Cache::forget('presentations_list_' . tenant('id'));
        } catch (\Exception $e) {
            // Silenciar errores de caché
        }
    }
}
