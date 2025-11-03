@php
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

@endphp
<div x-data="{ mobileMenuOpen: false }" @open-mobile-menu.window="mobileMenuOpen = true">

  {{-- Sidebar Mobile --}}
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
      {{-- Header móvil --}}
      <div class="flex items-center justify-between p-4 border-b">
        <h2 class="text-lg font-semibold text-gray-900">Menú</h2>
        <button @click="mobileMenuOpen = false" class="p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100">
          <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      {{-- Links móvil --}}
      <div class="overflow-y-auto flex-1 px-4 py-4">
        <ul class="space-y-2">
          @foreach ($links as $link)
            @if(isset($link['children']) && is_array($link['children']))
              @php
                // Verificar si al menos un hijo tiene permiso (móvil)
                $hasAnyChildPermissionMobile = false;
                foreach($link['children'] as $child) {
                  if(auth()->user()->can('isEnabled', [App\Models\Module::class, $child['can'] ?? $link['can']])) {
                    $hasAnyChildPermissionMobile = true;
                    break;
                  }
                }
              @endphp
              @if($hasAnyChildPermissionMobile)
                <li x-data="{ open: false }">
                  <div @click.prevent="open = !open" class="flex items-center justify-between px-3 py-2 rounded-md text-sm font-medium cursor-pointer {{ $link['active'] ? 'bg-cyan-50 text-cyan-700' : 'text-gray-700 hover:bg-gray-50' }}" title="{{ $link['name'] }}">
                    <div class="flex items-center">
                      @php
                        $iconStr = trim($link['icon']);
                        $parts = preg_split('/\s+/', $iconStr);
                        $namePart = $parts[0] ?? '';
                        $rest = implode(' ', array_slice($parts, 1));
                        $isPackage = (Illuminate\Support\Str::contains($iconStr, 'ti-package') || $namePart === 'package');
                      @endphp
                      @if($isPackage)
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 {{ $rest }}" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                          <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                          <path d="M12 3l7 4v6l-7 4l-7 -4v-6z" />
                          <path d="M12 7v10" />
                          <path d="M7 7l10 0" />
                        </svg>
                      @else
                        <i class="{{ Illuminate\Support\Str::startsWith($iconStr, 'ti-') ? 'ti ' . $iconStr : 'ico icon-' . $namePart . ($rest ? ' ' . $rest : '') }} mr-3"></i>
                      @endif
                      <span>{{ $link['name'] }}</span>
                    </div>
                  </div>
                  <ul x-show="open" x-transition class="pl-6 mt-1 space-y-1">
                    @foreach($link['children'] as $child)
                      @can('isEnabled', [App\Models\Module::class, $child['can'] ?? $link['can']])
                        <li>
                          <a href="{{ $child['route'] ?? '#' }}" @click="mobileMenuOpen = false" class="flex items-center px-3 py-2 rounded-md text-sm font-medium {{ ($child['active'] ?? false) ? 'bg-cyan-50 text-cyan-700' : 'text-gray-700 hover:bg-gray-50' }}">
                            @php
                              $cicon = trim($child['icon'] ?? '');
                              $cparts = preg_split('/\s+/', $cicon);
                              $cnamePart = $cparts[0] ?? '';
                              $crest = implode(' ', array_slice($cparts, 1));
                            @endphp
                            @if($cicon && (Illuminate\Support\Str::contains($cicon, 'ti-package') || $cnamePart === 'package'))
                              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-3 {{ $crest }}" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M12 3l7 4v6l-7 4l-7 -4v-6z" />
                                <path d="M12 7v10" />
                                <path d="M7 7l10 0" />
                              </svg>
                            @else
                              @if($cicon)
                                <i class="{{ Illuminate\Support\Str::startsWith($cicon, 'ti-') ? 'ti ' . $cicon : 'ico icon-' . $cnamePart . ($crest ? ' ' . $crest : '') }} mr-3"></i>
                              @endif
                            @endif
                            {{ $child['name'] }}
                          </a>
                        </li>
                      @endcan
                    @endforeach
                  </ul>
                </li>
              @endif
            @else
              @can('isEnabled', [App\Models\Module::class, $link['can']])
                <li>
                  <a href="{{ $link['route'] }}" @click="mobileMenuOpen = false"
                     class="flex items-center px-3 py-2 rounded-md text-sm font-medium {{ $link['active'] ? 'bg-cyan-50 text-cyan-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    @php
                      $iconStr = trim($link['icon']);
                    @endphp
                    <i class="ico {{ $link['icon'] }} mr-3"></i>
                    {{ $link['name'] }}
                  </a>
                </li>
              @endcan
            @endif
          @endforeach
        </ul>
      </div>
    </div>
  </nav>

  {{-- Sidebar Desktop --}}
  <nav class="hidden md:block fixed inset-y-0 z-40 h-screen">

    <div class="{{ request()->routeIs('admin.quick-sales.create') || request()->routeIs('admin.direct-sale.create') ? 'w-14' : 'w-52 lg:w-60' }} flex h-full flex-col border bg-white pt-12 shadow shadow-gray-400 relative">

      <div class="overflow-hidden overflow-y-auto pb-3 pt-4">
        <ul class="space-y-1 lg:space-y-1.5 px-1.5 lg:px-2">
          @foreach ($links as $link)
            @if(isset($link['children']) && is_array($link['children']))
              @php
                // Verificar si al menos un hijo tiene permiso
                $hasAnyChildPermission = false;
                foreach($link['children'] as $child) {
                  if(auth()->user()->can('isEnabled', [App\Models\Module::class, $child['can'] ?? $link['can']])) {
                    $hasAnyChildPermission = true;
                    break;
                  }
                }
              @endphp
              @if($hasAnyChildPermission)
                <li x-data="{ open: false }" class="relative">
                  <div @click="open = !open" class="flex items-center justify-between px-2 h-8 lg:h-9 cursor-pointer text-sm lg:text-base {{ $link['active'] ? 'text-cyan-400 bg-gray-100 rounded-md shadow-inner shadow-slate-300' : 'text-gray-600 hover:bg-gray-50' }}" title="{{ $link['name'] }}">
                    <div class="inline-flex items-center w-full">
                      @php
                        $iconStr = trim($link['icon']);
                        $parts = preg_split('/\s+/', $iconStr);
                        $namePart = $parts[0] ?? '';
                        $rest = implode(' ', array_slice($parts, 1));
                      @endphp
                      @if(Illuminate\Support\Str::contains($iconStr, 'ti-package') || $namePart === 'package')
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 lg:h-5 lg:w-5 {{ $rest }} mr-2 lg:mr-4" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                          <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                          <path d="M12 3l7 4v6l-7 4l-7 -4v-6z" />
                          <path d="M12 7v10" />
                          <path d="M7 7l10 0" />
                        </svg>
                      @else
                        <i class="{{ Illuminate\Support\Str::startsWith($iconStr, 'ti-') ? 'ti ' . $iconStr : 'ico icon-' . $namePart . ($rest ? ' ' . $rest : '') }} mr-2 lg:mr-4 text-base lg:text-lg"></i>
                      @endif
                      @if (! (request()->routeIs('admin.quick-sales.create') || request()->routeIs('admin.direct-sale.create')))
                        <span class="whitespace-nowrap">{{ $link['name'] }}</span>
                      @endif
                    </div>
                    @if (! (request()->routeIs('admin.quick-sales.create') || request()->routeIs('admin.direct-sale.create')))
                      <button @click.prevent="open = !open" class="p-1 rounded-md text-gray-500 hover:text-gray-700" @click.stop>
                        <svg x-bind:class="open ? 'transform rotate-90' : ''" class="h-4 w-4 transition-transform" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                      </button>
                    @endif
                  </div>
                  @if (! (request()->routeIs('admin.quick-sales.create') || request()->routeIs('admin.direct-sale.create')))
                    <ul x-show="open" class="mt-0.5 lg:mt-1 pl-4 lg:pl-6 space-y-0.5 lg:space-y-1">
                      @foreach($link['children'] as $child)
                        @can('isEnabled', [App\Models\Module::class, $child['can'] ?? $link['can']])
                          <li>
                            <x-menu.nav-link :route="$child['route']" :name="$child['name']" :icon="$child['icon']" :active="$child['active'] ?? false" />
                          </li>
                        @endcan
                      @endforeach
                    </ul>
                  @else
                    {{-- En modo compacto, mostrar dropdown con submenús --}}
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
                        @foreach($link['children'] as $child)
                          @can('isEnabled', [App\Models\Module::class, $child['can'] ?? $link['can']])
                            <a href="{{ $child['route'] ?? '#' }}"
                               class="group relative flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-cyan-50 hover:text-cyan-700 transition-colors {{ ($child['active'] ?? false) ? 'bg-cyan-50 text-cyan-700' : '' }}">
                              @php
                                $cicon = trim($child['icon'] ?? '');
                                $cparts = preg_split('/\s+/', $cicon);
                                $cnamePart = $cparts[0] ?? '';
                                $crest = implode(' ', array_slice($cparts, 1));
                              @endphp
                              @if($cicon && (Illuminate\Support\Str::contains($cicon, 'ti-package') || $cnamePart === 'package'))
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 {{ $crest }}" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                  <path d="M12 3l7 4v6l-7 4l-7 -4v-6z" />
                                  <path d="M12 7v10" />
                                  <path d="M7 7l10 0" />
                                </svg>
                              @else
                                @if($cicon)
                                  <i class="{{ Illuminate\Support\Str::startsWith($cicon, 'ti-') ? 'ti ' . $cicon : 'ico icon-' . $cnamePart . ($crest ? ' ' . $crest : '') }} mr-3 text-lg"></i>
                                @endif
                              @endif
                              <span class="font-medium">{{ $child['name'] }}</span>
                            </a>
                          @endcan
                        @endforeach
                      </div>
                    </div>
                  @endif
                </li>
              @endif
            @else
              @if(isset($link['name']) && $link['name'] === 'Remisiones')
                <li>
                  <x-menu.nav-link :route="$link['route']" :name="$link['name']" :icon="$link['icon']" :active="$link['active']" />
                </li>
              @else
                @can('isEnabled', [App\Models\Module::class, $link['can']])
                  <li>
                    <x-menu.nav-link :route="$link['route']" :name="$link['name']" :icon="$link['icon']" :active="$link['active']" />
                  </li>
                @endcan
              @endif
            @endif
          @endforeach
        </ul>
      </div>
    </div>

  </nav>

  @php
    // Calcular el left del top bar basado en el ancho del sidebar
    if (request()->routeIs('admin.quick-sales.create') || request()->routeIs('admin.direct-sale.create')) {
      $menuLeftDesktop = '3.5rem'; // 56px - sidebar compacto
    } else {
      $menuLeftDesktop = '13rem'; // 208px en pantallas md (1024px+)
    }
  @endphp

  <nav id="menu-top" class="fixed top-0 right-0 z-50 flex h-12 md:h-14 border bg-white shadow" style="left: 0;">

    <div class="flex w-full px-1 md:px-2">

      {{-- Botón hamburguesa para mobile --}}
      <div class="flex items-center mr-2 md:mr-4 md:hidden">
        <button x-data x-on:click="$dispatch('open-mobile-menu')" class="p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100">
          <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>
      </div>


      {{-- Accesos directos --}}
      <div class="flex items-center overflow-x-auto">
        <ul class="flex space-x-0.5 md:space-x-2 min-w-max">
          @can('isEnabled', [App\Models\Module::class, 'facturas'])
            <li class="text-gray-600 flex-shrink-0">
              <a href="{{ route('admin.bills.create') }}" class="flex flex-col items-center px-1 py-0.5 md:py-1 hover:bg-gray-50 rounded">
                <i class="ico icon-quote text-base md:text-lg lg:text-xl"></i>
                <span class="mt-0.5 md:mt-1 text-[10px] md:text-xs hidden sm:block">Crear factura</span>
                <span class="mt-0.5 md:mt-1 text-[10px] md:text-xs sm:hidden">Factura</span>
              </a>
            </li>
          @endcan

          @can('isEnabled', [App\Models\Module::class, 'vender'])
            <li class="text-gray-600 flex-shrink-0">
              <a href="{{ route('admin.direct-sale.create') }}" class="flex flex-col items-center px-1 py-0.5 md:py-1 hover:bg-gray-50 rounded">
                <i class="ico icon-quote text-base md:text-lg lg:text-xl"></i>
                <span class="mt-0.5 md:mt-1 text-[10px] md:text-xs hidden sm:block">Vender</span>
                <span class="mt-0.5 md:mt-1 text-[10px] md:text-xs sm:hidden">Vender</span>
              </a>
            </li>
          @endcan

          @can('isEnabled', [App\Models\Module::class, 'ventas rapidas'])
            <li class="text-gray-600 flex-shrink-0">
              <a href="{{ route('admin.quick-sales.create') }}" class="flex flex-col items-center px-1 py-0.5 md:py-1 hover:bg-gray-50 rounded">
                <i class="ico icon-new-order text-base md:text-lg lg:text-xl"></i>
                <span class="mt-0.5 md:mt-1 text-[10px] md:text-xs hidden sm:block">Ventas rápidas</span>
                <span class="mt-0.5 md:mt-1 text-[10px] md:text-xs sm:hidden">Rápidas</span>
              </a>
            </li>
          @endcan

          @can('isEnabled', [App\Models\Module::class, 'productos'])
            <li class="text-gray-600 flex-shrink-0">
              <a href="{{ route('admin.products.index') }}" class="flex flex-col items-center px-1 py-0.5 md:py-1 hover:bg-gray-50 rounded">
                <i class="ico icon-inventory text-base md:text-lg lg:text-xl"></i>
                <span class="mt-0.5 md:mt-1 text-[10px] md:text-xs hidden sm:block">Productos</span>
                <span class="mt-0.5 md:mt-1 text-[10px] md:text-xs sm:hidden">Productos</span>
              </a>
            </li>
          @endcan

        </ul>
      </div>

      <div class="ml-auto flex h-full items-center space-x-1 md:space-x-2 lg:space-x-3">

        @if (hasTerminal())
          <div class="hidden lg:block text-xs lg:text-sm font-semibold text-slate-600">
            Terminal: {{ getTerminal()->name }}
          </div>

          @if (!hasTerminalOpen())
            <div class="flex h-full items-center" title="Abrir caja registradora">
              <button wire:click='openCashRegister'>
                <i class="ico icon-payment text-base md:text-lg lg:text-xl text-green-600"></i>
              </button>
            </div>
          @endif

          @if (hasTerminalOpen())
            <div class="flex h-full items-center" title="Cierre de caja">
              <button x-data @click="window.livewire.emitTo('admin.cash-closing.create', 'openCreate', {{ getTerminal()->id }})">
                <i class="ico icon-payment text-base md:text-lg lg:text-xl text-red-600"></i>
              </button>
            </div>
          @endif

        @endif

        @if (App\Services\FactusConfigurationService::isApiEnabled() && isRoot())
          <div x-data="authenticatedFactus()">
            <button @click="openFactus()" class="flex h-full items-center" title="Abrir Factus">
              <x-icons.factus class="h-5 w-5 md:h-6 md:w-6 text-indigo-800" title="Factus activo" />
            </button>
          </div>
        @endif

        @if (App\Services\FactroConfigurationService::isApiEnabled() && isRoot())
          <div x-data="authenticatedFactro()">
            <button @click="openFactro()" class="flex h-full items-center" title="Abrir Factro">
              <x-icons.factro class="h-6 w-6 text-indigo-800" title="Factro activo" />
            </button>
          </div>
        @endif

        @if (isRoot())
          <div class="flex h-full items-center">
            <i class="ico icon-user-config text-lg md:text-xl lg:text-2xl text-red-700"></i>
          </div>
        @endif

        <x-dropdown align="right" width="56">
          <x-slot name="trigger">
            <button
              class="flex items-center whitespace-nowrap text-sm font-medium text-gray-500 transition duration-150 ease-in-out hover:border-gray-300 hover:text-cyan-400 focus:border-gray-300 focus:text-gray-700 focus:outline-none"
              title="Perfil">
              <div class="ml-0.5 md:ml-1 min-h-max min-w-max">
                <img class="h-7 w-7 md:h-8 md:w-8 lg:h-9 lg:w-9 rounded-full" src="{{ auth()->user()->profile_photo_url }}">
              </div>
            </button>
          </x-slot>

          <x-slot name="content">
            <span class="px-2 text-sm font-semibold text-gray-500">
              {{ Str::limit(Auth::user()->name, 20, '...') }}
            </span>

              @can('isEnabled', [App\Models\Module::class, 'roles y permisos'])
                <x-dropdown-link :href="route('admin.roles.index')" class="flex items-center">
                  <i class="ico icon-user-lock mr-2 text-lg"></i>
                  Roles y permisos
                </x-dropdown-link>
              @endcan

              @can('isEnabled', [App\Models\Module::class, 'rangos de numeración'])
                <x-dropdown-link :href="route('admin.numbering-ranges.index')" class="flex items-center">
                  <i class="ico icon-order-2 mr-2 text-lg"></i>
                  Rangos de numeración
                </x-dropdown-link>
              @endcan

              @can('isEnabled', [App\Models\Module::class, 'impuestos'])
                <x-dropdown-link :href="route('admin.tax-rates.index')" class="flex items-center">
                  <i class="ti ti-percentage mr-2 text-lg"></i>
                  Impuestos
                </x-dropdown-link>
              @endcan

              @can('isEnabled', [App\Models\Module::class, 'terminales'])
                <x-dropdown-link :href="route('admin.terminals.index')" class="flex items-center">
                  <i class="ico icon-terminal mr-2"></i>
                  Terminales
                </x-dropdown-link>
              @endcan

              @if (isRoot() || auth()->user()->can('isEnabled', [App\Models\Module::class, 'administrar empresas']))
                <x-dropdown-link :href="route('admin.tenants.index')" class="flex items-center">
                  <i class="ti ti-building-store mr-2"></i>
                  Administrar Empresas
                </x-dropdown-link>
                <x-dropdown-link :href="route('admin.modules.index')" class="flex items-center">
                  <i class="ti ti-layout-dashboard mr-2"></i>
                  Módulos
                </x-dropdown-link>
                <x-dropdown-link :href="route('admin.factus.connection')" class="flex items-center">
                  <i class="ti ti-api mr-2"></i>
                  Factus
                </x-dropdown-link>
                <x-dropdown-link :href="route('admin.factro.connection')" class="flex items-center">
                  <i class="ti ti-api mr-2"></i>
                  Factro
                </x-dropdown-link>
              @endif

              @can('isEnabled', [App\Models\Module::class, 'configuraciones'])
                <x-dropdown-link :href="route('admin.companies.settings')" class="flex items-center">
                  <i class="ico icon-settings mr-2"></i>
                  Configuración
                </x-dropdown-link>
              @endcan

              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="flex items-center">
                  <i class="ico icon-logout mr-2"></i>
                  Cerrar sesión
                </x-dropdown-link>
              </form>
          </x-slot>
        </x-dropdown>
      </div>
    </div>
  </nav>

  <style>
    /* En móvil ocupa todo el ancho (left: 0). En md+ aplicamos el espacio del sidebar. */
    @media (min-width: 768px) {
      #menu-top { left: {{ $menuLeftDesktop }}; }
    }
    /* En pantallas grandes (lg+) ajustamos a 15rem (240px) */
    @media (min-width: 1366px) {
      #menu-top {
        left: {{ (request()->routeIs('admin.quick-sales.create') || request()->routeIs('admin.direct-sale.create')) ? '3.5rem' : '15rem' }};
      }
    }
  </style>

  @livewire('admin.cash-closing.create')

</div>
@push('js')
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
@endpush
