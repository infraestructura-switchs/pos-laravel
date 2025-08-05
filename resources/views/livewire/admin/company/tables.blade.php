<div>
    <x-wireui.card title="Configuración de Mesas">
        <x-wireui.errors />

        <div class="space-y-6">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-wireui.input 
                        label="Número de mesas" 
                        wire:model.defer="numberOfTables" 
                        type="number" 
                        min="1" 
                        max="100" 
                    />
                    <p class="text-sm text-gray-500 mt-1">
                        Especifica cuántas mesas quieres tener disponibles
                    </p>
                </div>

                <div>
                    <x-wireui.input 
                        label="Prefijo de mesas" 
                        wire:model.defer="tablePrefix" 
                        placeholder="Mesa"
                    />
                    <p class="text-sm text-gray-500 mt-1">
                        Ejemplo: "Mesa 1", "Table 1", "Mesa A1"
                    </p>
                </div>
            </div>

            <div class="bg-blue-50 p-4 rounded-lg">
                <h4 class="font-semibold text-blue-800 mb-2">Información:</h4>
                <ul class="text-sm text-blue-700 space-y-1">
                    <li>• Las mesas ocupadas no se eliminarán automáticamente</li>
                    <li>• Solo se eliminarán las mesas disponibles (vacías)</li>
                    <li>• Si reduces el número de mesas, se eliminarán desde la última disponible</li>
                    <li>• El prefijo se aplicará a todas las mesas existentes</li>
                </ul>
            </div>
        </div>

        <x-slot:footer>
            <div class="text-right">
                <x-wireui.button 
                    wire:click="updateTables" 
                    text="Actualizar Mesas" 
                    load 
                    textLoad="Actualizando..." 
                />
            </div>
        </x-slot:footer>
    </x-wireui.card>
</div>