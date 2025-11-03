<div x-data="alpineProducts()">

  <?php if (isset($component)) { $__componentOriginalbcb1b82e5f0838c22c3cae8d60511b7be93d9a1d = $component; } ?>
<?php $component = App\View\Components\Wireui\Card::resolve(['padding' => 'p-0'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('wireui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Wireui\Card::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>

    <div class="pt-2">

       <?php $__env->slot('header', null, []); ?> 

       <?php $__env->endSlot(); ?>

      <div class="pl-2 pr-2">
        <div class="flex items-center gap-2">

          <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.commons.search','data' => ['id' => 'searchProduct','placeholder' => 'Buscar producto','xRef' => 'search','xModel' => 'search','xOn:keyup.escape' => '$refs.search.blur();','class' => 'flex-1 min-w-0 h-9 duration-300','autocomplete' => 'off']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('commons.search'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'searchProduct','placeholder' => 'Buscar producto','x-ref' => 'search','x-model' => 'search','x-on:keyup.escape' => '$refs.search.blur();','class' => 'flex-1 min-w-0 h-9 duration-300','autocomplete' => 'off']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>

          <button x-on:click="setCategory(null)"
            class="rounded bg-indigo-600 h-9 px-3 text-xs sm:text-sm text-white whitespace-nowrap">
            Eliminar filtros
          </button>

        </div>

        <div class="mt-2 h-[5.5rem] overflow-hidden overflow-y-auto">
          <ul class="flex flex-wrap text-xs sm:text-sm gap-1">

            <template x-for="(item, key) in categories" :key="'category-' + key">

              <li x-on:click="setCategory(key)" class="cursor-pointer rounded-full px-2 py-1 whitespace-nowrap"
                :class="key == category_id ? 'text-white bg-cyan-500' : 'bg-slate-200 hover:bg-slate-300'">
                <span x-text="item"></span>
              </li>

            </template>

          </ul>
        </div>
      </div>

      <div class="h-2 w-full shadow-md shadow-slate-300"></div>

      <div class="h-96 overflow-hidden overflow-y-auto pb-2 pl-2 pt-2">
        <ul
          class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-2 rounded pr-2 text-xs sm:text-sm text-slate-800">
          <template x-for="(item, index) in filteredItems" :key="'product-' + item.id">

            <li x-on:click="setItem(item)"
              class="relative overflow-hidden rounded-md border border-slate-300 font-medium">

              <div class="flex h-full flex-col px-1 py-2"
:class="item.has_stock ? 'hover:bg-cyan-500 hover:text-white cursor-pointer ' : ''">

                <!-- Imagen del producto -->
                <div class="mb-2 flex justify-center">
                  <img :src="item.image_url || '/images/no-product-image.svg'" 
                       :alt="item.name"
                       class="h-12 w-12 sm:h-14 sm:w-14 md:h-16 md:w-16 object-cover rounded"
                       loading="lazy"
                       onerror="this.src='/images/no-product-image.svg'">
                </div>

                <div class="mt-auto select-none text-xs">
                  <span x-text="item.reference" class="font-medium text-blue-600 text-xs"></span>
                  <p x-text="item.name" class="leading-3 text-xs sm:text-sm break-words"></p>
                </div>

              </div>

              <div x-show='!item.has_stock'
                class="absolute inset-0 flex items-center justify-center bg-red-500 bg-opacity-60">
                <span class="font-bold text-white">
                  Sin stock
                </span>
              </div>

            </li>

          </template>

          <template x-if="!filteredItems.length">
            <li class="col-span-full py-2 text-center text-base">
              No se encontraron productos
            </li>
          </template>

        </ul>
      </div>
    </div>

   <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbcb1b82e5f0838c22c3cae8d60511b7be93d9a1d)): ?>
<?php $component = $__componentOriginalbcb1b82e5f0838c22c3cae8d60511b7be93d9a1d; ?>
<?php unset($__componentOriginalbcb1b82e5f0838c22c3cae8d60511b7be93d9a1d); ?>
<?php endif; ?>

</div>
<?php /**PATH C:\Users\USUARIO\Documents\proyecto-pos\app-pos-laravel\resources\views/livewire/admin/quick-sale/products.blade.php ENDPATH**/ ?>