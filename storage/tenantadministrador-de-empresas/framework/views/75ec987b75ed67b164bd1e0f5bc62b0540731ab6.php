<label <?php echo e($attributes->class([
    'block text-sm font-semibold',
    'text-red-600'  => $hasError,
    'opacity-60'         => $attributes->get('disabled'),
    'text-gray-700' => !$hasError,
])); ?>>
<?php echo e($label ?? $slot); ?>

</label>
<?php /**PATH C:\Users\USUARIO\Documents\proyecto-pos\app-pos-laravel\resources\views/components/wireui/label.blade.php ENDPATH**/ ?>