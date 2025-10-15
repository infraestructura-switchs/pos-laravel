<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function index()
    {
        $warehouses = Warehouse::paginate(15);
        return view('livewire.admin.warehouses.index', compact('warehouses'));
    }

    public function create()
    {
        return view('livewire.admin.warehouses.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'address' => 'nullable|string|max:200',
            'phone' => 'nullable|string|max:20',
        ]);

        Warehouse::create($data);

        return redirect()->route('warehouses.index')->with('success', 'Warehouse created');
    }

    public function show(Warehouse $warehouse)
    {
        return view('livewire.admin.warehouses.show', compact('warehouse'));
    }

    public function edit(Warehouse $warehouse)
    {
        return view('livewire.admin.warehouses.edit', compact('warehouse'));
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'address' => 'nullable|string|max:200',
            'phone' => 'nullable|string|max:20',
        ]);

        $warehouse->update($data);

        return redirect()->route('warehouses.index')->with('success', 'Warehouse updated');
    }

    public function destroy(Warehouse $warehouse)
    {
        $warehouse->delete();
        return redirect()->route('warehouses.index')->with('success', 'Warehouse deleted');
    }
}


