<x-app-layout>

  <div x-data="{ toggleView: false, order: {} }"
    x-init="
      $watch('toggleView', value => { 
        if (!value) order = {} 
      })
    "
    @toggle-view.window="toggleView=$event.detail"
    @current-order.window="order=$event.detail"
    class="pb-10">

    <x-loads.panel-fixed id="load-panel"
      text="Cargando..."
      class="z-[999] hidden" />

    <livewire:admin.quick-sale.customers />

    <livewire:admin.customers.create />

    <livewire:admin.quick-sale.change />

    @include('livewire.admin.quick-sale.modal-tables')

    @include('pdfs.ticket-bill')

    @include('pdfs.ticket-pre-bill')

    @include('pdfs.ticket-command-bill')

    <template x-if="Object.keys(order).length">
      <div
        class="sticky top-14 z-20 mb-2 flex w-full items-center justify-between space-x-4 border-b bg-white py-1 shadow">

        <button @click="$dispatch('verify-block-order')"
          class="rounded bg-cyan-400 px-4 py-2 font-bold text-white hover:bg-cyan-500">
          <i class="ico icon-arrow-l"></i>
          Mesas
        </button>

        <a @click="$dispatch('open-modal-tables', {order: order, view: 'order'})"
          class="cursor-pointer hover:text-cyan-400 hover:underline">
          <i class="ti ti-replace"></i>
          <span x-text="order.name"
            class="font-semibold"></span>
        </a>

        <div class="flex items-center space-x-2 pr-4">
          <span>Cliente:</span>
          <span x-text="order.customer.names"
            class="font-semibold"></span>
          
          <!-- Botón para seleccionar cliente -->
          <button @click="window.alpineOrdersInstance?.showCustomers(order)"
            class="ml-2 p-1 text-cyan-400 hover:text-cyan-600 hover:bg-cyan-50 rounded"
            title="Seleccionar cliente">
            <i class="ti ti-user text-lg"></i>
          </button>
          
          <!-- Botón para crear cliente nuevo -->
          <button @click="Livewire.emitTo('admin.customers.create', 'openCreate')"
            class="p-1 text-green-500 hover:text-green-700 hover:bg-green-50 rounded"
            title="Crear cliente nuevo">
            <i class="ti ti-user-plus text-lg"></i>
          </button>
        </div>

      </div>
    </template>

    <div x-show="!toggleView">
      <livewire:admin.quick-sale.orders />
    </div>

    <div x-show="toggleView"
      class="flex space-x-4">

      <div class="w-full">
        <livewire:admin.quick-sale.products />
        @include('livewire.admin.quick-sale.presentations')
      </div>

      <div class="w-[40%] xl:w-[33%]">
        @include('livewire.admin.quick-sale.cart')
      </div>

    </div>
  </div>

</x-app-layout>
