<div x-data="alpineBill" class="px-2 sm:px-4 pt-2 sm:pt-4 pb-14 text-gray-700">

  <div class="mx-auto max-w-full overflow-x-auto">
    <section class="col-span-full space-y-3 sm:space-y-5">

      <x-wireui.card title="Información del cliente">

        <div class="flex justify-end -mt-3">
          <livewire:admin.customers.search>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">

          <div class="border-b border-gray-300 min-w-0">
            <label class="text-sm font-bold">Nit/Cédula</label>
            <h1 x-text="customer.no_identification" class="mt-2 h-5 text-sm truncate"></h1>
          </div>
          <div class="border-b border-gray-300 min-w-0">
            <label class="text-sm font-bold">Nombre</label>
            <h1 x-text="customer.names" class="mt-2 h-5 text-sm truncate"></h1>
          </div>
          <div class="border-b border-gray-300 sm:col-span-2 lg:col-span-1 min-w-0">
            <label class="text-sm font-bold">Teléfono</label>
            <h1 x-text="customer.phone" class="mt-2 h-5 text-sm truncate"></h1>
          </div>
        </div>

        @error('customer.id')
          <x-slot:footer>
            <div class="text-right">
              <x-wireui.error for="customer.id" class="mr-2" />
            </div>
          </x-slot:footer>
        @enderror

      </x-wireui.card>

      <x-wireui.card title="Información del producto">

        <div class="flex justify-end -mt-3">
          <livewire:admin.products.search>
        </div>

        <div wire:ignore class="">

          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6">
            <div class="border-b border-gray-300 min-w-0">
              <label class="text-sm font-bold">Referencia</label>
              <h1 x-text="product.reference" class="h-5 text-sm truncate"></h1>
            </div>

            <div class="border-b border-gray-300 min-w-0">
              <label class="text-sm font-bold">Nombre</label>
              <h1 x-text="product.name" class="h-5 text-sm truncate"></h1>
            </div>

            <div class="border-b border-gray-300 min-w-0">
              <label class="text-sm font-bold">Impuestos</label>
              <h1 x-text="rates" class="h-5 text-sm truncate"></h1>
            </div>

            <div class="border-b border-gray-300 min-w-0">
              <label class="text-sm font-bold">Stock</label>
              <h1 x-text="product.stock" class="h-5 text-sm truncate"></h1>
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-6 mt-4">

            <div class="relative border-b border-gray-500">
              <label class="text-sm font-bold">Presentaciones</label>
              <div x-on:click.away="showDropdownPrensentations=false">
                <div x-on:click="showDropdownPrensentations = !showDropdownPrensentations"
                  class="flex items-center cursor-pointer">
                  <span x-text="presentation.name ?? 'No Aplica' " class="text-sm"></span>
                  <i class="ml-auto duration-300 ico icon-arrow-b"
                    :class="showDropdownPrensentations ? 'rotate-180' : ''"></i>
                </div>
                <ul x-show="showDropdownPrensentations"
                  class="absolute w-full text-sm bg-white border divide-y shadow-sm select-none z-10">
                  <template x-for="(item, index) in presentations">
                    <li class="flex px-2 cursor-pointer hover:bg-slate-100"
                      x-html="item.name + '<span class=\'ml-auto\'>' + formatToCop(item.price) + '</span>'"
                      :class="item.id == presentation.id ? 'font-bold text-blue-700' : ''"
                      x-on:click="setPresentation(item)"></li>
                  </template>
                </ul>
              </div>
            </div>

            <div x-ref="total" class="border-b border-gray-300">
              <label class="text-sm font-bold">Precio Unidad</label>
              <h1 x-text="formatToCop(price)" class="h-5 text-sm text-center"></h1>
            </div>

            <div x-ref="cant" class="border-b border-gray-500">
              <label class="text-sm font-bold">Cantidad</label>
              <input x-ref="amount" x-bind="inputAmount" x-model="amount" onkeypress='return onlyNumbers(event)'
                type="text"
                class="py-0 px-0 w-full h-5 text-sm text-center border-none focus:border-transparent focus:ring-0 focus:outline-none">
            </div>

            <div x-ref="desc" class="border-b border-gray-500">
              <label class="text-sm font-bold">Desc. %</label>
              @include('livewire.admin.bills.percent')
            </div>

            <div x-ref="desc" class="border-b border-gray-500">
              <label class="text-sm font-bold">Desc. $</label>
              <input x-bind="inputDiscount" x-model="discount" x-bind:disabled="Boolean(percent)"
                onkeypress='return onlyNumbers(event)' type="text"
                class="py-0 px-0 w-full h-5 text-sm text-center border-none focus:border-transparent focus:ring-0 focus:outline-none">
            </div>

          </div>

          <div class="mt-4 text-center sm:text-right">
            <div>
              <label class="text-xl sm:text-2xl font-bold">Total</label>
              <h1 x-text="formatToCop(total)" class="text-lg sm:text-xl font-bold"></h1>
            </div>
          </div>

        </div>

        <x-slot:footer>
          <div class="flex flex-col space-y-2 sm:flex-row sm:justify-end sm:items-center sm:space-y-0 sm:space-x-2">
            <div class="text-center sm:text-left">
              <x-wireui.error for="products.*.id" class="block" />
              <x-wireui.error for="products.*.discount" class="block" />
              <x-wireui.error for="products" class="block" />
            </div>

            <div wire:ignore class="flex flex-col space-y-2 sm:flex-row sm:space-y-0 sm:space-x-2">
              <div x-show="alert" class="inline-flex items-center text-sm text-red-500 justify-center sm:justify-start">
                <i class="mr-1 text-xl ico icon-alert"></i>
                <span x-text="alert"></span>
              </div>

              <div class="flex flex-col space-y-2 sm:flex-row sm:space-y-0 sm:space-x-2">
                <div class="lg:hidden">
                  <x-wireui.button icon="inventory" x-on:click="$dispatch('open-products')" text="Productos" class="w-full sm:w-auto" />
                </div>

                <template x-if="Object.keys(product).length">
                  <template x-if="!update">
                    <x-wireui.button x-on:click="addProduct()" text="Agregar" class="w-full sm:w-auto" />
                  </template>
                </template>

                <div x-show="update" class="flex flex-col space-y-2 sm:flex-row sm:space-y-0 sm:space-x-2">
                  <x-wireui.button x-on:click="updateProduct()" text="Actualizar" class="w-full sm:w-auto" />
                  <x-wireui.button danger x-on:click="cancel()" text="Cancelar" class="w-full sm:w-auto" />
                </div>
              </div>
            </div>
          </div>
        </x-slot:footer>

      </x-wireui.card>

      <div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 my-4">
          <x-wireui.native-select wire:model.defer="payment_method_id" optionKeyValue="true"
            placeholder="Medio de pago" :options="$paymentMethods" />
          <x-wireui.native-select x-model="finance" name="finance" optionKeyValue="true"
            placeholder="Método de pago" :options="[0 => 'Contado', 1 => 'Crédito']" />
          <div x-show="finance == 1" class="sm:col-span-2 lg:col-span-1">
            <x-wireui.input label="Fecha de vencimiento" wire:model.defer="due_date" type="date" />
          </div>
        </div>
        <div class="flex flex-col space-y-1">
          <x-wireui.error for="payment_method_id" />
          <x-wireui.error for="finance" />
          <x-wireui.error for="due_date" />
        </div>
      </div>

      <!-- Tabla Desktop -->
      <div wire:ignore class="hidden md:block overflow-x-auto mt-5 bg-white border shadow-sm scroll border-slate-200" style="max-width: 100%;">
        <table class="w-full min-w-max">
          <thead class="text-xs font-semibold uppercase bg-gray-100 border border-slate-200 text-slate-500">
            <tr>
              <th class="py-2 px-2 text-left">
                #
              </th>
              <th class="py-2 px-2 text-left">
                Referencia
              </th>
              <th class="py-2 px-2 text-left">
                Nombre
              </th>
              <th class="py-2 px-2 text-center">
                Cantidad
              </th>
              <th class="py-2 px-2 text-center">
                V. Unidad
              </th>
              <th class="py-2 px-2 text-center">
                Descuento
              </th>
              <th class="py-2 px-2 text-center">
                Total
              </th>
              <th class="py-2 px-2 text-center">
                Acciones
              </th>
            </tr>
          </thead>
          <tbody class="text-sm divide-y divide-slate-200">
            <template x-for="(item, index) in products" :key="index">
              <tr>
                <td x-text="index+1" class="py-1 px-2 font-semibold text-blue-500"></td>
                <td x-text="item.reference" class="py-1 px-2 font-semibold text-blue-500"></td>
                <template x-if="Object.keys(item.presentation).length">
                  <td x-text="`${item.name} [${item.presentation.name}]`"
                    class="py-1 px-2 whitespace-nowrap text-slate-600"></td>
                </template>
                <template x-if="!Object.keys(item.presentation).length">
                  <td x-text="item.name" class="py-1 px-2 whitespace-nowrap text-slate-600"></td>
                </template>
                <td x-text="item.amount" class="py-1 px-2 font-semibold text-center whitespace-nowrap text-slate-600"></td>
                <td x-text="formatToCop(item.price)" class="py-1 px-2 font-semibold text-center whitespace-nowrap text-slate-600"></td>
                <td x-text="formatToCop(item.discount)" class="py-1 px-2 font-semibold text-center whitespace-nowrap text-slate-600"></td>
                <td x-text="formatToCop(item.total)" class="py-1 px-2 font-semibold text-center whitespace-nowrap text-slate-600"></td>
                <td class="py-1 px-4 space-x-1 text-center">
                  <x-buttons.edit x-on:click="editProduct(index)" />
                  <x-buttons.delete x-on:click="deleteProduct(index)" />
                </td>
              </tr>
            </template>
            <template x-if="!Object.keys(products).length">
              <x-commons.table-empty text="No se encontraron productos agregados" />
            </template>
          </tbody>
        </table>
      </div>

      <!-- Vista Mobile - Cards -->
      <div wire:ignore class="md:hidden mt-5 space-y-3">
        <template x-for="(item, index) in products" :key="index">
          <div class="bg-white border border-slate-200 rounded-lg p-4 shadow-sm">
            <div class="flex justify-between items-start mb-3">
              <div class="flex-1">
                <div class="flex items-center space-x-2 mb-1">
                  <span class="bg-blue-100 text-blue-600 text-xs font-semibold px-2 py-1 rounded" x-text="`#${index+1}`"></span>
                  <span class="text-sm font-semibold text-blue-500" x-text="item.reference"></span>
                </div>
                <template x-if="Object.keys(item.presentation).length">
                  <h3 x-text="`${item.name} [${item.presentation.name}]`" class="text-sm font-medium text-gray-900"></h3>
                </template>
                <template x-if="!Object.keys(item.presentation).length">
                  <h3 x-text="item.name" class="text-sm font-medium text-gray-900"></h3>
                </template>
              </div>
              <div class="flex space-x-1">
                <x-buttons.edit x-on:click="editProduct(index)" />
                <x-buttons.delete x-on:click="deleteProduct(index)" />
              </div>
            </div>
            <div class="grid grid-cols-2 gap-3 text-sm">
              <div>
                <span class="text-gray-500">Cantidad:</span>
                <span class="font-semibold ml-1" x-text="item.amount"></span>
              </div>
              <div>
                <span class="text-gray-500">V. Unidad:</span>
                <span class="font-semibold ml-1" x-text="formatToCop(item.price)"></span>
              </div>
              <div>
                <span class="text-gray-500">Descuento:</span>
                <span class="font-semibold ml-1" x-text="formatToCop(item.discount)"></span>
              </div>
              <div>
                <span class="text-gray-500">Total:</span>
                <span class="font-semibold ml-1 text-green-600" x-text="formatToCop(item.total)"></span>
              </div>
            </div>
          </div>
        </template>
        <template x-if="!Object.keys(products).length">
          <div class="bg-white border border-slate-200 rounded-lg p-8 text-center">
            <p class="text-gray-500">No se encontraron productos agregados</p>
          </div>
        </template>
      </div>

      <x-wireui.card title="Observaciones">
        <x-wireui.textarea wire:model.defer="observation"  />
      </x-wireui.card>

      <div class="mt-4 space-y-1">
        <!-- Desktop: Alineado a la derecha -->
        <div class="hidden sm:block pr-4 text-right">
          <div class="font-bold text-tiny">
            <span>VALOR BRUTO</span>
            <span class="inline-block w-28 text-right" x-text="formatToCop(subtotalT)"></span>
          </div>
          <div class="font-bold text-tiny">
            <span>DESCUENTO</span>
            <span class="inline-block w-28 text-right" x-text="formatToCop(discountT)"></span>
          </div>
          <div>
            <template x-for="(item, index) in taxRatesByTributeT">
              <div class="font-bold text-tiny">
                <span x-text="item.name"></span>
                <span class="inline-block w-28 text-right" x-text="formatToCop(item.value)"></span>
              </div>
            </template>
          </div>
          <div class="font-bold text-tiny">
            <span>TOTAL IMPUESTOS</span>
            <span class="inline-block w-28 text-right" x-text="formatToCop(totalTaxRatesT)"></span>
          </div>
          <div class="font-bold text-tiny">
            <span>TOTAL</span>
            <span class="inline-block w-28 text-right" x-text="formatToCop(totalT)"></span>
          </div>
        </div>

        <!-- Mobile: Card style -->
        <div class="sm:hidden bg-gray-50 p-4 rounded-lg space-y-2">
          <div class="flex justify-between text-sm font-bold">
            <span>VALOR BRUTO</span>
            <span x-text="formatToCop(subtotalT)"></span>
          </div>
          <div class="flex justify-between text-sm font-bold">
            <span>DESCUENTO</span>
            <span x-text="formatToCop(discountT)"></span>
          </div>
          <template x-for="(item, index) in taxRatesByTributeT">
            <div class="flex justify-between text-sm font-bold">
              <span x-text="item.name"></span>
              <span x-text="formatToCop(item.value)"></span>
            </div>
          </template>
          <div class="flex justify-between text-sm font-bold">
            <span>TOTAL IMPUESTOS</span>
            <span x-text="formatToCop(totalTaxRatesT)"></span>
          </div>
          <div class="flex justify-between text-lg font-bold text-green-600 border-t pt-2">
            <span>TOTAL</span>
            <span x-text="formatToCop(totalT)"></span>
          </div>
        </div>
      </div>

      <div class="flex flex-col space-y-3 sm:flex-row sm:justify-end sm:items-center sm:space-y-0 sm:space-x-4 pr-4 mt-6">

        @if ($errors->count())
          <span class="text-sm text-red-600 text-center sm:text-right">La factura contiene errores de validación</span>
        @endif

        <x-wireui.button x-on:click="openChange()" wire:target="store,openChange" text="Guardar" load
          textLoad="Guardando" class="w-full sm:w-auto py-3 sm:py-2" />
      </div>

      <x-wireui.errors />

    </section>
  </div>

  @include('pdfs.ticket-bill')

  {{-- MODALS --}}
  @include('livewire.admin.bills.presentations')

  <livewire:admin.customers.create>

  <x-loads.panel-fixed text="Enviando factura" class="no-print z-[999]" wire:loading wire:target='store' />

  @include('livewire.admin.bills.change')

</div>
