<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps(['text']) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps(['text']); ?>
<?php foreach (array_filter((['text']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<div <?php echo e($attributes->merge(['class' => 'absolute inset-0'])); ?> >
    <div class="bg-white bg-opacity-60 flex items-center justify-center w-full h-full relative z-40">
        <div class="flex flex-col items-center">
            <i class="ico icon-spinner text-blue-600 text-5xl animate-spin"></i>
            <span class="font-bold text-slate-600 text-lg mt-3"><?php echo e($text); ?></span>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\USUARIO\Documents\proyecto-pos\app-pos-laravel\resources\views/components/loads/panel.blade.php ENDPATH**/ ?>