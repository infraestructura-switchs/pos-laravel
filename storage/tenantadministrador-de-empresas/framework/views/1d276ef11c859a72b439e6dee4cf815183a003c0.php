<div class="<?php if($disabled): ?> opacity-60 <?php endif; ?>">

    <?php if($label): ?>
        <?php if (isset($component)) { $__componentOriginal3bf0a20793be3eca9a779778cf74145887b021b9 = $component; } ?>
<?php $component = Illuminate\View\DynamicComponent::resolve(['component' => 'wireui.label'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('dynamic-component'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\DynamicComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'mb-1','label' => $label,'has-error' => $errors->has($name),'for' => $id]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3bf0a20793be3eca9a779778cf74145887b021b9)): ?>
<?php $component = $__componentOriginal3bf0a20793be3eca9a779778cf74145887b021b9; ?>
<?php unset($__componentOriginal3bf0a20793be3eca9a779778cf74145887b021b9); ?>
<?php endif; ?>
    <?php endif; ?>

    <select <?php echo e($attributes->class([ $defaultClasses(),
        $errorClasses() =>  $errors->has($name),
        $colorClasses() => !$errors->has($name),
    ])); ?>>

        <?php if($placeholder): ?>
            <option value=""><?php echo e($placeholder); ?></option>
        <?php endif; ?>

        <?php if($options->isNotEmpty()): ?>

            <?php $__empty_1 = true; $__currentLoopData = $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <option value="<?php echo e($getOptionValue($key, $option)); ?>"
                    <?php if(data_get($option, 'disabled', false)): ?> disabled <?php endif; ?>>
                    <?php echo e($getOptionLabel($option)); ?>

                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <option disabled>
                    <?php echo app('translator')->get('wireui::messages.empty_options'); ?>
                </option>
            <?php endif; ?>
        <?php else: ?> <?php echo e($slot); ?> <?php endif; ?>
    </select>

    <?php if($hint): ?>
        <label <?php if($id): ?> for="<?php echo e($id); ?>" <?php endif; ?> class="mt-2 text-sm text-slate-500 dark:text-slate-400">
            <?php echo e($hint); ?>

        </label>
    <?php endif; ?>
</div>
<?php /**PATH C:\Users\USUARIO\Documents\proyecto-pos\app-pos-laravel\resources\views/components/wireui/native-select.blade.php ENDPATH**/ ?>