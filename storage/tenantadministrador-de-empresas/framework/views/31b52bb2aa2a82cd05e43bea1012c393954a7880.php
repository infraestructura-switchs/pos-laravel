<?php
  $links = [
      [
          'name' => 'Dashboard',
          'route' => route('admin.home'),
          'active' => request()->routeIs('admin.home'),
          'icon' => 'dashboard text-xl',
          'can' => 'dashboard',
      ],
      [
          'name' => 'Usuarios',
          'route' => route('admin.users.index'),
          'active' => request()->routeIs('admin.users.index'),
          'icon' => 'user-config text-xl',
          'can' => 'usuarios',
      ],
      [
          'name' => 'Clientes',
          'route' => route('admin.customers.index'),
          'active' => request()->routeIs('admin.customers.index'),
          'icon' => 'users text-xl',
          'can' => 'clientes',
      ],
      [
          'name' => 'Proveedores',
          'route' => route('admin.providers.index'),
          'active' => request()->routeIs('admin.providers.index'),
          'icon' => 'provider text-xl',
          'can' => 'proveedores',
      ],
      [
          'name' => 'Productos',
          'route' => route('admin.products.index'),
          'active' => request()->routeIs('admin.products.index'),
          'icon' => 'inventory text-xl',
          'can' => 'productos',
      ],


      [
          'name' => 'Facturación',
          'route' => '#',
          'active' => false,
          'icon' => 'quote text-xl',
          'can' => 'facturas',
          'children' => [
            [
              'name' => 'Vender',
              'route' => route('admin.direct-sale.create'),
              'active' => request()->routeIs('admin.direct-sale.*'),
              'icon' => 'quote text-xl',
              'can' => 'vender',
            ],
            [
              'name' => 'Facturas',
              'route' => route('admin.bills.index'),
              'active' => request()->routeIs('admin.bills.*'),
              'icon' => 'quote text-xl',
              'can' => 'facturas',
            ],
            [
              'name' => 'Ventas rápidas',
              'route' => route('admin.quick-sales.create'),
              'active' => request()->routeIs('admin.quick-sales.*'),
              'icon' => 'new-order text-xl',
              'can' => 'ventas rapidas',
            ],
            [
              'name' => 'Apertura de caja',
              'route' => route('admin.cash-opening.index'),
              'active' => request()->routeIs('admin.cash-opening.*'),
              'icon' => 'payment text-xl',
              'can' => 'cierre de caja',
            ],
            [
              'name' => 'Cierre de caja',
              'route' => route('admin.cash-closing.index'),
              'active' => request()->routeIs('admin.cash-closing.*'),
              'icon' => 'payment text-xl',
              'can' => 'cierre de caja',
            ],
          ],
      ],





      [
          'name' => 'Inventario',
          'route' => '#',
          'active' => false,
          'icon' => 'inventory text-2xl',
          'can' => 'inventario',
          'children' => [
            [
          'name' => 'Bodegas',
          'route' => route('admin.warehouses.index'),
          'active' => request()->routeIs('admin.warehouses.index'),
          'icon' => 'home text-xl',
          'can' => 'bodegas',
      ],
            [
          'name' => 'Entradas/Salidas',
          'route' => route('admin.stock_movements.index'),
          'active' => request()->routeIs('admin.stock_movements.index'),
          'icon' => 'arrow-l text-xl',
          'can' => 'entrada-salidas',
      ],
      [
          'name' => 'Remisiones',
          'route' => route('admin.inventory-remissions.index'),
          'active' => request()->routeIs('admin.inventory-remissions.*'),
          'icon' => 'order-2 text-xl',
          'can' => 'inventario-remisiones',
      ],
      [
          'name' => 'Transferencias',
          'route' => route('admin.warehouse-transfers.index'),
          'active' => request()->routeIs('admin.warehouse-transfers.index'),
          'icon' => 'arrow-l text-xl',
          'can' => 'entrada-salidas',
      ],
          ],
      ],





      [
          'name' => 'Financiaciones',
          'route' => route('admin.finances.index'),
          'active' => request()->routeIs('admin.finances.*'),
          'icon' => 'collect text-xl',
          'can' => 'financiaciones',
      ],
      [
          'name' => 'Compras',
          'route' => route('admin.purchases.index'),
          'active' => request()->routeIs('admin.purchases.*'),
          'icon' => 'purchases text-xl',
          'can' => 'compras',
      ],
      [
          'name' => 'Empleados',
          'route' => route('admin.staff.index'),
          'active' => request()->routeIs('admin.staff.index'),
          'icon' => 'users text-xl',
          'can' => 'empleados',
      ],
      [
          'name' => 'Nomina',
          'route' => route('admin.payroll.index'),
          'active' => request()->routeIs('admin.payroll.index'),
          'icon' => 'money text-xl',
          'can' => 'nomina',
      ],
      [
          'name' => 'Egresos',
          'route' => route('admin.outputs.index'),
          'active' => request()->routeIs('admin.outputs.index'),
          'icon' => 'wallet text-xl',
          'can' => 'egresos',
      ],
      [
          'name' => 'Productos vendidos',
          'route' => route('admin.sold-products.index'),
          'active' => request()->routeIs('admin.sold-products.index'),
          'icon' => 'folder-download text-xl',
          'can' => 'productos vendidos',
      ],
      [
          'name' => 'Ventas diarias',
          'route' => route('admin.daily-sales.index'),
          'active' => request()->routeIs('admin.daily-sales.index'),
          'icon' => 'excel text-xl',
          'can' => 'reporte de ventas diarias',
      ],
  ];

  // Agregar Logs solo para usuarios root
  if (isRoot()) {
      $links[] = [
          'name' => 'Logs',
          'route' => route('admin.logs.index'),
          'active' => request()->routeIs('admin.logs.*'),
          'icon' => 'terminal text-xl',
          'can' => 'dashboard', // Todos tienen acceso a dashboard, pero el isRoot() lo restringe
      ];
  }

