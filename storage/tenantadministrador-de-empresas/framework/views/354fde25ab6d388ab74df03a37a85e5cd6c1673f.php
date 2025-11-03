<div <?php echo e($attributes); ?> class="bg-white border border-slate-200 shadow-sm overflow-x-auto rounded-lg" style="width: 100%; max-width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch;">

    <?php if(isset($top)): ?>
        <div class="flex items-end justify-between py-1.5 px-2 bg-slate-100 border text-xs sm:text-sm md:text-tiny border-b shadow">
            <div>
                <span class="font-bold text-2xl"><?php echo e($top->attributes['title']); ?></span>
            </div>
            <div class="divide-x-2">
                <?php echo e($top); ?>

            </div>
        </div>
    <?php endif; ?>

    <?php if(isset($header)): ?>
        <div class="flex w-full items-end p-4 space-x-3">
            <?php echo e($header); ?>

        </div>
    <?php endif; ?>

    <?php echo e($slot); ?>


</div>
<?php /**PATH C:\Users\USUARIO\Documents\proyecto-pos\app-pos-laravel\resources\views/components/commons/table-responsive.blade.php ENDPATH**/ ?>