<div class="container mt-8">

    <x-commons.header>
        <x-wireui.button wire:click="exportTransfers" icon="excel" success text="Exportar Traspasos a Excel" load
            textLoad="Exportando..." />
        <x-wireui.button icon="truck" x-on:click="$wire.emitTo('admin.warehouse-transfers.create', 'openCreate')" text="Crear Traspaso" />
    </x-commons.header>

    <x-commons.table-responsive>
        <x-slot:top title="Traspasos entre Bodegas">
        </x-slot:top>

        <x-slot:header>
            <x-wireui.input wire:model.live="search" placeholder="Buscar traspasos..." />
            <x-wireui.native-select wire:model.defer="filter" :options="$filters" />
        </x-slot:header>

        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Bodega Origen</th>
                    <th>Bodega Destino</th>
                    <th>Usuario</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th style="width: 150px; text-align: center;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transfers as $item)
                    <tr wire:key="transfer{{ $item->id }}" wire:click="selectTransfer({{ $item->id }})"
                        class="{{ $selectedTransferId == $item->id ? 'bg-blue-100' : '' }}" style="cursor: pointer;">
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->originWarehouse->name ?? 'N/A' }}</td>
                        <td>{{ $item->destinationWarehouse->name ?? 'N/A' }}</td>
                        <td>{{ $item->user->name ?? 'N/A' }}</td>
                        <td>{{ $item->transfer_date?->format('Y-m-d') ?? 'N/A' }}</td>
                        <td>
                            <span class="px-2 py-1 rounded text-xs
                                @if($item->status === 'completed') bg-green-100 text-green-800
                                @elseif($item->status === 'pending') bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ ucfirst(__($item->status)) }}
                            </span>
                        </td>
                        <td style="text-align: center;">
                            <x-buttons.edit wire:click="editTransfer({{ $item->id }})" title="Editar" />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">No hay traspasos disponibles.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-commons.table-responsive>

    @if ($transfers->hasPages())
        <div class="p-3">
            {{ $transfers->links() }}
        </div>
    @endif

    <div class="my-6">
        <x-commons.table-responsive>
            <x-slot:top title="Detalles del Traspaso">
            </x-slot:top>

            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Costo Unitario</th>
                        <th>Costo Total</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($selectedTransferId && $transferDetails && $transferDetails->isNotEmpty())
                        @foreach ($transferDetails as $item)
                            <tr wire:key="detail{{ $item->id }}">
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->product->name ?? 'N/A' }}</td>
                                <td>{{ number_format($item->quantity, 2) }}</td>
                                <td>{{ number_format($item->unit_cost, 2) }}</td>
                                <td>{{ number_format($item->total_cost, 2) }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5" style="text-align: center;">No hay detalles disponibles. Selecciona un traspaso para ver sus detalles.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </x-commons.table-responsive>

        

    </div>

    <livewire:admin.warehouse-transfers.create />
    <livewire:admin.warehouse-transfers.edit />
</div>