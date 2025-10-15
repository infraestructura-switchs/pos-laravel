<div>
  <x-wireui.modal wire:model.defer="openCreate" max-width="2xl">
    <x-wireui.card title="Apertura de Caja" class="!pt-1">

      <x-wireui.errors />

      <div class="flex items-end justify-end mb-4">
        <span class="mr-1 font-semibold">Terminal: </span>
        <span class="text-sm font-semibold">{{ $this->getTerminal()->name ?? 'No asignada' }}</span>
      </div>

      {{-- Información del estado actual --}}
      @if($this->cashStatus)
        <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
          <div class="flex items-center">
            <i class="ti ti-alert-triangle text-yellow-600 mr-2"></i>
            <span class="text-yellow-800 font-medium">
              Ya existe una caja abierta para esta terminal desde {{ $this->cashStatus->opened_at->format('d/m/Y H:i') }}
            </span>
          </div>
        </div>
      @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          {{-- Columna izquierda: Formulario --}}
          <section>
            <p class="text-center font-semibold uppercase text-gray-700 mb-4">
              Dinero inicial
            </p>

            <div class="space-y-4">
              <x-wireui.input 
                label="Efectivo inicial"
                wire:model.lazy="initial_cash"
                onlyNumbers 
                placeholder="0"
                prefix="$"
              />

              <x-wireui.input 
                label="Monedas iniciales (opcional)"
                wire:model.lazy="initial_coins"
                onlyNumbers 
                placeholder="0"
                prefix="$"
              />

              <x-wireui.input 
                label="Tarjeta crédito (opcional)"
                wire:model.lazy="tarjeta_credito"
                onlyNumbers 
                placeholder="0"
                prefix="$"
              />

              <x-wireui.input 
                label="Tarjeta débito (opcional)"
                wire:model.lazy="tarjeta_debito"
                onlyNumbers 
                placeholder="0"
                prefix="$"
              />

              <x-wireui.input 
                label="Cheques (opcional)"
                wire:model.lazy="cheques"
                onlyNumbers 
                placeholder="0"
                prefix="$"
              />

              <x-wireui.input 
                label="Otros métodos (opcional)"
                wire:model.lazy="otros"
                onlyNumbers 
                placeholder="0"
                prefix="$"
              />

              <div class="p-3 bg-blue-50 rounded-lg border border-blue-200">
                <div class="flex justify-between items-center">
                  <span class="font-semibold text-blue-800">Total inicial:</span>
                  <span class="text-lg font-bold text-blue-900">
                    ${{ number_format($total_initial, 0) }}
                  </span>
                </div>
              </div>

              <x-wireui.textarea 
                label="Observaciones (opcional)"
                wire:model.defer="observations"
                rows="3"
                placeholder="Notas sobre la apertura de caja..."
              />
            </div>
          </section>

          {{-- Columna derecha: Información --}}
          <section class="bg-gray-50 p-4 rounded-lg">
            <p class="text-center font-semibold uppercase text-gray-700 mb-4">
              Información
            </p>

            <ul class="space-y-3 text-sm">
              <li class="flex justify-between">
                <span class="text-gray-600">Fecha:</span>
                <span class="font-medium">{{ now()->format('d/m/Y') }}</span>
              </li>
              <li class="flex justify-between">
                <span class="text-gray-600">Hora:</span>
                <span class="font-medium">{{ now()->format('H:i:s') }}</span>
              </li>
              <li class="flex justify-between">
                <span class="text-gray-600">Usuario:</span>
                <span class="font-medium">{{ auth()->user()->name }}</span>
              </li>
              <li class="flex justify-between">
                <span class="text-gray-600">Terminal:</span>
                <span class="font-medium">{{ $this->getTerminal()->name ?? 'No asignada' }}</span>
              </li>
            </ul>

            <div class="mt-6 p-3 bg-green-50 border border-green-200 rounded">
              <h4 class="font-semibold text-green-800 mb-2">✅ Al abrir caja:</h4>
              <ul class="text-sm text-green-700 space-y-1">
                <li>• Se registrará la apertura</li>
                <li>• Se imprimirá un comprobante</li>
                <li>• Se habilitará el cierre de caja</li>
                <li>• Se guardará en el historial</li>
              </ul>
            </div>
          </section>
        </div>

        <hr class="my-4 border-gray-300">

        <div class="flex justify-end space-x-3">
          <x-wireui.button 
            secondary
            x-on:click="show=false"
            text="Cancelar" 
          />
          
          <x-wireui.button 
            wire:click="store"
            text="Abrir Caja"
            load
            textLoad="Abriendo caja..."
            primary
          />
        </div>
      @endif

    </x-wireui.card>
  </x-wireui.modal>
</div>