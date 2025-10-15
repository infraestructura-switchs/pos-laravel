<div>
    <x-wireui.modal wire:model.defer="openCreate" max-width="3xl">
        <x-wireui.card title="Crear Bodega">

            <x-wireui.errors />

            <div class="grid sm:grid-cols-2 gap-6">
                <x-wireui.input label="Nombre" wire:model.defer="name" placeholder="Nombre de la bodega" />

                <x-wireui.input label="Dirección" wire:model.defer="address" placeholder="Dirección de la bodega" />

                <x-wireui.input onlyNumbers label="Teléfono" wire:model.defer="phone" placeholder="Teléfono de la bodega" />
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
