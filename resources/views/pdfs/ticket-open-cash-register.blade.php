<div x-data="alpineTicketOpenCashRegister()" class="print">
  <div x-show="show" class="absolute left-0 top-0 -z-40 w-full bg-white px-4 py-10 font-roboto !text-[12px]">

    {{-- Información de la apertura --}}
    <div class="overflow-hidden">
      <p class="text-sm font-bold text-center mb-4">APERTURA DE CAJA</p>
      
      <ul class="leading-4 whitespace-nowrap space-y-1">
        <li>
          <span class="inline-block w-16">Fecha:</span>
          <span class="font-medium" x-text="datetime"></span>
        </li>
        <li>
          <span class="inline-block w-16">Cajero:</span>
          <span class="font-medium" x-text="user || '{{ auth()->user()->name }}'"></span>
        </li>
        <li>
          <span class="inline-block w-16">Terminal:</span>
          <span class="font-medium" x-text="terminal"></span>
        </li>
      </ul>

      <hr class="my-3 border-gray-400">

      <div class="mb-3">
        <p class="font-semibold text-center mb-2">DINERO INICIAL</p>
        <ul class="leading-4 space-y-1">
          <li class="flex justify-between">
            <span>Efectivo:</span>
            <span class="font-medium" x-text="'$' + (initialCash ?? 0).toLocaleString()"></span>
          </li>
          <li x-show="(initialCoins ?? 0) > 0" class="flex justify-between">
            <span>Monedas:</span>
            <span class="font-medium" x-text="'$' + (initialCoins ?? 0).toLocaleString()"></span>
          </li>
          <li class="flex justify-between border-t pt-1 font-bold">
            <span>TOTAL:</span>
            <span x-text="'$' + (totalInitial ?? initialCash ?? 0).toLocaleString()"></span>
          </li>
        </ul>
      </div>

      <div x-show="observations ?? false" class="mb-3">
        <p class="font-semibold">Observaciones:</p>
        <p class="text-xs mt-1" x-text="observations ?? ''"></p>
      </div>

      <hr class="my-3 border-gray-400">
      
      <p class="text-center text-xs font-medium">
        Caja registrada exitosamente
      </p>
    </div>

  </div>
</div>
