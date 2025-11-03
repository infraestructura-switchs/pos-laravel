<?php $model = $attributes->wire('model'); ?>

<div class="fixed inset-0 overflow-y-auto <?php echo e($zIndex); ?>"
    x-data="wireui_modal({
        model: <?php if ((object) ($attributes->wire('model')) instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($_instance->id); ?>').entangle('<?php echo e($attributes->wire('model')->value()); ?>')<?php echo e($attributes->wire('model')->hasModifier('defer') ? '.defer' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($_instance->id); ?>').entangle('<?php echo e($attributes->wire('model')); ?>')<?php endif; ?>
    })"
    x-on:keydown.escape.window="close"
    x-on:keydown.tab.prevent="$event.shiftKey || nextFocusable().focus()"
    x-on:keydown.shift.tab.prevent="previousFocusable().focus()"
    x-on:open-wireui-modal:<?php echo e(Str::kebab((string)$model)); ?>.window="show = true"
    <?php echo e($attributes->whereStartsWith(['x-on:', '@'])); ?>

    style="display: none"
    x-show="show">
    <div class="flex items-end <?php echo e($align); ?> min-h-screen justify-center w-full
                relative transform transition-all <?php echo e($spacing); ?>"
        style="min-height: -webkit-fill-available; min-height: fill-available;">
        <div class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                'fixed inset-0 bg-slate-400 bg-opacity-60',
                'transform transition-opacity',
                $blur => (bool) $blur
            ]) ?>"
            x-show="show"
            x-on:click="close"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">
        </div>

        <div class="w-full <?php echo e($maxWidth); ?> z-10"
            x-show="show"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
            <?php echo e($slot); ?>

        </div>
    </div>
</div>
<?php /**PATH C:\Users\USUARIO\Documents\proyecto-pos\app-pos-laravel\resources\views/components/wireui/modal.blade.php ENDPATH**/ ?>