<?php if($hasErrors($errors)): ?>
    <div <?php echo e($attributes->merge(['class' => 'rounded-lg bg-red-50 p-4 mt-2'])); ?>>
        <div class="flex items-center pb-3 border-b-2 border-red-200 ">

            <i class="ico icon-error mr-3 text-red-500 text-lg"></i>

            <span class="text-sm font-semibold text-red-800">
                <?php echo e(str_replace('{errors}', $count($errors), $title($errors))); ?>

            </span>
        </div>

        <div class="ml-5 pl-1 mt-2">
            <ul class="list-disc space-y-1 text-sm text-red-700">
                <?php $__currentLoopData = $getErrorMessages($errors); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e(head($message)); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    </div>
<?php else: ?>
    <div class="hidden"></div>
<?php endif; ?>
<?php /**PATH C:\Users\USUARIO\Documents\proyecto-pos\app-pos-laravel\resources\views/components/wireui/errors.blade.php ENDPATH**/ ?>