<div>
    <x-wireui.modal wire:model.defer="openEdit" max-width="6xl">
        <x-wireui.card title="Actualizar producto">

            <x-wireui.errors />

            <div class="">


                <div class="grid grid-cols-2 gap-6">

                    <div class="flex space-x-2 items-end">
                        <div class="w-full">
                            <x-wireui.native-select label="Categoría" placeholder="Selecciona una categoría" wire:model.defer="product.category_id" optionKeyValue :options="$categories" class="min-w-full" />
                        </div>
                        <button wire:click='$emitTo("admin.categories.index", "openCreate", "{{ $this->getName() }}")' class="h-10 w-10 bg-indigo-500 text-white rounded-lg" title="Crear categoría">
                            <i class="ico icon-add"></i>
                        </button>
                    </div>

                    <x-wireui.input label="Nombre" name="name" wire:model.defer="product.name" placeholder="Nombre del producto" />

                </div>

                <div class="grid grid-cols-2 gap-6 mt-6">
                    <x-wireui.input label="Código de barras" name="barcode" wire:model.defer="product.barcode" placeholder="Código de barras"  />
                    <x-wireui.input label="Referencia" name="reference" wire:model.defer="product.reference" placeholder="Referencia del producto"  />
                </div>

                <div class="grid grid-cols-3 gap-6 mt-6">
                  <div class="flex space-x-2 items-end">
                    <div class="flex-1">
                      <x-wireui.input label="Impuestos" :value="$tax_rates->implode('format_rate', ', ')" readonly class="w-full"  />
                    </div>
                        <button wire:click='openTaxRates' class="h-10 w-10 bg-indigo-500 text-white rounded-lg" title="Agregar impuestos">
                            <i class="ico icon-add"></i>
                        </button>
                  </div>
                    <x-wireui.input onlyNumbers label="Costo" name="cost" wire:model.defer="product.cost" placeholder="Costo del producto"  />
                    <x-wireui.input onlyNumbers label="Precio" name="price" wire:model.defer="product.price" placeholder="Precio del producto"  />
                </div>

                @if ($is_inventory_enabled)
                  <div class="grid grid-cols-3 gap-6 mt-6">

                      <x-buttons.switch label="Llevar inventario" wire:model="product.has_inventory" active="Sí" inactive="No" />

                      @if (!$product->has_inventory)

                          <x-buttons.switch label="Manejar presentaciones" wire:model="product.has_presentations" active="Sí" inactive="No" />

                          <!--  
                          <div class="grid {{ $product->has_presentations ? 'grid-cols-1' : 'grid-cols-2' }} gap-6">

                              <x-wireui.input onlyNumbers label="Stock" name="stock" wire:model.defer="product.stock" placeholder="Cantidad de stock"  />

                              @if (!$product->has_presentations)

                                  <x-wireui.input onlyNumbers label="Unidades" name="units" wire:model.defer="units" placeholder="Unidades"  />

                              @endif

                          </div>
                          -->

                      @endif

                  </div>
                @endif

                {{-- Sección de Imagen --}}
                <div class="mt-6 border-t pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Imagen del Producto</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Vista previa de imagen actual --}}
                        <div class="space-y-4">
                            <label class="block text-sm font-medium text-gray-700">Imagen Actual</label>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
                                @if($product->cloudinary_public_id)
                                    <div class="relative">
                                        <img src="{{ $product->image_url }}" 
                                             alt="{{ $product->name }}" 
                                             class="mx-auto h-32 w-32 object-cover rounded-lg">
                                        <button wire:click="removeImage" 
                                                class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600"
                                                title="Eliminar imagen">
                                            <i class="ti ti-x text-sm"></i>
                                        </button>
                                    </div>
                                    <p class="mt-2 text-sm text-gray-500">{{ $product->cloudinary_public_id }}</p>
                                @else
                                    <div class="text-gray-400">
                                        <i class="ti ti-photo text-4xl"></i>
                                        <p class="mt-2">Sin imagen asignada</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Subir nueva imagen --}}
                        <div class="space-y-4">
                            <label class="block text-sm font-medium text-gray-700">Subir Nueva Imagen</label>
                            <div class="space-y-3">
                                <input type="file" 
                                       wire:model="photo" 
                                       accept="image/*"
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Seleccionar imagen</label>
                                
                                @if ($photo)
                                    <div class="border rounded-lg p-3 bg-gray-50">
                                        <p class="text-sm text-gray-600">
                                            <i class="ti ti-file text-green-500"></i>
                                            {{ $photo->getClientOriginalName() }}
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            Tamaño: {{ number_format($photo->getSize() / 1024, 2) }} KB
                                        </p>
                                    </div>
                                @endif

                                <button wire:click="uploadImage" 
                                        :disabled="!$wire.photo || $wire.uploadingImage"
                                        class="w-full bg-green-500 hover:bg-green-600 disabled:bg-gray-300 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                                    <span x-show="!$wire.uploadingImage">
                                        <i class="ti ti-upload mr-2"></i>Subir Imagen
                                    </span>
                                    <span x-show="$wire.uploadingImage">
                                        <i class="ti ti-loader animate-spin mr-2"></i>Subiendo...
                                    </span>
                                </button>

                                <p class="text-xs text-gray-500">
                                    Formatos soportados: JPG, PNG, GIF. Tamaño máximo: 5MB
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            @if (!$product->has_presentations)

                <div class="mt-4 flex justify-between items-end">
                    <x-wireui.input onlyNumbers label="Unidades por producto" name="quantity" wire:model.defer="product.quantity" placeholder="Cantidad"  />
                    <div>
                        <x-wireui.button x-on:click="$wire.emitTo('admin.products.presentations', 'openPresentations', '{{ $this->getName() }}')"  text="Agregar presentación" />
                    </div>
                </div>

                <x-commons.table-responsive class="mt-4 border">

                    <table class="table-sm">
                        <thead >
                            <tr>
                                <th left>
                                    Nombre
                                </th>
                                <th>
                                    Cantidad
                                </th>
                                <th left>
                                    Precio
                                </th>
                                <th>
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($presentations as $key => $item)
                                <tr wire:key="edit-presentation{{ $key }}">
                                    <td left>
                                        {{ $item['name'] }}
                                    </td>
                                    <td>
                                        {{ $item['quantity'] }}
                                    </td>
                                    <td left>
                                        @formatToCop($item['price'])
                                    </td>
                                    <td actions>
                                        <x-buttons.delete wire:click="removePresentation({{ $key }})" />
                                        <x-buttons.edit wire:click="editPresentation({{ $key }})" />
                                    </td>
                                </tr>
                            @empty
                                <x-commons.table-empty text="No se encontraron presentaciones agregadas"/>
                            @endforelse
                        <tbody>
                    </table>
                </x-commons.table-responsive>

            @endif

            <x-slot:footer>
                <div class="flex justify-between items-center">
                    <div class="flex flex-col space-y-2">
                        <x-buttons.switch wire:model="product.top" active="destacado" inactive="no destacado"/>
                        <x-buttons.switch wire:model="product.status" />
                    </div>

                    <div class="text-right space-x-3">
                        <x-wireui.button secondary x-on:click="show=false" text="Cerrar" />
                        <x-wireui.button wire:click="update" text="Actualizar" load textLoad="Actualizando.." />
                    </div>
                </div>
            </x-slot:footer>
        </x-wireui.card>
    </x-wireui.modal>
</div>
