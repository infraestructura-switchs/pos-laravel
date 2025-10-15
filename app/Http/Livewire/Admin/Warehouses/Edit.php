<?php

namespace App\Http\Livewire\Admin\Warehouses;

use App\Models\Warehouse;
use Livewire\Component;
use App\Rules\Phone;
use Illuminate\Validation\Rule;
use App\Traits\LivewireTrait;
use Illuminate\Support\Arr;

class Edit extends Component {

    use LivewireTrait;

    protected $listeners = ['openEdit'];

    public $openEdit=false, $types;

    public $warehouse;


    protected function rules(){
        return [
            'warehouse.name' => 'required|string|min:5|max:250',
            'warehouse.address' => 'nullable|string|max:250',
            'warehouse.phone' => ['nullable', 'string', new Phone]
        ];
    }

    public function render() {
        return view('livewire.admin.warehouses.edit');
    }

        public function closeEdit()
{
    $this->openEdit = false;
}


    public function openEdit(Warehouse $warehouse){
        $this->warehouse = $warehouse;
        $this->resetValidation();
        $this->openEdit=true;
    }

    public function update(){

        $this->validate();
        $this->warehouse->save();

        $this->emit('success', 'Bodega actualizado con éxito');
        $this->emitTo('admin.warehouses.index', 'render');

        $this->resetExcept('types');

        $this->warehouse = new Warehouse();

        $this->openEdit = false;
    }
}
