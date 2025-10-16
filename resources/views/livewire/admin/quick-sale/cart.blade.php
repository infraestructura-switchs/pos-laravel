<div x-data="alpineCart">

  <x-wireui.card padding="p-2">

    <x-slot:header>
      <div class="flex items-center border-b px-3 py-1">

        <h1 class="font-semibold">
          Productos agregados
        </h1>

        <div class="ml-auto flex space-x-2">
          <span x-text="formatToCop(total)"
            class="font-semibold leading-3"></span>
        </div>
      </div>
    </x-slot:header>

    <div class="h-80 sm:h-96 overflow-hidden overflow-y-auto">
      <!-- Header Desktop -->
      <ul class="hidden md:block rounded-tl-lg rounded-tr-md bg-slate-200 px-2 py-1">
        <li class="flex">
          <div class="w-full text-sm font-semibold">
            Nombre
          </div>
          <div class="w-60 text-center text-sm font-semibold">
            Cantidad
          </div>
          <div class="w-48 text-center text-sm font-semibold">
            Precio
          </div>
          <div class="w-20 text-center text-sm font-semibold">
          </div>
        </li>
      </ul>
      
      <!-- Lista Desktop -->
      <ul class="hidden md:block divide-y rounded-bl-md rounded-br-md border">

        <!-- Items Desktop -->
        <template x-for="(item, index) in products" :key="'product-desktop-' + index">
          <li class="flex flex-wrap px-1 py-1" x-data="{ showComment: false }">
            <div class="flex w-full">
              <div class="w-full text-xs font-semibold">
                <span x-text="item.reference"></span>
                <p x-text="getProductName(item)" class="leading-3"></p>
                <p><span x-text="item.comment" class="font-xs text-gray-400"></span></p>
              </div>

              <div class="flex w-60 items-center justify-center font-semibold">
                <div class="h-8 overflow-hidden whitespace-nowrap rounded-md border">
                  <button @click="handleAmount(item, 'less')" class="h-full bg-slate-300 px-2 hover:bg-slate-200">
                    <i class="ico icon-minus text-xs"></i>
                  </button>
                  <input inputmode="numeric" x-model="item.amount" onkeypress='return onlyNumbers(event)'
                    @paste="event.preventDefault()" @input="calcProduct(item)"
                    class="w-10 rounded border-none px-0 py-1 text-center text-sm focus:border-transparent focus:outline-none focus:ring-0">
                  <button @click="handleAmount(item, 'add')" class="h-full bg-slate-300 px-2 hover:bg-slate-200">
                    <i class="ico icon-add text-xs"></i>
                  </button>
                </div>
              </div>

              <div class="flex w-48 items-center justify-center text-sm font-semibold">
                <span x-text="formatToCop(item.total)" class="leading-3"></span>
              </div>
              <div class="flex w-40 items-center justify-center">
                <div>
                  <button @click="showComment = !showComment" class="h-7 w-7 rounded bg-gray-600 px-0 py-0">
                    <i class="ico icon-message text-sm text-white"></i>
                  </button>
                  <button @click="dropProduct(index)" class="h-7 w-7 rounded bg-red-600 px-0 py-0">
                    <i class="ico icon-trash text-sm text-white"></i>
                  </button>
                </div>
              </div>
            </div>
            <div class="flex w-full">
              <textarea type="text" x-model="item.comment" x-show="showComment"
                class="shadow appearance-none text-xs border border-gray-300 mt-2 rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline"
                placeholder="Comentario ..."></textarea>
            </div>
          </li>
        </template>

        <template x-if="!Object.keys(products).length">
          <li class="py-2 text-center text-sm font-semibold">
            No se encontraron productos agregados
          </li>
        </template>

      </ul>

      <!-- Lista Mobile (Simplificada) -->
      <div class="md:hidden space-y-2 pt-2">
        <template x-for="(item, index) in products" :key="'product-mobile-' + index">
          <div class="border border-gray-200 rounded p-2 sm:p-3" x-data="{ showComment: false }">
            
            <!-- Info del producto -->
            <div class="flex justify-between items-start mb-2">
              <div class="flex-1 min-w-0">
                <p class="text-xs sm:text-sm font-semibold truncate" x-text="getProductName(item)"></p>
                <p class="text-xs text-gray-500" x-text="item.reference"></p>
                <p class="text-xs text-gray-400" x-show="item.comment" x-text="item.comment"></p>
              </div>
              
              <div class="flex space-x-1 ml-2">
                <button @click="showComment = !showComment" 
                        class="h-6 w-6 sm:h-7 sm:w-7 rounded bg-gray-500 text-white text-xs flex items-center justify-center">
                  <i class="ico icon-message text-xs"></i>
                </button>
                <button @click="dropProduct(index)" 
                        class="h-6 w-6 sm:h-7 sm:w-7 rounded bg-red-500 text-white text-xs flex items-center justify-center">
                  <i class="ico icon-trash text-xs"></i>
                </button>
              </div>
            </div>

            <!-- Cantidad y Precio -->
            <div class="flex items-center justify-between">
              <div class="flex items-center border rounded">
                <button @click="handleAmount(item, 'less')" 
                        class="h-7 w-7 sm:h-8 sm:w-8 bg-gray-100 hover:bg-gray-200 flex items-center justify-center">
                  <i class="ico icon-minus text-xs"></i>
                </button>
                <input inputmode="numeric" x-model="item.amount" onkeypress='return onlyNumbers(event)'
                       @paste="event.preventDefault()" @input="calcProduct(item)"
                       class="w-10 sm:w-12 h-7 sm:h-8 text-center border-0 text-xs sm:text-sm font-semibold focus:ring-0">
                <button @click="handleAmount(item, 'add')" 
                        class="h-7 w-7 sm:h-8 sm:w-8 bg-gray-100 hover:bg-gray-200 flex items-center justify-center">
                  <i class="ico icon-add text-xs"></i>
                </button>
              </div>
              
              <span class="font-semibold text-green-600 text-xs sm:text-sm" x-text="formatToCop(item.total)"></span>
            </div>

            <!-- Comentario -->
            <div x-show="showComment" class="mt-2">
              <textarea x-model="item.comment"
                        class="w-full text-xs sm:text-sm border rounded py-1 px-2 focus:outline-none focus:ring-1 focus:ring-cyan-500"
                        placeholder="Comentario..." rows="2"></textarea>
            </div>

          </div>
        </template>

        <!-- Mensaje vacío -->
        <template x-if="!Object.keys(products).length">
          <div class="text-center py-4">
            <p class="text-sm text-gray-500">No hay productos agregados</p>
          </div>
        </template>
      </div>
    </div>

    <div id="delivery">
      <div class="flex items-center justify-between border-t px-3 py-2">
        <div class="w-full">
          <input type="text" id="delivery" x-model="order.delivery_address" placeholder="Domicilio"
            class="h-8 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
        </div>
      </div>
    </div>

    <div class="flex justify-end border-t py-2">

      <div class="space-x-3">

        <x-wireui.button @click="products=[]"
          x-show="products.length"
          danger
          text="Limpiar" />

        <x-wireui.button x-show="changedHash"
          x-on:click="restore()"
          text="Resturar" />

        <x-wireui.button @click="storeBill()"
          text="Facturar" />

        @unless(request()->routeIs('admin.direct-sale.create'))
          <x-wireui.button x-show="order.id" x-text="update ? 'Actualizar' : 'Guardar'"
            @click="store()"
            success
            text="Guardar" />
        @endunless

      </div>

    </div>

  </x-wireui.card>
</div>
