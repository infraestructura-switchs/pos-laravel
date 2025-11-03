<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps(['title' => 'Editar']) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps(['title' => 'Editar']); ?>
<?php foreach (array_filter((['title' => 'Editar']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<button <?php echo e($attributes); ?> title="<?php echo e($title); ?>">
    <i class="ico icon-edit text-blue-600 text-xl"></i>
</button>
<?php /**PATH C:\Users\USUARIO\Documents\proyecto-pos\app-pos-laravel\resources\views/components/buttons/edit.blade.php ENDPATH**/ ?>