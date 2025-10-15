<div class="container">

    <x-commons.header>
        <x-wireui.button wire:click="exportProducts" icon="excel" success text="Exportar a excel" load textLoad="Exportando..." />
        <x-wireui.button icon="excel" x-on:click="$wire.emitTo('admin.products.import', 'openImport')"  success text="Importar desde excel" />
        <x-wireui.button icon="inventory" x-on:click="$wire.emitTo('admin.products.create', 'openCreate')"  text="Crear producto" />
    </x-commons.header>

    <x-commons.table-responsive>

        <x-slot:top title="Productos">
            <x-commons.tag tooltip="Costo del inventario" label="Costos" :value="formatToCop($totalCost)"/>
        </x-slot:top>

        <x-slot:header>
            <x-wireui.search placeholder="Buscar..." />
            <x-wireui.native-select wire:model.defer="filter" optionKeyValue="true" :options="$filters" />
        </x-slot:header>

        <table class="table">
            <thead >
                <tr>
                    <th>
                        Imagen
                    </th>
                    <th left>
                        Código barras
                    </th>
                    <th left>
                        Referencia
                    </th>
                    <th left>
                        Nombre
                    </th>
                    <th left>
                        Categoría
                    </th>
                    <th>
                        Impuestos(%)
                    </th>
                    <th left>
                        Costo
                    </th>
                    <th left>
                        Precio
                    </th>
                    <th>
                        Stock
                    </th>
                    <th>
                        Estado
                    </th>
                    <th>
                        Acciones
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $item)
                    <tr wire:key="product-{{ $item->id }}">
                        <td class="w-16">
                            <img src="{{ $item->image_url }}" 
                                 alt="{{ $item->name }}" 
                                 class="h-12 w-12 object-cover rounded-lg">
                        </td>
                        <td left class="{{ !$item->top ? 'text-green-500 font-bold' : ''  }}">
                            {{ $item->barcode }}
                        </td>
                        <td left class="{{ !$item->top ? 'text-green-500 font-bold' : ''  }}">
                            {{ $item->reference }}
                        </td>
                        <td left>
                            {{ $item->name }}
                        </td>
                        <td left>
                            {{ $item->category?->name ?? '-' }}
                        </td>
                        <td>
                            @if($item->taxRates->count() > 0)
                                {{ $item->taxRates->pluck('format_rate')->implode(', ') }}
                            @else
                                -
                            @endif
                        </td>
                        <td left>
                            @formatToCop($item->cost)
                        </td>
                        <td left>
                            @formatToCop($item->price)
                        </td>
                        <td>
                            {{ $item->stockUnitsLabel }}
                        </td>
                        <td>
                            <x-commons.status :status="$item->status" inactive="desactivado" />
                        </td>
                        <td actions>
                            <x-buttons.edit wire:click="$emitTo('admin.products.edit', 'openEdit', {{ $item->id }})" title="Editar" />
                        </td>
                    </tr>
                @empty
                    <x-commons.table-empty />
                @endforelse
            <tbody>
        </table>
    </x-commons.table-responsive>

    @if ($products->hasPages())
        <div class="p-3">
            {{ $products->links() }}
        </div>
    @endif

    <livewire:admin.products.create>
    <livewire:admin.products.edit>
    <livewire:admin.products.presentations>
    <livewire:admin.products.import>
    <livewire:admin.categories.index>
    <livewire:admin.products.tax-rates>

</div>

@push('js')

    <script>
        document.addEventListener('livewire:load', function () {

            Livewire.on('deleteProduct', id => {
                Swal.fire({
                    title: '¿Estas seguro?',
                    text: '¿Quires eliminar este producto?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, aceptar',
                    cancelButtonText: 'Cancelar',
                    showLoaderOnConfirm: true,
                    preConfirm: function() {
                        return @this.delete(id);
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                });
            });
        });
    </script>
@endpush


