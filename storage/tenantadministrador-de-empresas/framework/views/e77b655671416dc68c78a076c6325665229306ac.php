<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps(['label'=>'', 'active' => 'activado', 'inactive' => 'desactivado', 'width' => null]) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps(['label'=>'', 'active' => 'activado', 'inactive' => 'desactivado', 'width' => null]); ?>
<?php foreach (array_filter((['label'=>'', 'active' => 'activado', 'inactive' => 'desactivado', 'width' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>
<div>
    <?php if($label): ?>
        <label class="font-semibold text-sm"><?php echo e($label); ?></label>
    <?php endif; ?>
    <div x-data="{ status: <?php if ((object) ($attributes->wire('model')) instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($_instance->id); ?>').entangle('<?php echo e($attributes->wire('model')->value()); ?>')<?php echo e($attributes->wire('model')->hasModifier('defer') ? '.defer' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($_instance->id); ?>').entangle('<?php echo e($attributes->wire('model')); ?>')<?php endif; ?>,
                text:'',
                textActive: '<?php echo e($active); ?>',
                textInactive: '<?php echo e($inactive); ?>',
                changeState(){
                    if(this.status == '1'){
                        this.status='0';
                    }else{
                        this.status='1';
                    }
                }
            }"
        x-init="text = status == '0' ? textActive : textInactive; $watch('status', value => text = value == '0' ? textActive : textInactive)"
        <?php echo e($attributes->merge(['class' => 'flex items-center'])); ?>>
        <div x-on:click="changeState()"
            :class="status=='0' ? 'bg-blue-600' : 'bg-gray-400' "
            class="w-8 sm:w-12 h-4 sm:h-5 rounded-full flex items-center px-1 cursor-pointer transition-colors duration-300">
            <span class="h-3 w-3 sm:h-4 sm:w-4 bg-white rounded-full duration-300 transform " :class="status=='0' ? 'translate-x-3.5 sm:translate-x-6' : '' "></span>
        </div>
        <span x-text="text" class="text-xs sm:text-sm font-semibold ml-1 <?php echo e($width ? $width : ''); ?> "></span>
    </div>
</div>
<?php /**PATH C:\Users\USUARIO\Documents\proyecto-pos\app-pos-laravel\resources\views/components/buttons/switch.blade.php ENDPATH**/ ?>