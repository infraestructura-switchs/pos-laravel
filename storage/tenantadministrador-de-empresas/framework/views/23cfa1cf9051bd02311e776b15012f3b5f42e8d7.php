<?php
    $hasError = false;
    if ($name) { $hasError = $errors->has($name); }
?>

<div class="<?php if($disabled): ?> opacity-60 <?php endif; ?>">

    <?php if($label): ?>
        <div class="flex mb-1">
            <?php if (isset($component)) { $__componentOriginal3bf0a20793be3eca9a779778cf74145887b021b9 = $component; } ?>
<?php $component = Illuminate\View\DynamicComponent::resolve(['component' => 'wireui.label'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('dynamic-component'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\DynamicComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => $label,'has-error' => $hasError,'for' => $id]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3bf0a20793be3eca9a779778cf74145887b021b9)): ?>
<?php $component = $__componentOriginal3bf0a20793be3eca9a779778cf74145887b021b9; ?>
<?php unset($__componentOriginal3bf0a20793be3eca9a779778cf74145887b021b9); ?>
<?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="relative rounded-md shadow-sm">

        <textarea <?php echo e($attributes->class([ $getInputClasses($hasError)])->merge(['autocomplete' => 'off', 'rows'=> 4])); ?>><?php echo e($slot); ?></textarea>

        <?php if(($hasError)): ?>
            <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none
                <?php echo e($hasError ? 'text-negative-500' : 'text-secondary-400'); ?>">
                <?php if($hasError): ?>
                    <i class="ico icon-error"></i>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH C:\Users\USUARIO\Documents\proyecto-pos\app-pos-laravel\resources\views/components/wireui/textarea.blade.php ENDPATH**/ ?>