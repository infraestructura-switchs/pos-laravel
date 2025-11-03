<div class="px-2 pt-2 pb-10">
  <?php if($this->isDomainEnabled()): ?>

  <div class="flex justify-end items-end gap-x-4">

    <div class="flex gap-x-4">
      <?php if($filterDate === 8): ?>
      <?php if (isset($component)) { $__componentOriginal3e7654d0fd09e29429bde203b5c0e71320d1dfd9 = $component; } ?>
<?php $component = App\View\Components\Wireui\Input::resolve(['label' => 'Desde'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('wireui.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Wireui\Input::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'startDate','type' => 'date','onkeydown' => 'return false']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3e7654d0fd09e29429bde203b5c0e71320d1dfd9)): ?>
<?php $component = $__componentOriginal3e7654d0fd09e29429bde203b5c0e71320d1dfd9; ?>
<?php unset($__componentOriginal3e7654d0fd09e29429bde203b5c0e71320d1dfd9); ?>
<?php endif; ?>
      <?php if (isset($component)) { $__componentOriginal3e7654d0fd09e29429bde203b5c0e71320d1dfd9 = $component; } ?>
<?php $component = App\View\Components\Wireui\Input::resolve(['label' => 'Hasta'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('wireui.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Wireui\Input::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'endDate','type' => 'date','onkeydown' => 'return false']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3e7654d0fd09e29429bde203b5c0e71320d1dfd9)): ?>
<?php $component = $__componentOriginal3e7654d0fd09e29429bde203b5c0e71320d1dfd9; ?>
<?php unset($__componentOriginal3e7654d0fd09e29429bde203b5c0e71320d1dfd9); ?>
<?php endif; ?>
      <?php endif; ?>
    </div>

    <div>
      <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.wireui.range-date','data' => ['wire:model' => 'filterDate']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('wireui.range-date'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'filterDate']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
    </div>
  </div>

  <p class="text-center text-xl text-red-600 font-semibold">Importante</p>

  <h1 class="text-center font-semibold">
    Los datos presentados en esta vista se están calculando basándose en las ventas realizadas a partir de 31 de julio
    de 2024
  </h1>

  <div class="flex flex-wrap justify-center gap-8 mt-10">

    <div class="w-80 h-36 flex flex-col items-center justify-center border rounded-2xl bg-indigo-700/90 text-white">
      <i class="ico icon-money text-7xl text-indigo-800"></i>
      <span class="-mt-3">Costos</span>
      <span class="font-semibold text-2xl"><?php echo '$ ' . number_format((float)$costTotal, 0, '.', ','); ?></span>
    </div>

    <div class="w-80 h-36 flex flex-col items-center justify-center border rounded-2xl bg-blue-700/90 text-white">
      <i class="ico icon-money text-7xl text-blue-800"></i>
      <span class="-mt-3">Ventas</span>
      <span class="font-semibold text-2xl"><?php echo '$ ' . number_format((float)$saleTotal, 0, '.', ','); ?></span>
    </div>

  </div>
  <?php endif; ?>
</div>
<?php /**PATH C:\Users\USUARIO\Documents\proyecto-pos\app-pos-laravel\resources\views/livewire/dashboard.blade.php ENDPATH**/ ?>