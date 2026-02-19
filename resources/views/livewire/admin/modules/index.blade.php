<div class="container pt-8 relative">
    <x-wireui.card title="Módulos" cardClasses="max-w-6xl mx-auto">

        <x-slot:header>
            <div x-data class="px-4 py-1 flex justify-between items-center border-b">
                <h3 class="font-semibold whitespace-normal text-lg">Módulos</h3>
                <div class="flex items-center space-x-2">
                    @if (isRoot() || auth()->user()->can('isEnabled', [\App\Models\Module::class, 'administrar empresas']))
                        <x-wireui.button href="{{ route('admin.tenants.index') }}" icon="office-building" text="Administrar Empresas" sm secondary />
                    @endif
                    <x-wireui.button x-on:click="$wire.emit('enableAllModules')" text="Habilitar todos los módulos" sm />
                </div>
            </div>
        </x-slot:header>

        <ul class="grid grid-cols-4">
            @foreach ($modules as $item)
                <li>
                    <a class="text-center py-1 cursor-pointer select-none h-20 flex items-center justify-center border {{ $item->is_enabled ? 'font-semibold' : 'line-through bg-slate-100' }}"
                        wire:click="$emit('togglePermission', '{{$item->id}}', {{ (int)$item->is_enabled }}, {{ (int)$item->is_functionality }})">
                        {{ $item->name }}
                    </a>
                </li>
            @endforeach
        </ul>


    </x-wireui.card>

    <x-wireui.card title="Funciones" cardClasses="max-w-6xl mx-auto mt-4">

        <x-slot:header>
            <div class="px-4 py-1 flex justify-between items-center border-b">
                <h3 class="font-semibold whitespace-normal text-lg">Funcionalidades</h3>
            </div>
        </x-slot:header>

        <ul class="grid grid-cols-4">
            @foreach ($functionalities as $item)
                <li>
                    @if ($item->is_enabled)
                        {{-- Si está habilitado, mostrar como navegable --}}
                        @php
                            $route = '';
                            switch(strtolower($item->name)) {
                                case 'vender':
                                    $route = route('admin.direct-sale.create');
                                    break;
                                case 'ventas rapidas':
                                    $route = route('admin.quick-sales.create');
                                    break;
                                case 'inventario':
                                    $route = '#'; // Agregar ruta cuando esté disponible
                                    break;
                                default:
                                    $route = '#';
                            }
                        @endphp
                        
                        <div class="relative group">
                            {{-- Enlace principal para navegar --}}
                            <a href="{{ $route }}" 
                               class="text-center py-1 cursor-pointer select-none h-20 flex items-center justify-center border font-semibold bg-gradient-to-br from-cyan-50 to-blue-50 hover:from-cyan-100 hover:to-blue-100 transition-all duration-200 shadow-sm hover:shadow-md">
                                <div class="flex flex-col items-center space-y-1">
                                    @if (strtolower($item->name) == 'vender')
                                        <i class="ico icon-quote text-2xl text-cyan-600"></i>
                                    @elseif (strtolower($item->name) == 'ventas rapidas')
                                        <i class="ico icon-new-order text-2xl text-blue-600"></i>
                                    @elseif (strtolower($item->name) == 'inventario')
                                        <i class="ico icon-inventory text-2xl text-green-600"></i>
                                    @endif
                                    <span class="text-sm">{{ $item->name }}</span>
                                </div>
                            </a>
                            
                            {{-- Botón pequeño para configurar (toggle) --}}
                            <button wire:click="$emit('togglePermission', '{{$item->id}}', '{{ $item->is_enabled }}', '{{ $item->is_functionality }}')"
                                    class="absolute top-1 right-1 w-6 h-6 bg-gray-200 hover:bg-gray-300 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200"
                                    title="Configurar módulo">
                                <i class="ico icon-settings text-xs text-gray-600"></i>
                            </button>
                        </div>
                    @else
                        {{-- Si está deshabilitado, mostrar como toggle --}}
                        <a class="text-center py-1 cursor-pointer select-none h-20 flex items-center justify-center border line-through bg-slate-100"
                            wire:click="$emit('togglePermission', '{{$item->id}}', '{{ $item->is_enabled }}', '{{ $item->is_functionality }}')">
                            <div class="flex flex-col items-center space-y-1 opacity-50">
                                @if (strtolower($item->name) == 'vender')
                                    <i class="ico icon-quote text-2xl text-gray-400"></i>
                                @elseif (strtolower($item->name) == 'ventas rapidas')
                                    <i class="ico icon-new-order text-2xl text-gray-400"></i>
                                @elseif (strtolower($item->name) == 'inventario')
                                    <i class="ico icon-inventory text-2xl text-gray-400"></i>
                                @endif
                                <span class="text-sm">{{ $item->name }}</span>
                            </div>
                        </a>
                    @endif
                </li>
            @endforeach
        </ul>


    </x-wireui.card>
    <x-loads.panel-fixed wire:loading wire:target='togglePermission'  text="cargando" class="z-50"/>
</div>

@push('js')

    <script>
        document.addEventListener('livewire:load', function () {

            Livewire.on('togglePermission', (id, is_enabled, is_functionality) => {
                let text1 = Boolean(is_functionality) ? 'esta funcionalidad' : 'este módulo'
                let text2 = Boolean(is_enabled) ? `¿Quieres desactivar ${text1}?` : `¿Quieres activar ${text1}?`;

                Swal.fire({
                    title: '¿Estas seguro?',
                    text: text2,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, aceptar',
                    cancelButtonText: 'Cancelar',
                    showLoaderOnConfirm: true,
                    preConfirm: function() {
                        return @this.togglePermission(id);
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                });
            });

            Livewire.on('enableAllModules', () => {
                Swal.fire({
                    title: '¿Estas seguro?',
                    text: '¿Quieres habilitar todos los módulos?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, aceptar',
                    cancelButtonText: 'Cancelar',
                    showLoaderOnConfirm: true,
                    preConfirm: function() {
                        return @this.enableAllModules();
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                });
            });
        });
    </script>
@endpush

