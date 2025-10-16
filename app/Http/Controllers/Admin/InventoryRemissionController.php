<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryRemission;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class InventoryRemissionController extends Controller
{
    public function index()
    {
        $remissions = InventoryRemission::with('warehouse', 'user')->paginate(15);
        return view('livewire.admin.inventory_remissions.index', compact('remissions'));
    }

    public function create()
    {
        $warehouses = Warehouse::all();
        return view('livewire.admin.inventory_remissions.create', compact('warehouses'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,warehouse_id',
            'user_id' => 'required|exists:users,id',
            'folio' => 'required|string|max:20',
            'remission_date' => 'required|date',
            'concept' => 'required|string|max:50',
            'note' => 'nullable|string|max:200',
        ]);

        InventoryRemission::create($data);

        return redirect()->route('admin.inventory-remissions.index')->with('success', 'Remission created');
    }

    public function show(InventoryRemission $inventoryRemission)
    {
        $inventoryRemission->load('warehouse', 'user');
        return view('livewire.admin.inventory_remissions.show', ['remission' => $inventoryRemission]);
    }

    public function edit(InventoryRemission $inventoryRemission)
    {
        $warehouses = Warehouse::all();
        return view('livewire.admin.inventory_remissions.edit', compact('inventoryRemission', 'warehouses'));
    }

    public function update(Request $request, InventoryRemission $inventoryRemission)
    {
        $data = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,warehouse_id',
            'user_id' => 'required|exists:users,id',
            'folio' => 'required|string|max:20',
            'remission_date' => 'required|date',
            'concept' => 'required|string|max:50',
            'note' => 'nullable|string|max:200',
        ]);

        $inventoryRemission->update($data);

        return redirect()->route('admin.inventory-remissions.index')->with('success', 'Remission updated');
    }

    public function destroy(InventoryRemission $inventoryRemission)
    {
        $inventoryRemission->delete();
        return redirect()->route('admin.inventory-remissions.index')->with('success', 'Remission deleted');
    }
}


