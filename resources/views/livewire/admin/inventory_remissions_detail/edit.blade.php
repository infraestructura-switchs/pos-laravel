<div>
    <x-wireui.modal wire:model.defer="openEdit" max-width="3xl">
        <x-wireui.card title="Actualizar Detalle de Remisión">

            <x-wireui.errors />

            <div class="grid sm:grid-cols-2 gap-6">
                <x-wireui.native-select label="Producto" wire:model="product_id" :options="$products" option-key-value
                    placeholder="Seleccionar Producto" />

                <x-wireui.input label="Cantidad" type="number" wire:model="quantity"
                    placeholder="Cantidad del producto" />

                <x-wireui.input label="Costo Unitario" type="number" wire:model="unit_cost"
                    placeholder="Costo unitario" />

                <x-wireui.input label="Costo Total" type="number" wire:model="total_cost" placeholder="Costo total"
                    readonly />
            </div>

            <x-slot:footer>
                <div class="text-right space-x-3">
                    <x-wireui.button secondary wire:click="closeEdit" text="Cerrar" />

                    <x-wireui.button wire:click="update" text="Actualizar" load textLoad="Actualizando..." />
                </div>
            </x-slot:footer>
        </x-wireui.card>
    </x-wireui.modal>
</div>