<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps(['label', 'value', 'tooltip']) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps(['label', 'value', 'tooltip']); ?>
<?php foreach (array_filter((['label', 'value', 'tooltip']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>
<div class="inline-flex flex-col text-center px-2 select-none cursor-pointer text-slate-600 hover:text-indigo-600" title="<?php echo e($tooltip); ?>">
    <span class="leading-4 font-semibold">
        <?php echo e($label); ?>

    </span>
    <span class="leading-4">
        <?php echo e($value); ?>

    </span>
</div><?php /**PATH C:\Users\USUARIO\Documents\proyecto-pos\app-pos-laravel\resources\views/components/commons/tag.blade.php ENDPATH**/ ?>