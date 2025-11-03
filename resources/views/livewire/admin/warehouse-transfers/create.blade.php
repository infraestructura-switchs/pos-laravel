<div>
    <x-wireui.modal wire:model="openCreate" max-width="5xl">
        <x-wireui.card title="Crear Traspaso entre Bodegas">

            <x-wireui.errors />

            <div class="grid gap-6">
                <div class="grid sm:grid-cols-2 gap-6">
                    <x-wireui.native-select
                        label="Bodega Origen"
                        wire:model="origin_warehouse_id"
                        :options="$warehouses"
                        option-key-value 
                        placeholder="Seleccionar Bodega"
                    />

                    <x-wireui.native-select
                        label="Bodega Destino"
                        wire:model="destination_warehouse_id"
                        :options="$warehouses"
                        option-key-value 
                        placeholder="Seleccionar Bodega"
                    />

                    <x-wireui.native-select
                        label="Usuario"
                        wire:model="user_id"
                        :options="$users"
                        option-key-value 
                        placeholder="Seleccionar Usuario"
                    />

                    <x-wireui.input
                        label="Fecha de Traspaso"
                        type="date"
                        wire:model="date"
                        placeholder="Fecha de traslado"
                    />

                    <x-wireui.input
                        label="Descripción"
                        wire:model="description"
                        placeholder="Descripción (opcional)"
                    />

                    <x-wireui.native-select
                        label="Estado"
                        wire:model="status"
                        :options="['pending' => 'Pendiente', 'completed' => 'Completado', 'cancelled' => 'Cancelado']"
                        option-key-value
                        placeholder="Seleccionar Estado"
                    />
                </div>

                <div>
                    <h3 class="text-lg font-semibold mb-2">Detalles de Productos</h3>
                    <table class="table w-full">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Costo Unitario</th>
                                <th>Costo Total</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($details as $index => $detail)
                                <tr>
                                    <td>
                                        <x-wireui.native-select
                                            wire:model="details.{{ $index }}.product_id"
                                            :options="$products"
                                            option-key-value 
                                            placeholder="Seleccionar Producto"
                                        />
                                    </td>
                                    <td>
                                        <x-wireui.input
                                            type="number"
                                            wire:model.live="details.{{ $index }}.quantity"
                                            placeholder="Cantidad"
                                            step="0.01"
                                            min="0"
                                        />
                                    </td>
                                    <td>
                                        <x-wireui.input
                                            type="number"
                                            wire:model.live="details.{{ $index }}.unit_cost"
                                            placeholder="Costo Unitario"
                                            step="0.01"
                                            min="0"
                                        />
                                    </td>
                                    <td>
                                        <x-wireui.input
                                            type="number"
                                            wire:model="details.{{ $index }}.total_cost"
                                            placeholder="Costo Total"
                                            readonly
                                            step="0.01"
                                        />
                                    </td>
                                    <td>
                                        <x-wireui.button wire:click="removeDetail({{ $index }})" danger text="Eliminar" />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <x-wireui.button wire:click="addDetail" secondary text="Añadir Producto" class="mt-2" />
                </div>
            </div>

            <x-slot:footer>
                <div class="text-right space-x-3">
                    <x-wireui.button secondary wire:click="closeCreate" text="Cerrar" />
                    <x-wireui.button wire:click="store" text="Guardar" load textLoad="Guardando..." />
                </div>
            </x-slot:footer>

        </x-wireui.card>
    </x-wireui.modal>
</div>