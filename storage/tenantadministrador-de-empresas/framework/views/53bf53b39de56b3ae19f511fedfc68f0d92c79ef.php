<div x-data="alpinePresentations()">
  <div x-show="show_presentations"
    @click.self="show_presentations = false"
    class="fixed inset-0 z-30 flex items-center justify-center bg-slate-800 bg-opacity-40">

    <?php if (isset($component)) { $__componentOriginalbcb1b82e5f0838c22c3cae8d60511b7be93d9a1d = $component; } ?>
<?php $component = App\View\Components\Wireui\Card::resolve(['title' => 'Presentaciones','cardClasses' => 'max-w-lg','padding' => 'p-0'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('wireui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Wireui\Card::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
      <div>
        <ul class="divide-y rounded bg-white">
          <template x-for="(item, index) in presentations">
            <li class="cursor-pointer px-4 py-2 text-center font-semibold text-slate-800 hover:bg-slate-100"
              x-on:click="setPresentation(item)">
              <span x-text="item.name"></span>
              -
              <span x-text="formatToCop(item.price)"
                class="text-green-600"></span>
            </li>
          </template>
        </ul>
      </div>

       <?php $__env->slot('footer', null, []); ?> 

        <div class="flex justify-end">
          <?php if (isset($component)) { $__componentOriginalf9792ceae53793171c0cbf93396898df587beb77 = $component; } ?>
<?php $component = App\View\Components\Wireui\Button::resolve(['text' => 'Cerrar'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('wireui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Wireui\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['x-on:click' => 'show_presentations=false','secondary' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf9792ceae53793171c0cbf93396898df587beb77)): ?>
<?php $component = $__componentOriginalf9792ceae53793171c0cbf93396898df587beb77; ?>
<?php unset($__componentOriginalf9792ceae53793171c0cbf93396898df587beb77); ?>
<?php endif; ?>
        </div>

       <?php $__env->endSlot(); ?>

     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbcb1b82e5f0838c22c3cae8d60511b7be93d9a1d)): ?>
<?php $component = $__componentOriginalbcb1b82e5f0838c22c3cae8d60511b7be93d9a1d; ?>
<?php unset($__componentOriginalbcb1b82e5f0838c22c3cae8d60511b7be93d9a1d); ?>
<?php endif; ?>

  </div>
</div>
<?php /**PATH C:\Users\USUARIO\Documents\proyecto-pos\app-pos-laravel\resources\views/livewire/admin/quick-sale/presentations.blade.php ENDPATH**/ ?>