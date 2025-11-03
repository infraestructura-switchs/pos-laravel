<?php
  $sessionConfig = session('config');
  $defaultCustomer = App\Models\Customer::default()->first();
  
  $config = [
      'customer' => $defaultCustomer ? $defaultCustomer->toArray() : null,
      'change' => $sessionConfig ? ($sessionConfig->change == '0') : false,
      'print' => $sessionConfig ? ($sessionConfig->print == '0') : false,
      'width_ticket' => $sessionConfig ? $sessionConfig->width_ticket : 80,
      'format_percentage_tip' => $sessionConfig ? $sessionConfig->format_percentage_tip : 0,
      'percentage_tip' => $sessionConfig ? $sessionConfig->percentage_tip : 0,
  ];
?>

<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

  <?php if(isset($title)): ?>
    <title><?php echo e($title); ?></title>
  <?php else: ?>
    <title><?php echo $__env->yieldContent('title'); ?></title>
  <?php endif; ?>

  <!-- Fonts and styles -->
  <link rel="stylesheet" href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap">
  <link rel="stylesheet" href="<?php echo e(url('vendor/icomoon-v1.0/style.css')); ?>?v9">
  
  <!-- International Telephone Input -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/css/intlTelInput.css">
  
  <style id="page-rule">
    @page {
      size: 80mm 178mm;
      margin: 0cm;
    }
  </style>

  <!-- Scripts -->
  <script src="<?php echo e(url('ts/app.js')); ?>" defer></script>

  
  <link rel="stylesheet" href="<?php echo e(url('build/assets/app-fd737ff0.css')); ?>">
  <script src="<?php echo e(url('build/assets/app-f737933f.js')); ?>" defer></script>

  <?php echo \Livewire\Livewire::styles(); ?>


</head>

<body class="antialiased scroll-smooth font-manrope">

  <div class="min-h-screen bg-gray-100 no-print text-slate-800">

    <div x-data x-init='$store.config.set(<?php echo e(json_encode($config)); ?>);'></div>

    <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.loads.alpine','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('loads.alpine'); ?>
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

    <?php
if (! isset($_instance)) {
    $html = \Livewire\Livewire::mount('admin.menu', [])->html();
} elseif ($_instance->childHasBeenRendered('6gL2ucZ')) {
    $componentId = $_instance->getRenderedChildComponentId('6gL2ucZ');
    $componentTag = $_instance->getRenderedChildComponentTagName('6gL2ucZ');
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild('6gL2ucZ');
} else {
    $response = \Livewire\Livewire::mount('admin.menu', []);
    $html = $response->html();
    $_instance->logRenderedChild('6gL2ucZ', $response->id(), \Livewire\Livewire::getRootElementTagName($html));
}
echo $html;
?>

    <?php
if (! isset($_instance)) {
    $html = \Livewire\Livewire::mount('admin.cash-opening.create', [])->html();
} elseif ($_instance->childHasBeenRendered('ZhF1ptY')) {
    $componentId = $_instance->getRenderedChildComponentId('ZhF1ptY');
    $componentTag = $_instance->getRenderedChildComponentTagName('ZhF1ptY');
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild('ZhF1ptY');
} else {
    $response = \Livewire\Livewire::mount('admin.cash-opening.create', []);
    $html = $response->html();
    $_instance->logRenderedChild('ZhF1ptY', $response->id(), \Livewire\Livewire::getRootElementTagName($html));
}
echo $html;
?>

      <main class="pt-12 md:pt-14 min-h-screen <?php echo e(request()->routeIs('admin.quick-sales.create') || request()->routeIs('admin.direct-sale.create') ? 'md:pl-14' : 'md:pl-52 lg:pl-60'); ?>">
        <?php echo e($slot); ?>

      </main>

  </div>

  <?php echo $__env->make('pdfs.ticket-open-cash-register', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
  <?php echo $__env->make('partials.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

  <?php echo $__env->yieldPushContent('html'); ?>

  <?php echo $__env->yieldPushContent('js'); ?>

  <!-- International Telephone Input JS -->
  <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/intlTelInput.min.js"></script>

  <?php echo \Livewire\Livewire::scripts(); ?>

</body>

</html>
<?php /**PATH C:\Users\USUARIO\Documents\proyecto-pos\app-pos-laravel\resources\views/layouts/app.blade.php ENDPATH**/ ?>