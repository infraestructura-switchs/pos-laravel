<?php

namespace App\Http\Livewire\Admin\QuickSale;

use App\Models\Customer;
use Livewire\Component;

class Customers extends Component
{

    public $customers;

    public $openModal=false;

    public function render()
    {
        // Cachear clientes por 5 minutos para evitar consultas repetidas
        $this->customers = \Cache::remember('customers_list_' . tenant('id'), 300, function() {
            return Customer::where('status', '0')
                ->select(['id', 'no_identification', 'names', 'phone'])
                ->orderBy('top', 'ASC')
                ->get()
                ->toArray();
        });

        return view('livewire.admin.quick-sale.customers');
    }
}