?>
<div x-data="{ mobileMenuOpen: false }" @open-mobile-menu.window="mobileMenuOpen = true">

  
  <div x-show="mobileMenuOpen" x-transition:enter="transition ease-in-out duration-300" x-transition:enter-start="opacity-0"
       x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in-out duration-300"
       x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
       class="md:hidden fixed inset-0 z-50 bg-black bg-opacity-50" @click="mobileMenuOpen = false"></div>

  <nav x-show="mobileMenuOpen" x-transition:enter="transition ease-in-out duration-300 transform"
       x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
       x-transition:leave="transition ease-in-out duration-300 transform"
       x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full"
       class="md:hidden fixed inset-y-0 left-0 z-50 w-72 sm:w-80 bg-white shadow-xl">

    <div class="flex h-full flex-col">
      
      <div class="flex items-center justify-between p-4 border-b">
        <h2 class="text-lg font-semibold text-gray-900">Menú</h2>
        <button @click="mobileMenuOpen = false" class="p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100">
          <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      
      <div class="overflow-y-auto flex-1 px-4 py-4">
        <ul class="space-y-2">
          <?php $__currentLoopData = $links; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(isset($link['children']) && is_array($link['children'])): ?>
              <?php
                // Verificar si al menos un hijo tiene permiso (móvil)
                $hasAnyChildPermissionMobile = false;
                foreach($link['children'] as $child) {
                  if(auth()->user()->can('isEnabled', [App\Models\Module::class, $child['can'] ?? $link['can']])) {
                    $hasAnyChildPermissionMobile = true;
                    break;
                  }
                }
              ?>
              <?php if($hasAnyChildPermissionMobile): ?>
                <li x-data="{ open: false }">
                  <div @click.prevent="open = !open" class="flex items-center justify-between px-3 py-2 rounded-md text-sm font-medium cursor-pointer <?php echo e($link['active'] ? 'bg-cyan-50 text-cyan-700' : 'text-gray-700 hover:bg-gray-50'); ?>" title="<?php echo e($link['name']); ?>">
                    <div class="flex items-center">
                      <?php
                        $iconStr = trim($link['icon']);
                        $parts = preg_split('/\s+/', $iconStr);
                        $namePart = $parts[0] ?? '';
                        $rest = implode(' ', array_slice($parts, 1));
                        $isPackage = (Illuminate\Support\Str::contains($iconStr, 'ti-package') || $namePart === 'package');
                      ?>
                      <?php if($isPackage): ?>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 <?php echo e($rest); ?>" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                          <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                          <path d="M12 3l7 4v6l-7 4l-7 -4v-6z" />
                          <path d="M12 7v10" />
                          <path d="M7 7l10 0" />
                        </svg>
                      <?php else: ?>
                        <i class="<?php echo e(Illuminate\Support\Str::startsWith($iconStr, 'ti-') ? 'ti ' . $iconStr : 'ico icon-' . $namePart . ($rest ? ' ' . $rest : '')); ?> mr-3"></i>
                      <?php endif; ?>
                      <span><?php echo e($link['name']); ?></span>
                    </div>
                  </div>
                  <ul x-show="open" x-transition class="pl-6 mt-1 space-y-1">
                    <?php $__currentLoopData = $link['children']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('isEnabled', [App\Models\Module::class, $child['can'] ?? $link['can']])): ?>
                        <li>
                          <a href="<?php echo e($child['route'] ?? '#'); ?>" @click="mobileMenuOpen = false" class="flex items-center px-3 py-2 rounded-md text-sm font-medium <?php echo e(($child['active'] ?? false) ? 'bg-cyan-50 text-cyan-700' : 'text-gray-700 hover:bg-gray-50'); ?>">
                            <?php
                              $cicon = trim($child['icon'] ?? '');
                              $cparts = preg_split('/\s+/', $cicon);
                              $cnamePart = $cparts[0] ?? '';
                              $crest = implode(' ', array_slice($cparts, 1));
                            ?>
                            <?php if($cicon && (Illuminate\Support\Str::contains($cicon, 'ti-package') || $cnamePart === 'package')): ?>
                              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-3 <?php echo e($crest); ?>" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M12 3l7 4v6l-7 4l-7 -4v-6z" />
                                <path d="M12 7v10" />
                                <path d="M7 7l10 0" />
                              </svg>
                            <?php else: ?>
                              <?php if($cicon): ?>
                                <i class="<?php echo e(Illuminate\Support\Str::startsWith($cicon, 'ti-') ? 'ti ' . $cicon : 'ico icon-' . $cnamePart . ($crest ? ' ' . $crest : '')); ?> mr-3"></i>
                              <?php endif; ?>
                            <?php endif; ?>
                            <?php echo e($child['name']); ?>

                          </a>
                        </li>
                      <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </ul>
                </li>
              <?php endif; ?>
            <?php else: ?>
              <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('isEnabled', [App\Models\Module::class, $link['can']])): ?>
                <li>
                  <a href="<?php echo e($link['route']); ?>" @click="mobileMenuOpen = false"
                     class="flex items-center px-3 py-2 rounded-md text-sm font-medium <?php echo e($link['active'] ? 'bg-cyan-50 text-cyan-700' : 'text-gray-700 hover:bg-gray-50'); ?>">
                    <?php
                      $iconStr = trim($link['icon']);
                    ?>
                    <i class="ico <?php echo e($link['icon']); ?> mr-3"></i>
                    <?php echo e($link['name']); ?>

                  </a>
                </li>
              <?php endif; ?>
            <?php endif; ?>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
      </div>
    </div>
  </nav>

  
  <nav class="hidden md:block fixed inset-y-0 z-40 h-screen">

    <div class="<?php echo e(request()->routeIs('admin.quick-sales.create') || request()->routeIs('admin.direct-sale.create') ? 'w-14' : 'w-52 lg:w-60'); ?> flex h-full flex-col border bg-white pt-12 shadow shadow-gray-400 relative">

      <div class="overflow-hidden overflow-y-auto pb-3 pt-4">
        <ul class="space-y-1 lg:space-y-1.5 px-1.5 lg:px-2">
          <?php $__currentLoopData = $links; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(isset($link['children']) && is_array($link['children'])): ?>
              <?php
                // Verificar si al menos un hijo tiene permiso
                $hasAnyChildPermission = false;
                foreach($link['children'] as $child) {
                  if(auth()->user()->can('isEnabled', [App\Models\Module::class, $child['can'] ?? $link['can']])) {
                    $hasAnyChildPermission = true;
                    break;
                  }
                }
              ?>
              <?php if($hasAnyChildPermission): ?>
                <li x-data="{ open: false }" class="relative">
                  <div @click="open = !open" class="flex items-center justify-between px-2 h-8 lg:h-9 cursor-pointer text-sm lg:text-base <?php echo e($link['active'] ? 'text-cyan-400 bg-gray-100 rounded-md shadow-inner shadow-slate-300' : 'text-gray-600 hover:bg-gray-50'); ?>" title="<?php echo e($link['name']); ?>">
                    <div class="inline-flex items-center w-full">
                      <?php
                        $iconStr = trim($link['icon']);
                        $parts = preg_split('/\s+/', $iconStr);
                        $namePart = $parts[0] ?? '';
                        $rest = implode(' ', array_slice($parts, 1));
                      ?>
                      <?php if(Illuminate\Support\Str::contains($iconStr, 'ti-package') || $namePart === 'package'): ?>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 lg:h-5 lg:w-5 <?php echo e($rest); ?> mr-2 lg:mr-4" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                          <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                          <path d="M12 3l7 4v6l-7 4l-7 -4v-6z" />
                          <path d="M12 7v10" />
                          <path d="M7 7l10 0" />
                        </svg>
                      <?php else: ?>
                        <i class="<?php echo e(Illuminate\Support\Str::startsWith($iconStr, 'ti-') ? 'ti ' . $iconStr : 'ico icon-' . $namePart . ($rest ? ' ' . $rest : '')); ?> mr-2 lg:mr-4 text-base lg:text-lg"></i>
                      <?php endif; ?>
                      <?php if(! (request()->routeIs('admin.quick-sales.create') || request()->routeIs('admin.direct-sale.create'))): ?>
                        <span class="whitespace-nowrap"><?php echo e($link['name']); ?></span>
                      <?php endif; ?>
                    </div>
                    <?php if(! (request()->routeIs('admin.quick-sales.create') || request()->routeIs('admin.direct-sale.create'))): ?>
                      <button @click.prevent="open = !open" class="p-1 rounded-md text-gray-500 hover:text-gray-700" @click.stop>
                        <svg x-bind:class="open ? 'transform rotate-90' : ''" class="h-4 w-4 transition-transform" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                      </button>
                    <?php endif; ?>
                  </div>
                  <?php if(! (request()->routeIs('admin.quick-sales.create') || request()->routeIs('admin.direct-sale.create'))): ?>
                    <ul x-show="open" class="mt-0.5 lg:mt-1 pl-4 lg:pl-6 space-y-0.5 lg:space-y-1">
                      <?php $__currentLoopData = $link['children']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('isEnabled', [App\Models\Module::class, $child['can'] ?? $link['can']])): ?>
                          <li>
                            <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.menu.nav-link','data' => ['route' => $child['route'],'name' => $child['name'],'icon' => $child['icon'],'active' => $child['active'] ?? false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('menu.nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['route' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($child['route']),'name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($child['name']),'icon' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($child['icon']),'active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($child['active'] ?? false)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
                          </li>
                        <?php endif; ?>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                  <?php else: ?>
                    
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         @click.away="open = false"
                         class="fixed z-50 ml-14 bg-white border border-gray-200 rounded-md shadow-2xl"
                         style="min-width: 14rem;">
                      <div class="py-1">
                        <?php $__currentLoopData = $link['children']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                          <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('isEnabled', [App\Models\Module::class, $child['can'] ?? $link['can']])): ?>
                            <a href="<?php echo e($child['route'] ?? '#'); ?>"
                               class="group relative flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-cyan-50 hover:text-cyan-700 transition-colors <?php echo e(($child['active'] ?? false) ? 'bg-cyan-50 text-cyan-700' : ''); ?>">
                              <?php
                                $cicon = trim($child['icon'] ?? '');
                                $cparts = preg_split('/\s+/', $cicon);
                                $cnamePart = $cparts[0] ?? '';
                                $crest = implode(' ', array_slice($cparts, 1));
                              ?>
                              <?php if($cicon && (Illuminate\Support\Str::contains($cicon, 'ti-package') || $cnamePart === 'package')): ?>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 <?php echo e($crest); ?>" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                  <path d="M12 3l7 4v6l-7 4l-7 -4v-6z" />
                                  <path d="M12 7v10" />
                                  <path d="M7 7l10 0" />
                                </svg>
                              <?php else: ?>
                                <?php if($cicon): ?>
                                  <i class="<?php echo e(Illuminate\Support\Str::startsWith($cicon, 'ti-') ? 'ti ' . $cicon : 'ico icon-' . $cnamePart . ($crest ? ' ' . $crest : '')); ?> mr-3 text-lg"></i>
                                <?php endif; ?>
                              <?php endif; ?>
                              <span class="font-medium"><?php echo e($child['name']); ?></span>
                            </a>
                          <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                      </div>
                    </div>
                  <?php endif; ?>
                </li>
              <?php endif; ?>
            <?php else: ?>
              <?php if(isset($link['name']) && $link['name'] === 'Remisiones'): ?>
                <li>
                  <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.menu.nav-link','data' => ['route' => $link['route'],'name' => $link['name'],'icon' => $link['icon'],'active' => $link['active']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('menu.nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['route' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($link['route']),'name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($link['name']),'icon' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($link['icon']),'active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($link['active'])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
                </li>
              <?php else: ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('isEnabled', [App\Models\Module::class, $link['can']])): ?>
                  <li>
                    <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.menu.nav-link','data' => ['route' => $link['route'],'name' => $link['name'],'icon' => $link['icon'],'active' => $link['active']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('menu.nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['route' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($link['route']),'name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($link['name']),'icon' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($link['icon']),'active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($link['active'])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
                  </li>
                <?php endif; ?>
              <?php endif; ?>
            <?php endif; ?>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
      </div>
    </div>

  </nav>

  <?php
    // Calcular el left del top bar basado en el ancho del sidebar
    if (request()->routeIs('admin.quick-sales.create') || request()->routeIs('admin.direct-sale.create')) {
      $menuLeftDesktop = '3.5rem'; // 56px - sidebar compacto
    } else {
      $menuLeftDesktop = '13rem'; // 208px en pantallas md (1024px+)
    }
  ?>

  <nav id="menu-top" class="fixed top-0 right-0 z-50 flex h-12 md:h-14 border bg-white shadow" style="left: 0;">

    <div class="flex w-full px-1 md:px-2">

      
      <div class="flex items-center mr-2 md:mr-4 md:hidden">
        <button x-data x-on:click="$dispatch('open-mobile-menu')" class="p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100">
          <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>
      </div>


      
      <div class="flex items-center overflow-x-auto">
        <ul class="flex space-x-0.5 md:space-x-2 min-w-max">
          <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('isEnabled', [App\Models\Module::class, 'facturas'])): ?>
            <li class="text-gray-600 flex-shrink-0">
              <a href="<?php echo e(route('admin.bills.create')); ?>" class="flex flex-col items-center px-1 py-0.5 md:py-1 hover:bg-gray-50 rounded">
                <i class="ico icon-quote text-base md:text-lg lg:text-xl"></i>
                <span class="mt-0.5 md:mt-1 text-[10px] md:text-xs hidden sm:block">Crear factura</span>
                <span class="mt-0.5 md:mt-1 text-[10px] md:text-xs sm:hidden">Factura</span>
              </a>
            </li>
          <?php endif; ?>

          <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('isEnabled', [App\Models\Module::class, 'vender'])): ?>
            <li class="text-gray-600 flex-shrink-0">
              <a href="<?php echo e(route('admin.direct-sale.create')); ?>" class="flex flex-col items-center px-1 py-0.5 md:py-1 hover:bg-gray-50 rounded">
                <i class="ico icon-quote text-base md:text-lg lg:text-xl"></i>
                <span class="mt-0.5 md:mt-1 text-[10px] md:text-xs hidden sm:block">Vender</span>
                <span class="mt-0.5 md:mt-1 text-[10px] md:text-xs sm:hidden">Vender</span>
              </a>
            </li>
          <?php endif; ?>

          <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('isEnabled', [App\Models\Module::class, 'ventas rapidas'])): ?>
            <li class="text-gray-600 flex-shrink-0">
              <a href="<?php echo e(route('admin.quick-sales.create')); ?>" class="flex flex-col items-center px-1 py-0.5 md:py-1 hover:bg-gray-50 rounded">
                <i class="ico icon-new-order text-base md:text-lg lg:text-xl"></i>
                <span class="mt-0.5 md:mt-1 text-[10px] md:text-xs hidden sm:block">Ventas rápidas</span>
                <span class="mt-0.5 md:mt-1 text-[10px] md:text-xs sm:hidden">Rápidas</span>
              </a>
            </li>
          <?php endif; ?>

          <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('isEnabled', [App\Models\Module::class, 'productos'])): ?>
            <li class="text-gray-600 flex-shrink-0">
              <a href="<?php echo e(route('admin.products.index')); ?>" class="flex flex-col items-center px-1 py-0.5 md:py-1 hover:bg-gray-50 rounded">
                <i class="ico icon-inventory text-base md:text-lg lg:text-xl"></i>
                <span class="mt-0.5 md:mt-1 text-[10px] md:text-xs hidden sm:block">Productos</span>
                <span class="mt-0.5 md:mt-1 text-[10px] md:text-xs sm:hidden">Productos</span>
              </a>
            </li>
          <?php endif; ?>

        </ul>
      </div>

      <div class="ml-auto flex h-full items-center space-x-1 md:space-x-2 lg:space-x-3">

        <?php if(hasTerminal()): ?>
          <div class="hidden lg:block text-xs lg:text-sm font-semibold text-slate-600">
            Terminal: <?php echo e(getTerminal()->name); ?>

          </div>

          <div class="flex h-full items-center" title="Abrir caja registradora">
            <button wire:click='openCashRegister'>
              <i class="ico icon-payment text-base md:text-lg lg:text-xl text-green-600"></i>
            </button>
          </div>

          <div class="flex h-full items-center" title="Cierre de caja">
            <button x-data @click="window.livewire.emitTo('admin.cash-closing.create', 'openCreate', <?php echo e(getTerminal()->id); ?>)">
              <i class="ico icon-payment text-base md:text-lg lg:text-xl text-red-600"></i>
            </button>
          </div>
        <?php endif; ?>

        <?php if(App\Services\FactusConfigurationService::isApiEnabled() && isRoot()): ?>
          <div x-data="authenticatedFactus()">
            <button @click="openFactus()" class="flex h-full items-center" title="Abrir Factus">
              <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icons.factus','data' => ['class' => 'h-5 w-5 md:h-6 md:w-6 text-indigo-800','title' => 'Factus activo']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('icons.factus'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'h-5 w-5 md:h-6 md:w-6 text-indigo-800','title' => 'Factus activo']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
            </button>
          </div>
        <?php endif; ?>

        <?php if(App\Services\FactroConfigurationService::isApiEnabled() && isRoot()): ?>
          <div x-data="authenticatedFactro()">
            <button @click="openFactro()" class="flex h-full items-center" title="Abrir Factro">
              <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icons.factro','data' => ['class' => 'h-6 w-6 text-indigo-800','title' => 'Factro activo']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('icons.factro'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'h-6 w-6 text-indigo-800','title' => 'Factro activo']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
            </button>
          </div>
        <?php endif; ?>

        <?php if(isRoot()): ?>
          <div class="flex h-full items-center">
            <i class="ico icon-user-config text-lg md:text-xl lg:text-2xl text-red-700"></i>
          </div>
        <?php endif; ?>

        <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dropdown','data' => ['align' => 'right','width' => '56']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('dropdown'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['align' => 'right','width' => '56']); ?>
           <?php $__env->slot('trigger', null, []); ?> 
            <button
              class="flex items-center whitespace-nowrap text-sm font-medium text-gray-500 transition duration-150 ease-in-out hover:border-gray-300 hover:text-cyan-400 focus:border-gray-300 focus:text-gray-700 focus:outline-none"
              title="Perfil">
              <div class="ml-0.5 md:ml-1 min-h-max min-w-max">
                <img class="h-7 w-7 md:h-8 md:w-8 lg:h-9 lg:w-9 rounded-full" src="<?php echo e(auth()->user()->profile_photo_url); ?>">
              </div>
            </button>
           <?php $__env->endSlot(); ?>

           <?php $__env->slot('content', null, []); ?> 
            <span class="px-2 text-sm font-semibold text-gray-500">
              <?php echo e(Str::limit(Auth::user()->name, 20, '...')); ?>

            </span>
            
              <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('isEnabled', [App\Models\Module::class, 'roles y permisos'])): ?>
                <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dropdown-link','data' => ['href' => route('admin.roles.index'),'class' => 'flex items-center']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('dropdown-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('admin.roles.index')),'class' => 'flex items-center']); ?>
                  <i class="ico icon-user-lock mr-2 text-lg"></i>
                  Roles y permisos
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
              <?php endif; ?>

              <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('isEnabled', [App\Models\Module::class, 'rangos de numeración'])): ?>
                <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dropdown-link','data' => ['href' => route('admin.numbering-ranges.index'),'class' => 'flex items-center']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('dropdown-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('admin.numbering-ranges.index')),'class' => 'flex items-center']); ?>
                  <i class="ico icon-order-2 mr-2 text-lg"></i>
                  Rangos de numeración
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
              <?php endif; ?>

              <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('isEnabled', [App\Models\Module::class, 'impuestos'])): ?>
                <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dropdown-link','data' => ['href' => route('admin.tax-rates.index'),'class' => 'flex items-center']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('dropdown-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('admin.tax-rates.index')),'class' => 'flex items-center']); ?>
                  <i class="ti ti-percentage mr-2 text-lg"></i>
                  Impuestos
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
              <?php endif; ?>

              <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('isEnabled', [App\Models\Module::class, 'terminales'])): ?>
                <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dropdown-link','data' => ['href' => route('admin.terminals.index'),'class' => 'flex items-center']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('dropdown-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('admin.terminals.index')),'class' => 'flex items-center']); ?>
                  <i class="ico icon-terminal mr-2"></i>
                  Terminales
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
              <?php endif; ?>

              <?php if(isRoot() || auth()->user()->can('isEnabled', [App\Models\Module::class, 'administrar empresas'])): ?>
                <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dropdown-link','data' => ['href' => route('admin.tenants.index'),'class' => 'flex items-center']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('dropdown-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('admin.tenants.index')),'class' => 'flex items-center']); ?>
                  <i class="ti ti-building-store mr-2"></i>
                  Administrar Empresas
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dropdown-link','data' => ['href' => route('admin.modules.index'),'class' => 'flex items-center']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('dropdown-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('admin.modules.index')),'class' => 'flex items-center']); ?>
                  <i class="ti ti-layout-dashboard mr-2"></i>
                  Módulos
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dropdown-link','data' => ['href' => route('admin.factus.connection'),'class' => 'flex items-center']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('dropdown-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('admin.factus.connection')),'class' => 'flex items-center']); ?>
                  <i class="ti ti-api mr-2"></i>
                  Factus
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dropdown-link','data' => ['href' => route('admin.factro.connection'),'class' => 'flex items-center']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('dropdown-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('admin.factro.connection')),'class' => 'flex items-center']); ?>
                  <i class="ti ti-api mr-2"></i>
                  Factro
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
              <?php endif; ?>

              <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('isEnabled', [App\Models\Module::class, 'configuraciones'])): ?>
                <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dropdown-link','data' => ['href' => route('admin.companies.settings'),'class' => 'flex items-center']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('dropdown-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('admin.companies.settings')),'class' => 'flex items-center']); ?>
                  <i class="ico icon-settings mr-2"></i>
                  Configuración
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
              <?php endif; ?>

              <form method="POST" action="<?php echo e(route('logout')); ?>">
                <?php echo csrf_field(); ?>
                <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dropdown-link','data' => ['href' => route('logout'),'onclick' => 'event.preventDefault(); this.closest(\'form\').submit();','class' => 'flex items-center']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('dropdown-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('logout')),'onclick' => 'event.preventDefault(); this.closest(\'form\').submit();','class' => 'flex items-center']); ?>
                  <i class="ico icon-logout mr-2"></i>
                  Cerrar sesión
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
              </form>
           <?php $__env->endSlot(); ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
      </div>
    </div>
  </nav>

  <style>
    /* En móvil ocupa todo el ancho (left: 0). En md+ aplicamos el espacio del sidebar. */
    @media (min-width: 768px) {
      #menu-top { left: <?php echo e($menuLeftDesktop); ?>; }
    }
    /* En pantallas grandes (lg+) ajustamos a 15rem (240px) */
    @media (min-width: 1366px) {
      #menu-top {
        left: <?php echo e((request()->routeIs('admin.quick-sales.create') || request()->routeIs('admin.direct-sale.create')) ? '3.5rem' : '15rem'); ?>;
      }
    }
  </style>

  <?php
