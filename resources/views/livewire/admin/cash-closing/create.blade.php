<div>
  <x-wireui.modal wire:model.defer="openCreate" max-width="4xl">
    <x-wireui.card title="Cierre de caja"
      class="!pt-1">

      <x-wireui.errors />

      <div class="flex items-end justify-between">
        <div>
          @if($currentOpening)
            <div class="text-sm text-green-600">
              <i class="ti ti-check-circle mr-1"></i>
              Caja abierta desde: {{ $currentOpening->opened_at->format('d/m/Y H:i') }}
            </div>
            <div class="text-xs text-gray-600">
              Base inicial registrada: @formatToCop($currentOpening->total_initial)
            </div>
          @else
            <div class="text-sm text-red-600">
              <i class="ti ti-alert-triangle mr-1"></i>
              No hay caja abierta
            </div>
          @endif
        </div>
        <div class="text-right">
          <span class="mr-1 font-semibold">Terminal: </span>
          <span class="text-sm font-semibold">{{ $terminal->name }}</span>
        </div>
      </div>

      <div class="grid grid-cols-2 gap-6 mt-4">
        <section>
          <p class="text-center font-semibold uppercase">
            Dinero recibido
          </p>

          <ul class="mt-2 divide-y-2 text-sm font-semibold">

            <li class="flex justify-between py-1.5">
              <span>
                Efectivo
              </span>
              <span class="text-right">
                @formatToCop($cash)
              </span>
            </li>

            <li class="flex justify-between py-1.5">
              <span>
                Tarjeta crédito
              </span>
              <span class="text-right">
                @formatToCop($credit_card)
              </span>
            </li>

            <li class="flex justify-between py-1.5">
              <span>
                Tarjeta débito
              </span>
              <span class="text-right">
                @formatToCop($debit_card)
              </span>
            </li>

            <li class="flex justify-between py-1.5">
              <span>
                Transferencia
              </span>
              <span class="text-right">
                @formatToCop($transfer)
              </span>
            </li>
          </ul>

          <p class="text-center font-semibold uppercase">
            Totales
          </p>

          <ul class="mt-2 divide-y-2 text-sm font-semibold">
            <li class="flex justify-between py-1.5">
              <span>
                Total propinas
              </span>
              <span class="text-right">
                @formatToCop($tip)
              </span>
            </li>

            <li class="flex justify-between py-1.5 text-red-600">
              <span>Total egresos</span>
              <span>@formatToCop($outputs)</span>
            </li>

            <li class="flex justify-between py-1.5">
              <span>
                Total ventas
              </span>
              <span class="text-right">
                @formatToCop($total_sales)
              </span>
            </li>
          </ul>
        </section>

        <section>

          <li class="flex justify-between py-1 text-lg font-bold">
            <span>Dinero esperado en caja</span>
            <span>@formatToCop($cashRegister)</span>
          </li>

          <div class="space-y-3">
            @if($currentOpening)
              <div class="p-3 bg-green-50 border border-green-200 rounded-lg">
                <label class="block text-sm font-medium text-gray-700 mb-1">Base inicial (desde apertura)</label>
                <div class="text-lg font-semibold text-green-800">
                  @formatToCop($base)
                </div>
                <p class="text-xs text-green-600 mt-1">
                  Registrada el {{ $currentOpening->opened_at->format('d/m/Y H:i') }}
                </p>
              </div>
            @else
              <x-wireui.input label="Base inicial"
                wire:model.debounce.500ms="base"
                onlyNumbers />
            @endif
            
            <x-wireui.input label="Dinero real en caja"
              wire:model.defer="price"
              onlyNumbers />
            
            <x-wireui.input label="Gastos (opcional)"
              wire:model.defer="gastos"
              onlyNumbers 
              placeholder="0"
              prefix="$" />
              
            <x-wireui.textarea label="Observaciones"
              wire:model.defer="observations"
              rows="3" />
          </div>
        </section>
      </div>

      <hr class="my-3 border-gray-300">

      <div class="mt-3 text-right">
        <x-wireui.button secondary
          x-on:click="show=false"
          text="Cerrar" />
        <x-wireui.button wire:click="store"
          text="Guardar"
          load
          textLoad="Guardando..." />
      </div>

    </x-wireui.card>
  </x-wireui.modal>

</div>
