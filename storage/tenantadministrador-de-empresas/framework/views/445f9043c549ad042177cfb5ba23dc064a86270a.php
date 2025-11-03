<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps(['status', 'active' => 'Activo', 'inactive' => 'Inactivo']) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps(['status', 'active' => 'Activo', 'inactive' => 'Inactivo']); ?>
<?php foreach (array_filter((['status', 'active' => 'Activo', 'inactive' => 'Inactivo']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<?php if($status): ?>
    <span class="hidden text-white px-2 bg-red-600 rounded-full text-xs md:inline-flex"><?php echo e($inactive); ?></span>
    <span class="w-3 h-3 bg-red-600 inline-block rounded-full md:hidden"></span>
<?php else: ?>
    <span class="hidden text-white px-2 bg-green-600 rounded-full text-xs md:inline-flex"><?php echo e($active); ?></span>
    <span class="w-3 h-3 bg-green-600 inline-block rounded-full md:hidden"></span>
<?php endif; ?>
<?php /**PATH C:\Users\USUARIO\Documents\proyecto-pos\app-pos-laravel\resources\views/components/commons/status.blade.php ENDPATH**/ ?>