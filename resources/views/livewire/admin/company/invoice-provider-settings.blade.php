<div>
  <x-wireui.card title="Proveedor de facturación" description="Selecciona y activa el proveedor tecnológico de facturación electrónica.">

    @if ($providers->isEmpty())
      <p class="text-sm text-slate-500 text-center py-4">No hay proveedores registrados.</p>
    @else
      <div class="space-y-3">
        @foreach ($providers as $provider)
          @php
            $isSelected = (int) $company->invoice_provider_id === (int) $provider->id;
            $isActive   = $provider->status === 'ACTIVE';
          @endphp

          <div class="flex items-center justify-between gap-4 rounded-xl border px-4 py-3 transition
            {{ $isSelected ? 'border-blue-500 bg-blue-50' : 'border-slate-200 bg-white' }}">

            {{-- Info del proveedor --}}
            <div class="min-w-0 flex-1">
              <div class="flex items-center gap-2 flex-wrap">
                <span class="font-semibold text-slate-800 text-sm">{{ $provider->name }}</span>

                {{-- Badge: activo / inactivo --}}
                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                  {{ $isActive ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                  {{ $isActive ? 'Activo' : 'Inactivo' }}
                </span>

                {{-- Badge: proveedor actual de la empresa --}}
                @if ($isSelected)
                  <span class="inline-flex items-center rounded-full bg-blue-100 text-blue-700 px-2 py-0.5 text-xs font-medium">
                    ✓ Seleccionado
                  </span>
                @endif
              </div>

              <div class="mt-1 flex flex-wrap gap-x-4 gap-y-0.5 text-xs text-slate-500">
                @if ($provider->nit)
                  <span>NIT: {{ $provider->nit }}</span>
                @endif
                @if ($provider->url)
                  <span>{{ $provider->url }}</span>
                @endif
                @if ($provider->phone)
                  <span>{{ $provider->phone }}</span>
                @endif
                @if ($provider->email)
                  <span>{{ $provider->email }}</span>
                @endif
              </div>
            </div>

            {{-- Acciones --}}
            <div class="flex items-center gap-2 shrink-0">

              {{-- Botón Seleccionar (solo si no está seleccionado) --}}
              @unless ($isSelected)
                <x-wireui.button
                  wire:click="selectProvider({{ $provider->id }})"
                  wire:loading.attr="disabled"
                  text="Usar este"
                  sm
                  outline />
              @endunless

              {{-- Toggle activo / inactivo --}}
              <button
                wire:click="toggleStatus({{ $provider->id }})"
                wire:loading.attr="disabled"
                title="{{ $isActive ? 'Desactivar proveedor' : 'Activar proveedor' }}"
                class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
                  {{ $isActive ? 'bg-green-500' : 'bg-slate-300' }}">
                <span
                  class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out
                    {{ $isActive ? 'translate-x-5' : 'translate-x-0' }}">
                </span>
              </button>

            </div>
          </div>
        @endforeach
      </div>
    @endif

    {{-- Nota informativa --}}
    <p class="mt-4 text-xs text-slate-400">
      El proveedor <strong>Seleccionado</strong> es el que se imprime en los PDFs de factura como proveedor tecnológico.
      Activa o desactiva proveedores sin perder la configuración anterior.
    </p>

  </x-wireui.card>
</div>
