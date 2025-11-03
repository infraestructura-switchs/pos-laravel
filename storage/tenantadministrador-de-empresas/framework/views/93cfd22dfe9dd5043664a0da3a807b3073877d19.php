<div>
    <?php if (isset($component)) { $__componentOriginal1c97c2c41815187e0919457cda1ee0c550e97c19 = $component; } ?>
<?php $component = App\View\Components\Wireui\Modal::resolve(['maxWidth' => 'md'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('wireui.modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Wireui\Modal::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.defer' => 'openPresentations']); ?>
        <?php if (isset($component)) { $__componentOriginalbcb1b82e5f0838c22c3cae8d60511b7be93d9a1d = $component; } ?>
<?php $component = App\View\Components\Wireui\Card::resolve(['title' => 'Crear presentación'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('wireui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Wireui\Card::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
            <div class="p-5">
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
                <div class="space-y-4">
                    <?php if (isset($component)) { $__componentOriginal3e7654d0fd09e29429bde203b5c0e71320d1dfd9 = $component; } ?>
<?php $component = App\View\Components\Wireui\Input::resolve(['label' => 'Nombre'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('wireui.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Wireui\Input::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.defer' => 'name']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3e7654d0fd09e29429bde203b5c0e71320d1dfd9)): ?>
<?php $component = $__componentOriginal3e7654d0fd09e29429bde203b5c0e71320d1dfd9; ?>
<?php unset($__componentOriginal3e7654d0fd09e29429bde203b5c0e71320d1dfd9); ?>
<?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginal3e7654d0fd09e29429bde203b5c0e71320d1dfd9 = $component; } ?>
<?php $component = App\View\Components\Wireui\Input::resolve(['label' => 'Cantidad','onlyNumbers' => true] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('wireui.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Wireui\Input::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.defer' => 'quantity']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3e7654d0fd09e29429bde203b5c0e71320d1dfd9)): ?>
<?php $component = $__componentOriginal3e7654d0fd09e29429bde203b5c0e71320d1dfd9; ?>
<?php unset($__componentOriginal3e7654d0fd09e29429bde203b5c0e71320d1dfd9); ?>
<?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginal3e7654d0fd09e29429bde203b5c0e71320d1dfd9 = $component; } ?>
<?php $component = App\View\Components\Wireui\Input::resolve(['label' => 'Precio'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
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
                </div>
            </div>

             <?php $__env->slot('footer', null, []); ?> 
                <div class="text-right">
                    <?php if (isset($component)) { $__componentOriginalf9792ceae53793171c0cbf93396898df587beb77 = $component; } ?>
<?php $component = App\View\Components\Wireui\Button::resolve(['text' => 'Cancelar'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
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
<?php $component = App\View\Components\Wireui\Button::resolve(['text' => 'Agregar','load' => true,'textLoad' => 'Agregando'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('wireui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Wireui\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => 'addPresentation']); ?>
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
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1c97c2c41815187e0919457cda1ee0c550e97c19)): ?>
<?php $component = $__componentOriginal1c97c2c41815187e0919457cda1ee0c550e97c19; ?>
<?php unset($__componentOriginal1c97c2c41815187e0919457cda1ee0c550e97c19); ?>
<?php endif; ?>
</div>
<?php /**PATH C:\Users\USUARIO\Documents\proyecto-pos\app-pos-laravel\resources\views/livewire/admin/products/presentations.blade.php ENDPATH**/ ?>