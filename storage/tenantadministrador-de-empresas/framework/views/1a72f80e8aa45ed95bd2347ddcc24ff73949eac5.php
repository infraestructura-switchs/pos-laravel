<div class="<?php echo e($cardClasses); ?>">

    <?php if($header): ?>
        <?php echo e($header); ?>

    <?php elseif($title || $action): ?>
        <div class="<?php echo e($headerClasses); ?>">
            <h3 class="font-semibold whitespace-normal text-lg"><?php echo e($title); ?></h3>
            <?php if($close): ?>
                <a x-on:click="show=false" class="focus:outline-none text-gray-700 font-semibold text-4xl leading-none cursor-pointer select-none">&times;</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div <?php echo e($attributes->merge(['class' => "{$padding} grow"])); ?>>
        <?php echo e($slot); ?>

    </div>

    <?php if($footer): ?>
        <div class="<?php echo e($footerClasses); ?>">
            <?php echo e($footer); ?>

        </div>
    <?php endif; ?>
</div>
<?php /**PATH C:\Users\USUARIO\Documents\proyecto-pos\app-pos-laravel\resources\views/components/wireui/card.blade.php ENDPATH**/ ?>