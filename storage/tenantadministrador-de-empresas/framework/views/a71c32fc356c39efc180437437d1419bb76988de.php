<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps(['route', 'name', 'icon', 'active']) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps(['route', 'name', 'icon', 'active']); ?>
<?php foreach (array_filter((['route', 'name', 'icon', 'active']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<?php
    $class = $active ? 'text-cyan-400 bg-gray-100 rounded-md shadow-inner shadow-slate-300' :
                        'text-gray-600'
?>

<div class="flex flex-col items-center justify-end cursor-pointer <?php echo e($class); ?>" title="<?php echo e($name); ?>">

    <a href="<?php echo e($route); ?>" class="inline-flex items-center font-medium w-full px-2 h-8 lg:h-9 text-sm lg:text-base" title="<?php echo e($name); ?>">
        <i class="ico icon-<?php echo e($icon); ?> text-base lg:text-lg"></i>
        <?php if(!request()->routeIs('admin.quick-sales.create') && !request()->routeIs('admin.direct-sale.create')): ?>
            <span class="whitespace-nowrap text-xs lg:text-sm ml-3 lg:ml-4"> <?php echo e($name); ?> </span>
        <?php endif; ?>
    </a>

</div>
<?php /**PATH C:\Users\USUARIO\Documents\proyecto-pos\app-pos-laravel\resources\views/components/menu/nav-link.blade.php ENDPATH**/ ?>