<div class="container">
    <x-commons.header>
        <x-wireui.button wire:click="exportInventoryRemissions" icon="excel" success text="Exportar Remisiones a Excel" load textLoad="Exportando..." />
        <x-wireui.button icon="user" x-on:click="$wire.emitTo('admin.inventory-remissions.create', 'openCreate')" text="Crear Remisión" />
    </x-commons.header>
    <x-commons.table-responsive>
        <x-slot:top title="Remisiones">
        </x-slot:top>
        <x-slot:header>
            <x-wireui.input wire:model.live="search" placeholder="Buscar..." />
            <x-wireui.native-select wire:model.defer="filter" optionKeyValue="true" :options="$filters" />
        </x-slot:header>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Folio</th>
                    <th>Bodega</th>
                    <th>Usuario</th>
                    <th>Fecha</th>
                    <th style="width: 150px; text-align: center;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($inventoryRemissions as $item)
                    <tr wire:key="remission{{ $item->id }}" wire:click="selectRemission({{ $item->id }})" class="{{ $selectedRemissionId == $item->id ? 'bg-blue-100' : '' }}" style="cursor: pointer;">
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->folio }}</td>
                        <td>{{ $item->warehouse->name ?? 'N/A' }}</td>
                        <td>{{ $item->user->name ?? 'N/A' }}</td>
                        <td>{{ $item->remission_date?->format('Y-m-d') ?? 'N/A' }}</td>
                        <td style="text-align: center;">
                            <x-buttons.download wire:click="downloadPdf({{ $item->id }})" icon="download" square outline secondary title="Descargar PDF" />
                            <x-buttons.edit wire:click="editRemission({{ $item->id }})" title="Editar" />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">No hay remisiones disponibles.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-commons.table-responsive>
    @if ($inventoryRemissions->hasPages())
        <div class="p-3">
            {{ $inventoryRemissions->links() }}
        </div>
    @endif
    <div class="my-6">
        <x-commons.table-responsive>
            <x-slot:top title="Detalles de Remisión">
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
                    @if ($selectedRemissionId && $remissionDetails->isNotEmpty())
                        @foreach ($remissionDetails as $item)
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
                            <td colspan="5" style="text-align: center;">No hay detalles disponibles. Selecciona una remisión para ver sus detalles.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </x-commons.table-responsive>
    </div>
    @if ($remissionDetails instanceof \Illuminate\Pagination\AbstractPaginator && $remissionDetails->hasPages())
        <div class="p-3">
            {{ $remissionDetails->links() }}
        </div>
    @endif
    <livewire:admin.inventory-remissions.create />
    <livewire:admin.inventory-remissions.edit />
</div>