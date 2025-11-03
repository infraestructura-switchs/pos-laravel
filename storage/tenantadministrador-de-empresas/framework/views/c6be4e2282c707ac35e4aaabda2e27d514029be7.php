<?php if (isset($component)) { $__componentOriginal8e2ce59650f81721f93fef32250174d77c3531da = $component; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\AppLayout::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>

  <div x-data="{ toggleView: false, order: {} }"
    x-init="
      $watch('toggleView', value => { 
        if (!value) order = {} 
      })
    "
    @toggle-view.window="toggleView=$event.detail"
    @current-order.window="order=$event.detail"
    class="pb-10">

    <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.loads.panel-fixed','data' => ['id' => 'load-panel','text' => 'Cargando...','class' => 'z-[999] hidden']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('loads.panel-fixed'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'load-panel','text' => 'Cargando...','class' => 'z-[999] hidden']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>

    <?php
if (! isset($_instance)) {
    $html = \Livewire\Livewire::mount('admin.quick-sale.customers', [])->html();
} elseif ($_instance->childHasBeenRendered('L1dUhk2')) {
    $componentId = $_instance->getRenderedChildComponentId('L1dUhk2');
    $componentTag = $_instance->getRenderedChildComponentTagName('L1dUhk2');
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild('L1dUhk2');
} else {
    $response = \Livewire\Livewire::mount('admin.quick-sale.customers', []);
    $html = $response->html();
    $_instance->logRenderedChild('L1dUhk2', $response->id(), \Livewire\Livewire::getRootElementTagName($html));
}
echo $html;
?>

    <?php
if (! isset($_instance)) {
    $html = \Livewire\Livewire::mount('admin.customers.create', [])->html();
} elseif ($_instance->childHasBeenRendered('I9lujEG')) {
    $componentId = $_instance->getRenderedChildComponentId('I9lujEG');
    $componentTag = $_instance->getRenderedChildComponentTagName('I9lujEG');
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild('I9lujEG');
} else {
    $response = \Livewire\Livewire::mount('admin.customers.create', []);
    $html = $response->html();
    $_instance->logRenderedChild('I9lujEG', $response->id(), \Livewire\Livewire::getRootElementTagName($html));
}
echo $html;
?>

    <?php
if (! isset($_instance)) {
    $html = \Livewire\Livewire::mount('admin.quick-sale.change', [])->html();
} elseif ($_instance->childHasBeenRendered('E1iT6qF')) {
    $componentId = $_instance->getRenderedChildComponentId('E1iT6qF');
    $componentTag = $_instance->getRenderedChildComponentTagName('E1iT6qF');
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild('E1iT6qF');
} else {
    $response = \Livewire\Livewire::mount('admin.quick-sale.change', []);
    $html = $response->html();
    $_instance->logRenderedChild('E1iT6qF', $response->id(), \Livewire\Livewire::getRootElementTagName($html));
}
echo $html;
?>

    <?php echo $__env->make('livewire.admin.quick-sale.modal-tables', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php echo $__env->make('pdfs.ticket-bill', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php echo $__env->make('pdfs.ticket-pre-bill', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php echo $__env->make('pdfs.ticket-command-bill', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

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
      <?php
if (! isset($_instance)) {
    $html = \Livewire\Livewire::mount('admin.quick-sale.orders', [])->html();
} elseif ($_instance->childHasBeenRendered('Y75DCCf')) {
    $componentId = $_instance->getRenderedChildComponentId('Y75DCCf');
    $componentTag = $_instance->getRenderedChildComponentTagName('Y75DCCf');
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild('Y75DCCf');
} else {
    $response = \Livewire\Livewire::mount('admin.quick-sale.orders', []);
    $html = $response->html();
    $_instance->logRenderedChild('Y75DCCf', $response->id(), \Livewire\Livewire::getRootElementTagName($html));
}
echo $html;
?>
    </div>

    <div x-show="toggleView"
      class="flex flex-col lg:flex-row space-y-4 lg:space-y-0 lg:space-x-4">

      <div class="w-full lg:w-3/5 xl:w-2/3">
        <?php
if (! isset($_instance)) {
    $html = \Livewire\Livewire::mount('admin.quick-sale.products', [])->html();
} elseif ($_instance->childHasBeenRendered('Ev8sY4d')) {
    $componentId = $_instance->getRenderedChildComponentId('Ev8sY4d');
    $componentTag = $_instance->getRenderedChildComponentTagName('Ev8sY4d');
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild('Ev8sY4d');
} else {
    $response = \Livewire\Livewire::mount('admin.quick-sale.products', []);
    $html = $response->html();
    $_instance->logRenderedChild('Ev8sY4d', $response->id(), \Livewire\Livewire::getRootElementTagName($html));
}
echo $html;
?>
        <?php echo $__env->make('livewire.admin.quick-sale.presentations', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
      </div>

      <div class="w-full lg:w-2/5 xl:w-1/3">
        <?php echo $__env->make('livewire.admin.quick-sale.cart', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
      </div>

    </div>
  </div>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8e2ce59650f81721f93fef32250174d77c3531da)): ?>
<?php $component = $__componentOriginal8e2ce59650f81721f93fef32250174d77c3531da; ?>
<?php unset($__componentOriginal8e2ce59650f81721f93fef32250174d77c3531da); ?>
<?php endif; ?>
<?php /**PATH C:\Users\USUARIO\Documents\proyecto-pos\app-pos-laravel\resources\views/livewire/admin/quick-sale/Index.blade.php ENDPATH**/ ?>