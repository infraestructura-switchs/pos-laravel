<?php

namespace App\Http\Livewire\Admin\Customers;

use App\Models\Customer;
use Livewire\Component;

class Search extends Component {

    public $customers;

    public function render() {
        
        $this->customers = Customer::where('status', '0')
                        ->select(['id', 'no_identification', 'dv', 'identification_document_id', 'names', 'phone'])
                        ->orderBy('top', 'ASC')
                        ->get()
                        ->map(function ($customer) {
                            $data = $customer->toArray();
                            $data['format_no_identification'] = $customer->formatNoIdentification;
                            return $data;
                        })
                        ->toArray();

        return view('livewire.admin.customers.search');
    }
}
