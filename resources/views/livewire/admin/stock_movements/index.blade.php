<div class="container mt-8">

    <x-commons.header>
        <x-wireui.button wire:click="exportStockMovements" icon="excel" success text="Exportar Movimientos a Excel" load
            textLoad="Exportando..." />
        <x-wireui.button icon="user" x-on:click="$wire.emitTo('admin.stock-movements.create', 'openCreate')"
            text="Crear Movimiento" />
    </x-commons.header>

    <x-commons.table-responsive>
        <x-slot:top title="Movimientos de Stock">
        </x-slot:top>

        <x-slot:header>
            <x-wireui.input wire:model.live="search" placeholder="Buscar..." />
            <x-wireui.native-select wire:model.defer="filter" :options="$filters" />
        </x-slot:header>

        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Bodega</th>
                    <th>Usuario</th>
                    <th>Fecha</th>
                    <th style="width: 150px; text-align: center;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($stockMovements as $item)
                    <tr wire:key="movement{{ $item->id }}" wire:click="selectStockMovement({{ $item->id }})"
                        class="{{ $selectedStockMovementId == $item->id ? 'bg-blue-100' : '' }}" style="cursor: pointer;">
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->warehouse->name ?? 'N/A' }}</td>
                        <td>{{ $item->user->name ?? 'N/A' }}</td>
                        <td>{{ $item->stock_movements_date?->format('Y-m-d') ?? 'N/A' }}</td>
                        <td style="text-align: center;">
                            <x-buttons.edit wire:click="editStockMovement({{ $item->id }})" title="Editar" />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">No hay movimientos disponibles.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-commons.table-responsive>

    @if ($stockMovements->hasPages())
        <div class="p-3">
            {{ $stockMovements->links() }}
        </div>
    @endif

    <div class="my-6">
        <x-commons.table-responsive>
            <x-slot:top title="Detalles de Movimiento">
            </x-slot:top>

            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Producto</th>
                        <th>Tipo de Ajuste</th>
                        <th>Cantidad</th>
                        <th>Costo Unitario</th>
                        <th>Costo Total</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($selectedStockMovementId && $movementDetails->isNotEmpty())
                        @foreach ($movementDetails as $item)
                            <tr wire:key="detail{{ $item->id }}">
                                <td>{{ $item->id }}</td>
                                <td>{{ isset($item->product->name) ? $item->product->name : 'N/A' }}</td>
                                <td>{{ $item->movement_type == 'IN' ? 'Incremento' : 'Disminución' }}</td>
                                <td>{{ number_format($item->quantity, 2) }}</td>
                                <td>{{ number_format($item->unit_cost, 2) }}</td>
                                <td>{{ number_format($item->total_cost, 2) }}</td>
                            </tr>
                        @endforeach

                    @else
                        <tr>
                            <td colspan="6" style="text-align: center;">No hay detalles disponibles. Selecciona un
                                movimiento para ver sus detalles.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </x-commons.table-responsive>

        @if ($movementDetails->hasPages())
            <div class="p-3">
                {{ $movementDetails->links() }}
            </div>
        @endif

    </div>

    <livewire:admin.stock-movements.create />
    <livewire:admin.stock-movements.edit />
</div>