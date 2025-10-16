<div class="container">

  <x-commons.header>
    <x-wireui.button wire:click="exportWarehouses()" icon="excel" success text="Exportar a Excel" load
      textLoad="Exportando..." />
    <x-wireui.button icon="user" x-on:click="$wire.emitTo('admin.warehouses.create', 'openCreate')"
      text="Crear Almacén" />
  </x-commons.header>

  <x-commons.table-responsive>

    <x-slot:top title="Bodegas">
    </x-slot:top>

    <x-slot:header>
      <x-wireui.search placeholder="Buscar..." />
      <x-wireui.native-select wire:model.defer="filter" optionKeyValue="true" :options="$filters" />
    </x-slot:header>

    <table class="table">
      <thead>
        <tr>
          <th>
            ID
          </th>
          <th>
            Nombres
          </th>
          <th>
            Teléfono
          </th>
          <th>
            Dirección
          </th>
          <th style="width: 150px; text-align: center;">
            Acciones
          </th>
        </tr>
      </thead>
      <tbody>
        @forelse ($warehouses as $item)
          <tr wire:key="warehouse{{ $item->id }}">
            <td>
              {{$item->id}}
            </td>
            <td>
              {{ $item->name }}
            </td>
            <td>
              {{ $item->phone }}
            </td>
            <td>
              {{ $item->address }}
            </td>
            <td style="text-align: center;">
              <x-buttons.edit wire:click="$emitTo('admin.warehouses.edit', 'openEdit', {{ $item->id }})" title="Editar" />
            </td>
          </tr>
        @empty
          <x-commons.table-empty />
        @endforelse
      </tbody>
    </table>


  </x-commons.table-responsive>

  @if ($warehouses->hasPages())
    <div class="p-3">
      {{ $warehouses->links() }}
    </div>
  @endif

  <livewire:admin.warehouses.create />
  <livewire:admin.warehouses.edit />

</div>