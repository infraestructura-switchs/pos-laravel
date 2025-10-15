<div>
    <x-wireui.modal wire:model="openEdit" max-width="5xl">
        <x-wireui.card title="Actualizar Movimiento de Stock con Detalles">

            <x-wireui.errors />

            <div class="grid gap-6">
                <div class="grid sm:grid-cols-2 gap-6">
                    <x-wireui.native-select 
                        label="Bodega" 
                        wire:model="warehouse_id" 
                        :options="$warehouses" 
                        option-key-value
                        placeholder="Seleccionar Bodega" 
                    />

                    <x-wireui.native-select 
                        label="Remisión (Opcional)" 
                        wire:model="remission_id" 
                        :options="$remissions" 
                        option-key-value
                        placeholder="Seleccionar Remisión" 
                    />

                    <x-wireui.native-select 
                        label="Usuario (Opcional)" 
                        wire:model="user_id" 
                        :options="$users" 
                        option-key-value
                        placeholder="Seleccionar Usuario" 
                    />

                    <x-wireui.input 
                        label="Fecha de Movimiento" 
                        type="date" 
                        wire:model="stock_movements_date" 
                        placeholder="Fecha de movimiento" 
                    />
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-2">Detalles de Productos</h3>
                    <table class="table w-full">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Tipo de Ajuste</th>
                                <th>Cantidad</th>
                                <th>Costo Unitario</th>
                                <th>Costo Total</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($details as $index => $detail)
                                <tr>
                                    <td>
                                        <x-wireui.native-select 
                                            wire:model="details.{{ $index }}.product_id" 
                                            :options="$products" 
                                            option-key-value
                                            placeholder="Seleccionar Producto" 
                                            x-data
                                            x-on:change="document.getElementById('current-stock-{{ $index }}').textContent = $event.target.options[$event.target.selectedIndex].dataset.stock || 'N/A'"
                                        />
                        
                                    </td>
                                    <td>
                                        <x-wireui.native-select 
                                            wire:model="details.{{ $index }}.adjustment_type" 
                                            :options="['increment' => 'Incremento', 'decrement' => 'Disminución']" 
                                            option-key-value
                                            placeholder="Seleccionar Tipo" 
                                        />
                                    </td>
                                    <td>
                                        <x-wireui.input 
                                            type="number" 
                                            wire:model.live="details.{{ $index }}.quantity" 
                                            placeholder="Cantidad" 
                                            step="0.01" 
                                            min="0"
                                            value="{{ $detail['quantity'] ?? '' }}"
                                        />
                                    </td>
                                    <td>
                                        <x-wireui.input 
                                            type="number" 
                                            wire:model.live="details.{{ $index }}.unit_cost" 
                                            placeholder="Costo Unitario" 
                                            step="0.01" 
                                            min="0"
                                            value="{{ $detail['unit_cost'] ?? '' }}"
                                        />
                                    </td>
                                    <td>
                                        <x-wireui.input 
                                            type="number" 
                                            wire:model="details.{{ $index }}.total_cost" 
                                            placeholder="Costo Total" 
                                            readonly 
                                            step="0.01" 
                                            value="{{ number_format($detail['total_cost'] ?? 0, 2) }}"
                                        />
                                    </td>
                                    <td>
                                        <x-wireui.button wire:click="removeDetail({{ $index }})" danger text="Eliminar" />
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">No hay detalles agregados. Usa el botón para añadir uno.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <x-wireui.button wire:click="addDetail" secondary text="Añadir Producto" class="mt-2" />
                </div>
            </div>

            <x-slot:footer>
                <div class="text-right space-x-3">
                    <x-wireui.button secondary wire:click="closeEdit" text="Cerrar" />
                    <x-wireui.button wire:click="update" text="Actualizar" load textLoad="Actualizando..." />
                </div>
            </x-slot:footer>

        </x-wireui.card>
    </x-wireui.modal>

    <script>
        document.addEventListener('livewire:load', function () {
        });
    </script>
</div>