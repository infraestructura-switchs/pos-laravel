<div>
  <x-wireui.card title="Aperturas de Caja">

    {{-- Filtros --}}
    <div class="mb-4 grid grid-cols-1 md:grid-cols-3 gap-4">
      <x-wireui.input 
        wire:model.lazy="search"
        placeholder="Buscar por usuario o terminal..."
        icon="search"
      />

      <x-wireui.native-select 
        wire:model="terminal_id"
        placeholder="Todas las terminales"
      >
        <option value="">Todas las terminales</option>
        @foreach($terminals as $terminal)
          <option value="{{ $terminal->id }}">{{ $terminal->name }}</option>
        @endforeach
      </x-wireui.native-select>

      <x-wireui.native-select 
        wire:model="status"
        placeholder="Todos los estados"
      >
        <option value="">Todos los estados</option>
        <option value="1">Activas</option>
        <option value="0">Cerradas</option>
      </x-wireui.native-select>
    </div>

    {{-- Tabla --}}
    <x-commons.table-responsive>
      <table class="table-sm">
        <thead>
          <tr>
            <th left>Fecha Apertura</th>
            <th left>Usuario</th>
            <th left>Terminal</th>
            <th center>Dinero Inicial</th>
            <th center>Estado</th>
            <th left>Observaciones</th>
            <th center>Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($cashOpenings as $opening)
            <tr wire:key="opening-{{ $opening->id }}">
              <td left>
                <div>
                  <div class="font-medium">{{ $opening->opened_at->format('d/m/Y') }}</div>
                  <div class="text-xs text-gray-500">{{ $opening->opened_at->format('H:i:s') }}</div>
                </div>
              </td>
              
              <td left>
                <div class="font-medium">{{ $opening->user->name }}</div>
              </td>
              
              <td left>
                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs font-medium">
                  {{ $opening->terminal->name }}
                </span>
              </td>
              
              <td center>
                <div>
                  <div class="font-semibold">@formatToCop($opening->total_initial)</div>
                  @if($opening->initial_coins > 0)
                    <div class="text-xs text-gray-500">
                      (Efectivo: @formatToCop($opening->initial_cash), Monedas: @formatToCop($opening->initial_coins))
                    </div>
                  @endif
                </div>
              </td>
              
              <td center>
                @if($opening->is_active)
                  <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-medium">
                    <i class="ti ti-circle-filled mr-1"></i>
                    Activa
                  </span>
                @else
                  <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs font-medium">
                    <i class="ti ti-circle mr-1"></i>
                    Cerrada
                  </span>
                @endif
              </td>
              
              <td left>
                <div class="max-w-xs truncate">
                  {{ $opening->observations ?: '—' }}
                </div>
              </td>
              
              <td center>
                <div class="flex justify-center space-x-1">
                  @if($opening->cashClosing)
                    <a href="#" class="text-blue-600 hover:text-blue-800" title="Ver cierre relacionado">
                      <i class="ti ti-file-invoice"></i>
                    </a>
                  @endif
                  
                  @if($opening->is_active)
                    <button class="text-green-600 hover:text-green-800" title="Caja activa">
                      <i class="ti ti-circle-check"></i>
                    </button>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <x-commons.table-empty />
          @endforelse
        </tbody>
      </table>
    </x-commons.table-responsive>

    {{-- Paginación --}}
    <div class="mt-4">
      {{ $cashOpenings->links() }}
    </div>

    {{-- Estadísticas rápidas --}}
    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
      <div class="p-4 bg-green-50 rounded-lg border border-green-200">
        <div class="flex items-center">
          <i class="ti ti-circle-check text-green-600 mr-2 text-xl"></i>
          <div>
            <div class="text-sm text-green-600 font-medium">Cajas Activas</div>
            <div class="text-lg font-bold text-green-800">
              {{ $cashOpenings->where('is_active', true)->count() }}
            </div>
          </div>
        </div>
      </div>

      <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
        <div class="flex items-center">
          <i class="ti ti-cash text-blue-600 mr-2 text-xl"></i>
          <div>
            <div class="text-sm text-blue-600 font-medium">Total Inicial Hoy</div>
            <div class="text-lg font-bold text-blue-800">
              @formatToCop($cashOpenings->where('opened_at', '>=', now()->startOfDay())->sum('total_initial'))
            </div>
          </div>
        </div>
      </div>

      <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
        <div class="flex items-center">
          <i class="ti ti-history text-gray-600 mr-2 text-xl"></i>
          <div>
            <div class="text-sm text-gray-600 font-medium">Total Registros</div>
            <div class="text-lg font-bold text-gray-800">
              {{ $cashOpenings->total() }}
            </div>
          </div>
        </div>
      </div>
    </div>

  </x-wireui.card>
</div>