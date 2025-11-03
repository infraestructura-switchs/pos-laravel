<div class="container">

    <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.commons.header','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('commons.header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
        <?php if (isset($component)) { $__componentOriginalf9792ceae53793171c0cbf93396898df587beb77 = $component; } ?>
<?php $component = App\View\Components\Wireui\Button::resolve(['icon' => 'excel','text' => 'Exportar a excel','load' => true,'textLoad' => 'Exportando...'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('wireui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Wireui\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => 'exportProducts','success' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf9792ceae53793171c0cbf93396898df587beb77)): ?>
<?php $component = $__componentOriginalf9792ceae53793171c0cbf93396898df587beb77; ?>
<?php unset($__componentOriginalf9792ceae53793171c0cbf93396898df587beb77); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginalf9792ceae53793171c0cbf93396898df587beb77 = $component; } ?>
<?php $component = App\View\Components\Wireui\Button::resolve(['icon' => 'excel','text' => 'Importar desde excel'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('wireui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Wireui\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['x-on:click' => '$wire.emitTo(\'admin.products.import\', \'openImport\')','success' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf9792ceae53793171c0cbf93396898df587beb77)): ?>
<?php $component = $__componentOriginalf9792ceae53793171c0cbf93396898df587beb77; ?>
<?php unset($__componentOriginalf9792ceae53793171c0cbf93396898df587beb77); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginalf9792ceae53793171c0cbf93396898df587beb77 = $component; } ?>
<?php $component = App\View\Components\Wireui\Button::resolve(['icon' => 'inventory','text' => 'Crear producto'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('wireui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Wireui\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['x-on:click' => '$wire.emitTo(\'admin.products.create\', \'openCreate\')']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf9792ceae53793171c0cbf93396898df587beb77)): ?>
<?php $component = $__componentOriginalf9792ceae53793171c0cbf93396898df587beb77; ?>
<?php unset($__componentOriginalf9792ceae53793171c0cbf93396898df587beb77); ?>
<?php endif; ?>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>

    <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.commons.table-responsive','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('commons.table-responsive'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>

         <?php $__env->slot('top', null, ['title' => 'Productos']); ?> 
            <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.commons.tag','data' => ['tooltip' => 'Costo del inventario','label' => 'Costos','value' => formatToCop($totalCost)]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('commons.tag'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['tooltip' => 'Costo del inventario','label' => 'Costos','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(formatToCop($totalCost))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
         <?php $__env->endSlot(); ?>

         <?php $__env->slot('header', null, []); ?> 
            <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.wireui.search','data' => ['placeholder' => 'Buscar...']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('wireui.search'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['placeholder' => 'Buscar...']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginal535e4e8f1d05b534d9bf9b717342a6e365682ac1 = $component; } ?>
<?php $component = App\View\Components\Wireui\NativeSelect::resolve(['optionKeyValue' => 'true','options' => $filters] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('wireui.native-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Wireui\NativeSelect::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.defer' => 'filter']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal535e4e8f1d05b534d9bf9b717342a6e365682ac1)): ?>
<?php $component = $__componentOriginal535e4e8f1d05b534d9bf9b717342a6e365682ac1; ?>
<?php unset($__componentOriginal535e4e8f1d05b534d9bf9b717342a6e365682ac1); ?>
<?php endif; ?>
         <?php $__env->endSlot(); ?>

        <table class="table">
            <thead >
                <tr>
                    <th>
                        Imagen
                    </th>
                    <th left>
                        Código barras
                    </th>
                    <th left>
                        Referencia
                    </th>
                    <th left>
                        Nombre
                    </th>
                    <th left>
                        Categoría
                    </th>
                    <th>
                        Impuestos(%)
                    </th>
                    <th left>
                        Costo
                    </th>
                    <th left>
                        Precio
                    </th>
                    <th>
                        Stock
                    </th>
                    <th>
                        Estado
                    </th>
                    <th>
                        Acciones
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr wire:key="product-<?php echo e($item->id); ?>">
                        <td class="w-16">
                            <img src="<?php echo e($item->image_url); ?>" 
                                 alt="<?php echo e($item->name); ?>" 
                                 class="h-12 w-12 object-cover rounded-lg">
                        </td>
                        <td left class="<?php echo e(!$item->top ? 'text-green-500 font-bold' : ''); ?>">
                            <?php echo e($item->barcode); ?>

                        </td>
                        <td left class="<?php echo e(!$item->top ? 'text-green-500 font-bold' : ''); ?>">
                            <?php echo e($item->reference); ?>

                        </td>
                        <td left>
                            <?php echo e($item->name); ?>

                        </td>
                        <td left>
                            <?php echo e($item->category?->name ?? '-'); ?>

                        </td>
                        <td>
                            <?php if($item->taxRates->count() > 0): ?>
                                <?php echo e($item->taxRates->pluck('format_rate')->implode(', ')); ?>

                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td left>
                            <?php echo '$ ' . number_format((float)$item->cost, 0, '.', ','); ?>
                        </td>
                        <td left>
                            <?php echo '$ ' . number_format((float)$item->price, 0, '.', ','); ?>
                        </td>
                        <td>
                            <?php echo e($item->stockUnitsLabel); ?>

                        </td>
                        <td>
                            <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.commons.status','data' => ['status' => $item->status,'inactive' => 'desactivado']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('commons.status'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item->status),'inactive' => 'desactivado']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
                        </td>
                        <td actions>
                            <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.buttons.edit','data' => ['wire:click' => '$emitTo(\'admin.products.edit\', \'openEdit\', '.e($item->id).')','title' => 'Editar']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('buttons.edit'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => '$emitTo(\'admin.products.edit\', \'openEdit\', '.e($item->id).')','title' => 'Editar']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.commons.table-empty','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('commons.table-empty'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
                <?php endif; ?>
            <tbody>
        </table>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>

    <?php if($products->hasPages()): ?>
        <div class="p-3">
            <?php echo e($products->links()); ?>

        </div>
    <?php endif; ?>

    <?php
if (! isset($_instance)) {
    $html = \Livewire\Livewire::mount('admin.products.create', [])->html();
} elseif ($_instance->childHasBeenRendered('l2300398897-0')) {
    $componentId = $_instance->getRenderedChildComponentId('l2300398897-0');
    $componentTag = $_instance->getRenderedChildComponentTagName('l2300398897-0');
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild('l2300398897-0');
} else {
    $response = \Livewire\Livewire::mount('admin.products.create', []);
    $html = $response->html();
    $_instance->logRenderedChild('l2300398897-0', $response->id(), \Livewire\Livewire::getRootElementTagName($html));
}
echo $html;
?>
    <?php
if (! isset($_instance)) {
    $html = \Livewire\Livewire::mount('admin.products.edit', [])->html();
} elseif ($_instance->childHasBeenRendered('l2300398897-1')) {
    $componentId = $_instance->getRenderedChildComponentId('l2300398897-1');
    $componentTag = $_instance->getRenderedChildComponentTagName('l2300398897-1');
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild('l2300398897-1');
} else {
    $response = \Livewire\Livewire::mount('admin.products.edit', []);
    $html = $response->html();
    $_instance->logRenderedChild('l2300398897-1', $response->id(), \Livewire\Livewire::getRootElementTagName($html));
}
echo $html;
?>
    <?php
if (! isset($_instance)) {
    $html = \Livewire\Livewire::mount('admin.products.presentations', [])->html();
} elseif ($_instance->childHasBeenRendered('l2300398897-2')) {
    $componentId = $_instance->getRenderedChildComponentId('l2300398897-2');
    $componentTag = $_instance->getRenderedChildComponentTagName('l2300398897-2');
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild('l2300398897-2');
} else {
    $response = \Livewire\Livewire::mount('admin.products.presentations', []);
    $html = $response->html();
    $_instance->logRenderedChild('l2300398897-2', $response->id(), \Livewire\Livewire::getRootElementTagName($html));
}
echo $html;
?>
    <?php
if (! isset($_instance)) {
    $html = \Livewire\Livewire::mount('admin.products.import', [])->html();
} elseif ($_instance->childHasBeenRendered('l2300398897-3')) {
    $componentId = $_instance->getRenderedChildComponentId('l2300398897-3');
    $componentTag = $_instance->getRenderedChildComponentTagName('l2300398897-3');
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild('l2300398897-3');
} else {
    $response = \Livewire\Livewire::mount('admin.products.import', []);
    $html = $response->html();
    $_instance->logRenderedChild('l2300398897-3', $response->id(), \Livewire\Livewire::getRootElementTagName($html));
}
echo $html;
?>
    <?php
if (! isset($_instance)) {
    $html = \Livewire\Livewire::mount('admin.categories.index', [])->html();
} elseif ($_instance->childHasBeenRendered('l2300398897-4')) {
    $componentId = $_instance->getRenderedChildComponentId('l2300398897-4');
    $componentTag = $_instance->getRenderedChildComponentTagName('l2300398897-4');
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild('l2300398897-4');
} else {
    $response = \Livewire\Livewire::mount('admin.categories.index', []);
    $html = $response->html();
    $_instance->logRenderedChild('l2300398897-4', $response->id(), \Livewire\Livewire::getRootElementTagName($html));
}
echo $html;
?>
    <?php
if (! isset($_instance)) {
    $html = \Livewire\Livewire::mount('admin.products.tax-rates', [])->html();
} elseif ($_instance->childHasBeenRendered('l2300398897-5')) {
    $componentId = $_instance->getRenderedChildComponentId('l2300398897-5');
    $componentTag = $_instance->getRenderedChildComponentTagName('l2300398897-5');
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild('l2300398897-5');
} else {
    $response = \Livewire\Livewire::mount('admin.products.tax-rates', []);
    $html = $response->html();
    $_instance->logRenderedChild('l2300398897-5', $response->id(), \Livewire\Livewire::getRootElementTagName($html));
}
echo $html;
?>

</div>

<?php $__env->startPush('js'); ?>

    <script>
        document.addEventListener('livewire:load', function () {

            Livewire.on('deleteProduct', id => {
                Swal.fire({
                    title: '¿Estas seguro?',
                    text: '¿Quires eliminar este producto?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, aceptar',
                    cancelButtonText: 'Cancelar',
                    showLoaderOnConfirm: true,
                    preConfirm: function() {
                        return window.livewire.find('<?php echo e($_instance->id); ?>').delete(id);
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                });
            });
        });
    </script>
<?php $__env->stopPush(); ?>


<?php /**PATH C:\Users\USUARIO\Documents\proyecto-pos\app-pos-laravel\resources\views/livewire/admin/products/index.blade.php ENDPATH**/ ?>