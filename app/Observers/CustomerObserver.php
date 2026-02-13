<?php

namespace App\Observers;

use App\Models\Customer;
use Illuminate\Support\Facades\Cache;

class CustomerObserver
{
    /**
     * Handle the Customer "created" event.
     */
    public function created(Customer $customer): void
    {
        $this->clearCache();
    }

    /**
     * Handle the Customer "updated" event.
     */
    public function updated(Customer $customer): void
    {
        $this->clearCache();
    }

    /**
     * Handle the Customer "deleted" event.
     */
    public function deleted(Customer $customer): void
    {
        $this->clearCache();
    }

    /**
     * Limpiar caché de clientes
     */
    private function clearCache(): void
    {
        try {
            Cache::forget('customers_list_' . tenant('id'));
            Cache::forget('default_customer_' . tenant('id'));
        } catch (\Exception $e) {
            // Silenciar errores de caché
        }
    }
}

