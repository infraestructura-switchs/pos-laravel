<?php extract(collect($attributes->getAttributes())->mapWithKeys(function ($value, $key) { return [Illuminate\Support\Str::camel(str_replace([':', '.'], ' ', $key)) => $value]; })->all(), EXTR_SKIP); ?>
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps(['label','hasError']) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps(['label','hasError']); ?>
<?php foreach (array_filter((['label','hasError']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>
<?php if (isset($component)) { $__componentOriginal8ef94ff787493ae52df925e31046b6eeb5dc2d7a = $component; } ?>
<?php $component = App\View\Components\Wireui\Label::resolve(['label' => $label,'hasError' => $hasError] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('wireui.label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Wireui\Label::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['attributes' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($attributes)]); ?>

<?php echo e($slot ?? ""); ?>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8ef94ff787493ae52df925e31046b6eeb5dc2d7a)): ?>
<?php $component = $__componentOriginal8ef94ff787493ae52df925e31046b6eeb5dc2d7a; ?>
<?php unset($__componentOriginal8ef94ff787493ae52df925e31046b6eeb5dc2d7a); ?>
<?php endif; ?><?php /**PATH C:\Users\USUARIO\Documents\proyecto-pos\app-pos-laravel\storage\tenantadministrador-de-empresas\framework\views/552286a0822f22f057bd707eb7154173c253264f.blade.php ENDPATH**/ ?>