if (! isset($_instance)) {
    $html = \Livewire\Livewire::mount('admin.cash-closing.create')->html();
} elseif ($_instance->childHasBeenRendered('l3963562044-0')) {
    $componentId = $_instance->getRenderedChildComponentId('l3963562044-0');
    $componentTag = $_instance->getRenderedChildComponentTagName('l3963562044-0');
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild('l3963562044-0');
} else {
    $response = \Livewire\Livewire::mount('admin.cash-closing.create');
    $html = $response->html();
    $_instance->logRenderedChild('l3963562044-0', $response->id(), \Livewire\Livewire::getRootElementTagName($html));
}
echo $html;
?>

</div>
<?php $__env->startPush('js'); ?>
  <script>
    function authenticatedFactus() {
      return {

        async openFactus() {
          response = await this.getTokenFactus();

          if (response.token) {
            params = new URLSearchParams({
              token: response.token,
              redirect_url: response.redirect_url
            }).toString();

            window.open(`${response.domain}/external-authentication?${params}`, '_blank');
          } else {
            alert('Ha ocurrido un error al abrir Factus');
          }

        },

        async getTokenFactus() {
          credentials = await this.$wire.call('getTokenFactus');
          return credentials
        },
      }
    }

    function authenticatedFactro() {
      return {

        async openFactro() {
          response = await this.getTokenFactro();

          if (response.token) {
            params = new URLSearchParams({
              token: response.token,
              redirect_url: response.redirect_url
            }).toString();

            window.open(`${response.domain}/external-authentication?${params}`, '_blank');
          } else {
            alert('Ha ocurrido un error al abrir Factro');
          }

        },

        async getTokenFactro() {
          credentials = await this.$wire.call('getTokenFactro');
          return credentials
        },
      }
    }
  </script>
<?php $__env->stopPush(); ?>
<?php /**PATH C:\Users\USUARIO\Documents\proyecto-pos\app-pos-laravel\resources\views/livewire/admin/menu.blade.php ENDPATH**/ ?>