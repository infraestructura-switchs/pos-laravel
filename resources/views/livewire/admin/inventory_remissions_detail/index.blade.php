<div class="container">

    <x-commons.header>
        <x-wireui.button wire:click="exportRemissionDetails" icon="excel" success text="Exportar a Excel" load textLoad="Exportando..." />
        <x-wireui.button icon="user" x-on:click="$wire.emitTo('admin.inventory-remissions.create', 'openCreate')" text="Crear Detalle" />
    </x-commons.header>

    <x-commons.table-responsive>
        <x-slot:top title="Detalles de Remisión">
        </x-slot:top>

        <x-slot:header>
            <x-wireui.search placeholder="Buscar..." />
            <x-wireui.native-select wire:model.defer="filter" optionKeyValue="true" :options="$filters" />
        </x-slot:header>

        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Costo Unitario</th>
                    <th>Costo Total</th>
                    <th style="width: 150px; text-align: center;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($remissionDetails as $item)
                    <tr wire:key="detail{{ $item->id }}">
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->product->name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ $item->unit_cost }}</td>
                        <td>{{ $item->total_cost }}</td>
                        <td style="text-align: center;">
                            <x-buttons.edit wire:click="$emitTo('admin.inventory-remissions.edit', 'openEdit', {{ $item->id }})" title="Editar" />
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-commons.table-responsive>

    @if ($remissionDetails->hasPages())
        <div class="p-3">
            {{ $remissionDetails->links() }}
        </div>
    @endif

    <livewire:admin.inventory-remissions-detail.create />
    <livewire:admin.inventory-remissions-detail.edit />

</div>
