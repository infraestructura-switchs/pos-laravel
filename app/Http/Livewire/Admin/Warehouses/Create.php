<?php

namespace App\Http\Livewire\Admin\Warehouses;

use App\Models\Warehouse;
use App\Rules\Phone;
use App\Traits\LivewireTrait;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Create extends Component {

    use LivewireTrait;

    protected $listeners = ['openCreate'];

    public $openCreate=false, $types;

    public  $name, $address, $phone;

    public function render() {
        return view('livewire.admin.warehouses.create');
    }

    public function openCreate(){
        $this->resetValidation();
        $this->openCreate = true;
    }

        public function closeCreate()
{
    $this->openCreate = false;
}
    public function store(){
        $rules = [
            'name' => 'required|string|min:5|max:250',
            'address' => 'nullable|string|max:250',
            'phone' => ['nullable', 'string', new Phone]
        ];

        $this->applyTrim([ 'name', 'address', 'phone']);

 $data = $this->validate($rules);
        Warehouse::create($data);

        $this->emit('success', 'Bodega creado con éxito');
        $this->emitTo('admin.warehouses.index', 'render');

        $this->reset();

        $this->openCreate = false;

    }
}
