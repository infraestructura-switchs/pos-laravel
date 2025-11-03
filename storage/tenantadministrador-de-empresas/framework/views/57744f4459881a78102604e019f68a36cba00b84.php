<div id="wire-orders"
  x-data="alpineOrders()"
  class="relative px-4 pt-2">

  <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.loads.panel-fixed','data' => ['id' => 'load-panel','text' => 'Cargando mesas...','class' => 'no-print z-[999]','wire:loading' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('loads.panel-fixed'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'load-panel','text' => 'Cargando mesas...','class' => 'no-print z-[999]','wire:loading' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>

  <div class="grid grid-cols-5 gap-4">

    <div class="flex cursor-pointer overflow-hidden rounded border bg-white">

      <div @click="loadOrder()"
        class="flex min-w-0 flex-1 flex-col items-center justify-center overflow-hidden px-2 py-4 duration-200 hover:scale-105">
        <i class="ti ti-receipt-2 text-5xl text-cyan-400"></i>
        <h1 class="font-semibold">Factura en caja</h1>
      </div>

    </div>
    <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <div wire:key="order-<?php echo e($item['id']); ?>" class="flex cursor-pointer overflow-hidden rounded border bg-white">

        <?php if(!$item['is_available']): ?>
        <div class="z-20 grid grid-rows-3 divide-y">

          <button onclick="window.dispatchEvent(new CustomEvent('open-modal-tables', {detail: {order: <?php echo \Illuminate\Support\Js::from($item)->toHtml() ?>, view: 'orders'}}))"
            class="w-10 rounded bg-cyan-400 text-white hover:bg-cyan-500"
            title="Cambiar mesa">
            <i class="ti ti-replace text-xl"></i>
          </button>

          <?php if($item['delivery_address']): ?>
          <div class="m-1 h-8 w-8 rounded-full bg-green-400 flex items-center justify-center text-white">
            <i class="ti ti-motorbike text-xl"></i>
          </div>
          <?php endif; ?>

          <button onclick="window.alpineOrdersInstance?.printPreBill(<?php echo \Illuminate\Support\Js::from($item)->toHtml() ?>)"
            class="row-start-3 row-end-4 w-10 rounded bg-cyan-400 text-white hover:bg-cyan-500"
            title="Imprimir factura">
            <i class="ti ti-printer text-xl"></i>
          </button>

          <button onclick="window.alpineOrdersInstance?.printPreBill(<?php echo \Illuminate\Support\Js::from($item)->toHtml() ?>, true)"
            class="row-start-3 ml-1 row-end-4 w-10 rounded bg-cyan-400 text-white hover:bg-cyan-500"
            title="Imprimir Comanda">
            <i class="ico icon-payment text-xl"></i>
          </button>

        </div>
        <?php endif; ?>

        <div onclick="window.alpineOrdersInstance?.loadOrder(<?php echo \Illuminate\Support\Js::from($item)->toHtml() ?>)"
          class="flex min-w-0 flex-1 flex-col items-center justify-start overflow-hidden px-2 py-4 duration-200 hover:scale-105">
          <h1 class="text-sm"><?php echo e($item['name']); ?></h1>
          <img src="<?php echo e(Storage::url('images/system/table.png')); ?>"
            class="h-14 object-cover object-center">
          <span class="mt-1 block h-4 text-sm font-bold leading-none text-green-500">
            $<?php echo e(number_format($item['total'], 0)); ?>

          </span>
          <?php if(!empty($item['customer']['names'])): ?>
          <span class="block h-4 w-full truncate text-center text-sm font-semibold leading-none">
            <?php echo e($item['customer']['names']); ?>

          </span>
          <?php else: ?>
          <span class="block h-4 w-full truncate text-center text-sm text-gray-400 leading-none">
            Disponible
          </span>
          <?php endif; ?>
        </div>

        <?php if(!$item['is_available']): ?>
        <div class="z-20 grid divide-y">

          <button onclick="window.alpineOrdersInstance?.showCustomers(<?php echo \Illuminate\Support\Js::from($item)->toHtml() ?>)"
            class="w-10 bg-cyan-400 text-white hover:bg-cyan-500"
            title="Asignar cliente">
            <i class="ti ti-user text-2xl"></i>
          </button>

          <button onclick="window.alpineOrdersInstance?.change(<?php echo \Illuminate\Support\Js::from($item)->toHtml() ?>, 'orders')"
            class="w-10 bg-cyan-400 text-white hover:bg-cyan-500"
            title="Crear factura">
            <i class="ti ti-receipt-2 text-2xl"></i>
          </button>

          <button onclick="window.alpineOrdersInstance?.deleteOrder(<?php echo \Illuminate\Support\Js::from($item)->toHtml() ?>)"
            class="w-10 bg-cyan-400 text-white hover:bg-cyan-500"
            title="Eliminar">
            <i class="ti ti-trash text-2xl"></i>
          </button>

        </div>
        <?php endif; ?>

      </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </div>

</div>
<?php /**PATH C:\Users\USUARIO\Documents\proyecto-pos\app-pos-laravel\resources\views/livewire/admin/quick-sale/orders.blade.php ENDPATH**/ ?>