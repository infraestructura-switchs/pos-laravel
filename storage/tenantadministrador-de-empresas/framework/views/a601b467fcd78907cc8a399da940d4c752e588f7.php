<div x-data="alpineCart">

  <?php if (isset($component)) { $__componentOriginalbcb1b82e5f0838c22c3cae8d60511b7be93d9a1d = $component; } ?>
<?php $component = App\View\Components\Wireui\Card::resolve(['padding' => 'p-2'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('wireui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Wireui\Card::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>

     <?php $__env->slot('header', null, []); ?> 
      <div class="flex items-center border-b px-3 py-1">

        <h1 class="font-semibold">
          Productos agregados
        </h1>

        <div class="ml-auto flex space-x-2">
          <span x-text="formatToCop(total)"
            class="font-semibold leading-3"></span>
        </div>
      </div>
     <?php $__env->endSlot(); ?>

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


    <div class="flex justify-end border-t py-2">

      <div class="space-x-3">

        <?php if (isset($component)) { $__componentOriginalf9792ceae53793171c0cbf93396898df587beb77 = $component; } ?>
<?php $component = App\View\Components\Wireui\Button::resolve(['text' => 'Limpiar'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('wireui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Wireui\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['@click' => 'products=[]','x-show' => 'products.length','danger' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf9792ceae53793171c0cbf93396898df587beb77)): ?>
<?php $component = $__componentOriginalf9792ceae53793171c0cbf93396898df587beb77; ?>
<?php unset($__componentOriginalf9792ceae53793171c0cbf93396898df587beb77); ?>
<?php endif; ?>

        <?php if (isset($component)) { $__componentOriginalf9792ceae53793171c0cbf93396898df587beb77 = $component; } ?>
<?php $component = App\View\Components\Wireui\Button::resolve(['text' => 'Resturar'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('wireui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Wireui\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['x-show' => 'changedHash','x-on:click' => 'restore()']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf9792ceae53793171c0cbf93396898df587beb77)): ?>
<?php $component = $__componentOriginalf9792ceae53793171c0cbf93396898df587beb77; ?>
<?php unset($__componentOriginalf9792ceae53793171c0cbf93396898df587beb77); ?>
<?php endif; ?>

        <?php if(request()->routeIs('admin.direct-sale.create')): ?>
          <?php if (isset($component)) { $__componentOriginalf9792ceae53793171c0cbf93396898df587beb77 = $component; } ?>
<?php $component = App\View\Components\Wireui\Button::resolve(['text' => 'Facturar'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('wireui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Wireui\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['@click' => 'showWhatsappConfirmation()','wire:loading.attr' => 'disabled','wire:loading.class' => 'opacity-50 cursor-not-allowed']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf9792ceae53793171c0cbf93396898df587beb77)): ?>
<?php $component = $__componentOriginalf9792ceae53793171c0cbf93396898df587beb77; ?>
<?php unset($__componentOriginalf9792ceae53793171c0cbf93396898df587beb77); ?>
<?php endif; ?>
        <?php else: ?>
          <?php if (isset($component)) { $__componentOriginalf9792ceae53793171c0cbf93396898df587beb77 = $component; } ?>
<?php $component = App\View\Components\Wireui\Button::resolve(['text' => 'Facturar'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('wireui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Wireui\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['@click' => 'storeBill()','wire:loading.attr' => 'disabled','wire:loading.class' => 'opacity-50 cursor-not-allowed']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf9792ceae53793171c0cbf93396898df587beb77)): ?>
<?php $component = $__componentOriginalf9792ceae53793171c0cbf93396898df587beb77; ?>
<?php unset($__componentOriginalf9792ceae53793171c0cbf93396898df587beb77); ?>
<?php endif; ?>
        <?php endif; ?>

        <?php if (! (request()->routeIs('admin.direct-sale.create'))): ?>
          <?php if (isset($component)) { $__componentOriginalf9792ceae53793171c0cbf93396898df587beb77 = $component; } ?>
<?php $component = App\View\Components\Wireui\Button::resolve(['text' => 'Guardar'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('wireui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Wireui\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['x-show' => 'order.id','x-text' => 'update ? \'Actualizar\' : \'Guardar\'','@click' => 'store()','success' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf9792ceae53793171c0cbf93396898df587beb77)): ?>
<?php $component = $__componentOriginalf9792ceae53793171c0cbf93396898df587beb77; ?>
<?php unset($__componentOriginalf9792ceae53793171c0cbf93396898df587beb77); ?>
<?php endif; ?>
        <?php endif; ?>

      </div>

    </div>

   <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbcb1b82e5f0838c22c3cae8d60511b7be93d9a1d)): ?>
<?php $component = $__componentOriginalbcb1b82e5f0838c22c3cae8d60511b7be93d9a1d; ?>
<?php unset($__componentOriginalbcb1b82e5f0838c22c3cae8d60511b7be93d9a1d); ?>
<?php endif; ?>
</div>
<?php /**PATH C:\Users\USUARIO\Documents\proyecto-pos\app-pos-laravel\resources\views/livewire/admin/quick-sale/cart.blade.php ENDPATH**/ ?>