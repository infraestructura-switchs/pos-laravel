<div>
  <?php if (isset($component)) { $__componentOriginal1c97c2c41815187e0919457cda1ee0c550e97c19 = $component; } ?>
<?php $component = App\View\Components\Wireui\Modal::resolve(['maxWidth' => '4xl'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('wireui.modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Wireui\Modal::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.defer' => 'openCreate']); ?>
    <?php if (isset($component)) { $__componentOriginalbcb1b82e5f0838c22c3cae8d60511b7be93d9a1d = $component; } ?>
<?php $component = App\View\Components\Wireui\Card::resolve(['title' => 'Cierre de caja'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('wireui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Wireui\Card::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => '!pt-1']); ?>

      <?php if (isset($component)) { $__componentOriginalfc218273c6509951100fffb520610b6c38f96d07 = $component; } ?>
<?php $component = App\View\Components\Wireui\Errors::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('wireui.errors'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Wireui\Errors::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfc218273c6509951100fffb520610b6c38f96d07)): ?>
<?php $component = $__componentOriginalfc218273c6509951100fffb520610b6c38f96d07; ?>
<?php unset($__componentOriginalfc218273c6509951100fffb520610b6c38f96d07); ?>
<?php endif; ?>

      <div class="flex items-end justify-between">
        <div>
          <?php if($currentOpening): ?>
            <div class="text-sm text-green-600">
              <i class="ti ti-check-circle mr-1"></i>
              Caja abierta desde: <?php echo e($currentOpening->opened_at->format('d/m/Y H:i')); ?>

            </div>
            <div class="text-xs text-gray-600">
              Base inicial registrada: <?php echo '$ ' . number_format((float)$currentOpening->total_initial, 0, '.', ','); ?>
            </div>
          <?php else: ?>
            <div class="text-sm text-red-600">
              <i class="ti ti-alert-triangle mr-1"></i>
              No hay caja abierta
            </div>
          <?php endif; ?>
        </div>
        <div class="text-right">
          <span class="mr-1 font-semibold">Terminal: </span>
          <span class="text-sm font-semibold"><?php echo e($terminal->name); ?></span>
        </div>
      </div>

      <div class="grid grid-cols-2 gap-6 mt-4">
        <section>
          <p class="text-center font-semibold uppercase">
            Dinero recibido
          </p>

          <ul class="mt-2 divide-y-2 text-sm font-semibold">

            <li class="flex justify-between py-1.5">
              <span>
                Efectivo
              </span>
              <span class="text-right">
                <?php echo '$ ' . number_format((float)$cash, 0, '.', ','); ?>
              </span>
            </li>

            <li class="flex justify-between py-1.5">
              <span>
                Tarjeta crédito
              </span>
              <span class="text-right">
                <?php echo '$ ' . number_format((float)$credit_card, 0, '.', ','); ?>
              </span>
            </li>

            <li class="flex justify-between py-1.5">
              <span>
                Tarjeta débito
              </span>
              <span class="text-right">
                <?php echo '$ ' . number_format((float)$debit_card, 0, '.', ','); ?>
              </span>
            </li>

            <li class="flex justify-between py-1.5">
              <span>
                Transferencia
              </span>
              <span class="text-right">
                <?php echo '$ ' . number_format((float)$transfer, 0, '.', ','); ?>
              </span>
            </li>
          </ul>

          <p class="text-center font-semibold uppercase">
            Totales
          </p>

          <ul class="mt-2 divide-y-2 text-sm font-semibold">
            <li class="flex justify-between py-1.5">
              <span>
                Total propinas
              </span>
              <span class="text-right">
                <?php echo '$ ' . number_format((float)$tip, 0, '.', ','); ?>
              </span>
            </li>

            <li class="flex justify-between py-1.5 text-red-600">
              <span>Total egresos</span>
              <span><?php echo '$ ' . number_format((float)$outputs, 0, '.', ','); ?></span>
            </li>

            <li class="flex justify-between py-1.5">
              <span>
                Total ventas
              </span>
              <span class="text-right">
                <?php echo '$ ' . number_format((float)$total_sales, 0, '.', ','); ?>
              </span>
            </li>
          </ul>
        </section>

        <section>

          <li class="flex justify-between py-1 text-lg font-bold">
            <span>Dinero esperado en caja</span>
            <span><?php echo '$ ' . number_format((float)$cashRegister, 0, '.', ','); ?></span>
          </li>

          <div class="space-y-3">
            <?php if($currentOpening): ?>
              <div class="p-3 bg-green-50 border border-green-200 rounded-lg">
                <label class="block text-sm font-medium text-gray-700 mb-1">Base inicial (desde apertura)</label>
                <div class="text-lg font-semibold text-green-800">
                  <?php echo '$ ' . number_format((float)$base, 0, '.', ','); ?>
                </div>
                <p class="text-xs text-green-600 mt-1">
                  Registrada el <?php echo e($currentOpening->opened_at->format('d/m/Y H:i')); ?>

                </p>
              </div>
            <?php else: ?>
              <?php if (isset($component)) { $__componentOriginal3e7654d0fd09e29429bde203b5c0e71320d1dfd9 = $component; } ?>
<?php $component = App\View\Components\Wireui\Input::resolve(['label' => 'Base inicial','onlyNumbers' => true] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('wireui.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Wireui\Input::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.debounce.500ms' => 'base']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3e7654d0fd09e29429bde203b5c0e71320d1dfd9)): ?>
<?php $component = $__componentOriginal3e7654d0fd09e29429bde203b5c0e71320d1dfd9; ?>
<?php unset($__componentOriginal3e7654d0fd09e29429bde203b5c0e71320d1dfd9); ?>
<?php endif; ?>
            <?php endif; ?>
            
            <?php if (isset($component)) { $__componentOriginal3e7654d0fd09e29429bde203b5c0e71320d1dfd9 = $component; } ?>
<?php $component = App\View\Components\Wireui\Input::resolve(['label' => 'Dinero real en caja','onlyNumbers' => true] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('wireui.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Wireui\Input::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.defer' => 'price']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3e7654d0fd09e29429bde203b5c0e71320d1dfd9)): ?>
<?php $component = $__componentOriginal3e7654d0fd09e29429bde203b5c0e71320d1dfd9; ?>
<?php unset($__componentOriginal3e7654d0fd09e29429bde203b5c0e71320d1dfd9); ?>
<?php endif; ?>
            
            <?php if (isset($component)) { $__componentOriginal3e7654d0fd09e29429bde203b5c0e71320d1dfd9 = $component; } ?>
<?php $component = App\View\Components\Wireui\Input::resolve(['label' => 'Gastos (opcional)','onlyNumbers' => true,'prefix' => '$'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('wireui.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Wireui\Input::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.defer' => 'gastos','placeholder' => '0']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3e7654d0fd09e29429bde203b5c0e71320d1dfd9)): ?>
<?php $component = $__componentOriginal3e7654d0fd09e29429bde203b5c0e71320d1dfd9; ?>
<?php unset($__componentOriginal3e7654d0fd09e29429bde203b5c0e71320d1dfd9); ?>
<?php endif; ?>
              
            <?php if (isset($component)) { $__componentOriginala2b084c9e72264ca0e1477eae920024f794dd3d1 = $component; } ?>
<?php $component = App\View\Components\Wireui\Textarea::resolve(['label' => 'Observaciones'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('wireui.textarea'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Wireui\Textarea::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.defer' => 'observations','rows' => '3']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala2b084c9e72264ca0e1477eae920024f794dd3d1)): ?>
<?php $component = $__componentOriginala2b084c9e72264ca0e1477eae920024f794dd3d1; ?>
<?php unset($__componentOriginala2b084c9e72264ca0e1477eae920024f794dd3d1); ?>
<?php endif; ?>
          </div>
        </section>
      </div>

      <hr class="my-3 border-gray-300">

      <div class="mt-3 text-right">
        <?php if (isset($component)) { $__componentOriginalf9792ceae53793171c0cbf93396898df587beb77 = $component; } ?>
<?php $component = App\View\Components\Wireui\Button::resolve(['text' => 'Cerrar'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('wireui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Wireui\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['secondary' => true,'x-on:click' => 'show=false']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf9792ceae53793171c0cbf93396898df587beb77)): ?>
<?php $component = $__componentOriginalf9792ceae53793171c0cbf93396898df587beb77; ?>
<?php unset($__componentOriginalf9792ceae53793171c0cbf93396898df587beb77); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginalf9792ceae53793171c0cbf93396898df587beb77 = $component; } ?>
<?php $component = App\View\Components\Wireui\Button::resolve(['text' => 'Guardar','load' => true,'textLoad' => 'Guardando...'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('wireui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Wireui\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => 'store']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf9792ceae53793171c0cbf93396898df587beb77)): ?>
<?php $component = $__componentOriginalf9792ceae53793171c0cbf93396898df587beb77; ?>
<?php unset($__componentOriginalf9792ceae53793171c0cbf93396898df587beb77); ?>
<?php endif; ?>
      </div>

     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbcb1b82e5f0838c22c3cae8d60511b7be93d9a1d)): ?>
<?php $component = $__componentOriginalbcb1b82e5f0838c22c3cae8d60511b7be93d9a1d; ?>
<?php unset($__componentOriginalbcb1b82e5f0838c22c3cae8d60511b7be93d9a1d); ?>
<?php endif; ?>
   <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1c97c2c41815187e0919457cda1ee0c550e97c19)): ?>
<?php $component = $__componentOriginal1c97c2c41815187e0919457cda1ee0c550e97c19; ?>
<?php unset($__componentOriginal1c97c2c41815187e0919457cda1ee0c550e97c19); ?>
<?php endif; ?>

</div>
<?php /**PATH C:\Users\USUARIO\Documents\proyecto-pos\app-pos-laravel\resources\views/livewire/admin/cash-closing/create.blade.php ENDPATH**/ ?>