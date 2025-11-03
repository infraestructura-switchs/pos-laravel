
<<?php echo e($tag); ?> <?php if($isDisabled): echo 'disabled'; endif; ?> <?php echo e($attributes->whereDoesntStartWith('wire:target')); ?> <?php if($disabledTarget): ?> wire:target='<?php echo e($disabledTarget); ?>' <?php endif; ?> >

    <?php if($load && !$href): ?>
        <?php echo $getIconLoad($attributes->get('wire:target')); ?>

    <?php endif; ?>

    <?php if($icon): ?>
        <i class="ico icon-<?php echo e($icon); ?> mr-1" <?php echo e($load ? 'wire:loading.remove' : ''); ?> ></i>
    <?php endif; ?>

    <?php if(!$href): ?>
        <span wire:target="<?php echo e($attributes->get('wire:target')); ?>" wire:loading> <?php echo e($textLoad); ?> </span>
        <span wire:target="<?php echo e($attributes->get('wire:target')); ?>" wire:loading.remove> <?php echo e($text); ?> </span>
    <?php else: ?>
        <?php echo e($text); ?>

    <?php endif; ?>


</<?php echo e($tag); ?>>
<?php /**PATH C:\Users\USUARIO\Documents\proyecto-pos\app-pos-laravel\resources\views/components/wireui/button.blade.php ENDPATH**/ ?>